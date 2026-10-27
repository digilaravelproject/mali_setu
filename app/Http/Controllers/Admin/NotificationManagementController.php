<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationAction;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\FirebaseCredentialService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationManagementController extends Controller
{
    public function __construct(
        private readonly FirebaseCredentialService $credentials,
        private readonly NotificationService $notifications,
    ) {}

    public function index(): View
    {
        NotificationAction::ensureDefaults();

        return view('admin.notifications.index', [
            'actions' => NotificationAction::query()->orderBy('name')->paginate(8)->withQueryString(),
            'firebase' => $this->credentials->status(),
            'pushEnabled' => SystemSetting::bool('push_notifications_enabled', true),
            'usersWithTokens' => User::query()->whereNotNull('fcm_token')->count(),
        ]);
    }

    public function create(): View
    {
        return view('admin.notifications.create', ['action' => new NotificationAction]);
    }

    public function store(Request $request): RedirectResponse
    {
        $action = NotificationAction::query()->create($this->validatedAction($request));

        return redirect()->route('admin.notifications.edit', $action)->with('success', 'Notification action created.');
    }

    public function edit(NotificationAction $notificationAction): View
    {
        return view('admin.notifications.edit', ['action' => $notificationAction]);
    }

    public function update(Request $request, NotificationAction $notificationAction): RedirectResponse
    {
        $notificationAction->update($this->validatedAction($request, $notificationAction));

        return back()->with('success', 'Notification action updated.');
    }

    public function destroy(NotificationAction $notificationAction): RedirectResponse
    {
        if ($notificationAction->isSystemAction()) {
            return back()->with('error', 'Built-in actions cannot be deleted. Disable the action instead.');
        }

        $notificationAction->delete();

        return redirect()->route('admin.notifications.index')->with('success', 'Notification action deleted.');
    }

    public function updateFirebase(Request $request): RedirectResponse
    {
        $request->validate(['credentials' => ['required', 'file', 'mimes:json,txt', 'max:1024']]);
        $summary = $this->credentials->store($request->file('credentials'));

        return back()->with('success', "Firebase credentials updated for {$summary['project_id']}.");
    }

    public function updateGlobalSettings(Request $request): RedirectResponse
    {
        SystemSetting::setValue(
            'push_notifications_enabled',
            $request->boolean('push_notifications_enabled') ? '1' : '0',
            'Master switch for Firebase push notifications'
        );

        return back()->with('success', 'Push notification settings updated.');
    }

    public function sendForm(?NotificationAction $notificationAction = null): View
    {
        NotificationAction::ensureDefaults();

        return view('admin.notifications.send', [
            'actions' => NotificationAction::query()->where('is_enabled', true)->orderBy('name')->get(),
            'selectedAction' => $notificationAction,
            'users' => User::query()->whereNotNull('fcm_token')->orderBy('name')->get(['id', 'name', 'email', 'phone']),
        ]);
    }

    public function send(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'notification_action_id' => ['required', 'exists:notification_actions,id'],
            'audience' => ['required', 'in:user,all'],
            'user_id' => ['nullable', 'required_if:audience,user', 'exists:users,id'],
            'data' => ['nullable', 'json'],
        ]);

        $action = NotificationAction::query()->findOrFail($validated['notification_action_id']);
        if (! $action->is_enabled) {
            return back()->withErrors(['notification_action_id' => 'This notification action is disabled.'])->withInput();
        }

        $userIds = $validated['audience'] === 'all'
            ? User::query()->whereNotNull('fcm_token')->pluck('id')->all()
            : [(int) $validated['user_id']];

        if (! $userIds) {
            return back()->withErrors(['audience' => 'No users with FCM tokens were found.'])->withInput();
        }

        $data = json_decode($validated['data'] ?? '{}', true) ?: [];
        $sent = count($this->notifications->triggerAction($action->key, $userIds, $data));

        return redirect()->route('admin.notifications.index')->with('success', "Notification processed for {$sent} user(s).");
    }

    private function validatedAction(Request $request, ?NotificationAction $action = null): array
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9_]+$/', 'unique:notification_actions,key,'.($action?->id ?? 'NULL')],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'title_template' => ['required', 'string', 'max:255'],
            'body_template' => ['required', 'string', 'max:2000'],
            'action_url' => ['nullable', 'string', 'max:255'],
            'available_variables_text' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['available_variables'] = collect(explode(',', $validated['available_variables_text'] ?? ''))
            ->map(fn ($item) => trim($item))->filter()->values()->all();
        $validated['is_enabled'] = $request->boolean('is_enabled');
        $validated['push_enabled'] = $request->boolean('push_enabled');
        unset($validated['available_variables_text']);

        return $validated;
    }
}
