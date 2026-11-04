<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Webinar;
use App\Models\BusinessPlan;
use App\Models\MatrimonyPlan;
use App\Services\AgoraTokenService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\WebinarComment;

class WebinarManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Webinar::withCount('registrations');
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('speaker_name', 'like', "%{$search}%"));
        }
        if ($request->tab === 'upcoming') $query->where('starts_at', '>', now());
        if ($request->tab === 'live') $query->where('starts_at', '<=', now())->whereRaw('DATE_ADD(starts_at, INTERVAL duration_minutes MINUTE) >= ?', [now()]);
        if ($request->tab === 'completed') $query->whereRaw('DATE_ADD(starts_at, INTERVAL duration_minutes MINUTE) < ?', [now()]);

        $webinars = $query->latest('starts_at')->paginate(15)->withQueryString();
        $counts = [
            'all' => Webinar::count(),
            'upcoming' => Webinar::where('starts_at', '>', now())->count(),
            'live' => Webinar::where('starts_at', '<=', now())->whereRaw('DATE_ADD(starts_at, INTERVAL duration_minutes MINUTE) >= ?', [now()])->count(),
            'completed' => Webinar::whereRaw('DATE_ADD(starts_at, INTERVAL duration_minutes MINUTE) < ?', [now()])->count(),
        ];
        return view('admin.webinars.index', compact('webinars', 'counts'));
    }

    public function create()
    {
        return view('admin.webinars.form', [
            'webinar' => new Webinar,
            'businessPlans' => BusinessPlan::where('active', true)->get(),
            'matrimonyPlans' => MatrimonyPlan::where('active', true)->get(),
        ]);
    }

    public function edit(Webinar $webinar)
    {
        return view('admin.webinars.form', [
            'webinar' => $webinar,
            'businessPlans' => BusinessPlan::where('active', true)->get(),
            'matrimonyPlans' => MatrimonyPlan::where('active', true)->get(),
        ]);
    }

    public function registrations(Webinar $webinar)
    {
        $registrations = $webinar->registrations()
            ->with('user')
            ->latest()
            ->paginate(25);

        return view('admin.webinars.registrations', compact('webinar', 'registrations'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['category'] = $data['categories'][0];
        $data['require_registration'] = $request->boolean('require_registration');
        $data['allow_qa'] = $request->boolean('allow_qa');
        $data['allow_recording'] = $request->boolean('allow_recording');
        $data['plan_ids'] = $data['access_type'] === 'specific_plans' ? ($data['plan_ids'] ?? []) : null;
        $data['starts_at'] = Carbon::createFromFormat('Y-m-d H:i', $data['webinar_date'].' '.$data['webinar_time'], config('app.webinar_timezone'));
        unset($data['webinar_date'], $data['webinar_time']);
        if ($request->hasFile('banner')) $data['banner_path'] = $request->file('banner')->store('webinars', 'public');
        $data['created_by'] = $request->user()->id;
        $data['agora_channel'] = 'webinar_'.Str::uuid();
        $webinar = Webinar::create($data + ['agora_link' => 'pending']);
        $webinar->update(['agora_link' => route('webinars.join', $webinar)]);
        return redirect()->route('admin.webinars.index')->with('success', 'Webinar created with Agora channel and join link.');
    }

    public function update(Request $request, Webinar $webinar)
    {
        $data = $this->validated($request);
        $data['category'] = $data['categories'][0];
        $data['require_registration'] = $request->boolean('require_registration');
        $data['allow_qa'] = $request->boolean('allow_qa');
        $data['allow_recording'] = $request->boolean('allow_recording');
        $data['plan_ids'] = $data['access_type'] === 'specific_plans' ? ($data['plan_ids'] ?? []) : null;
        $data['starts_at'] = Carbon::createFromFormat('Y-m-d H:i', $data['webinar_date'].' '.$data['webinar_time'], config('app.webinar_timezone'));
        unset($data['webinar_date'], $data['webinar_time']);
        if ($request->hasFile('banner')) {
            if ($webinar->banner_path) Storage::disk('public')->delete($webinar->banner_path);
            $data['banner_path'] = $request->file('banner')->store('webinars', 'public');
        }
        $webinar->update($data);
        return redirect()->route('admin.webinars.index')->with('success', 'Webinar updated.');
    }

    public function destroy(Webinar $webinar)
    {
        if ($webinar->banner_path) Storage::disk('public')->delete($webinar->banner_path);
        $webinar->delete();
        return back()->with('success', 'Webinar deleted.');
    }

    public function goLive(Webinar $webinar)
    {
        abort_unless($webinar->adminCanGoLive(), 403, 'You can go live from 10 minutes before the webinar until it ends.');

        return view('webinars.join', [
            'webinar' => $webinar,
            'isHost' => true,
            'tokenUrl' => route('admin.webinars.token', $webinar),
            'commentsUrl' => route('admin.webinars.comments', $webinar),
            'controlsUrl' => null,
            'moderationUrl' => route('admin.webinars.participant-control', $webinar),
            'participantUrl' => route('admin.webinars.participant', [$webinar, '__USER__']),
        ]);
    }

    public function token(Request $request, Webinar $webinar, AgoraTokenService $tokens)
    {
        abort_unless($webinar->adminCanGoLive(), 403, 'You can go live from 10 minutes before the webinar until it ends.');

        return response()->json($tokens->credentials($webinar, $request->user(), true));
    }

    public function comments(Request $request, Webinar $webinar)
    {
        abort_unless($webinar->adminCanGoLive(), 403);
        abort_unless($webinar->allow_qa, 403, 'Chat is disabled for this webinar.');

        if ($request->isMethod('post')) {
            $data = $request->validate(['comment' => 'required|string|max:1000']);
            WebinarComment::create($data + ['webinar_id' => $webinar->id, 'user_id' => $request->user()->id]);
        }

        $comments = $webinar->comments()->with('user:id,name')->where('id', '>', $request->integer('after'))
            ->oldest('id')->limit(100)->get()->map(fn ($comment) => [
                'id' => $comment->id,
                'name' => $comment->user?->name ?? 'Member',
                'message' => $comment->comment,
                'time' => $comment->created_at->format('h:i A'),
                'mine' => $comment->user_id === $request->user()->id,
            ]);

        return response()->json($comments);
    }

    public function controlParticipant(Request $request, Webinar $webinar)
    {
        abort_unless($webinar->adminCanGoLive(), 403);
        $data = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'action' => 'required|in:mute_audio,disable_video,ask_unmute,ask_video,remove',
        ]);
        $command = ['id' => (string) Str::uuid(), 'action' => $data['action']];
        Cache::put("webinar:{$webinar->id}:user:{$data['user_id']}:control", $command, now()->addMinutes(10));

        return response()->json(['success' => true, 'command' => $command]);
    }

    public function participant(Webinar $webinar, User $user)
    {
        abort_unless($webinar->adminCanGoLive(), 403);

        return response()->json(['id' => $user->id, 'name' => $user->name]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'categories' => 'required|array|min:1',
            'categories.*' => 'distinct|in:business,matrimony,other',
            'description' => 'required|string|max:5000',
            'banner' => 'nullable|image|max:5120', 'speaker_name' => 'nullable|string|max:255',
            'agenda' => 'nullable|string|max:5000',
            'webinar_date' => 'required|date',
            'webinar_time' => 'required|date_format:H:i',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'audience' => 'required|in:all,subscribers',
            'require_registration' => 'sometimes|boolean',
            'allow_qa' => 'sometimes|boolean',
            'allow_recording' => 'sometimes|boolean',
            'access_type' => 'required|in:free,specific_plans',
            'plan_ids' => 'nullable|required_if:access_type,specific_plans|array|min:1',
            'plan_ids.*' => 'string|max:50',
            'status' => 'required|in:draft,published,cancelled',
        ]);
    }
}
