<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\WebinarRegistrationMail;
use App\Models\Webinar;
use App\Models\WebinarComment;
use App\Models\WebinarRegistration;
use App\Services\AgoraTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WebinarController extends Controller
{
    public function index(Request $request)
    {
        $query = Webinar::query()->where('status', 'published')->withCount('registrations');
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(fn ($q) => $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('speaker_name', 'like', "%{$search}%"));
        }
        if ($request->status === 'upcoming') $query->where('starts_at', '>', now());
        if ($request->status === 'completed') $query->where('starts_at', '<', now());

        $webinars = $query->orderBy('starts_at')->paginate($request->integer('per_page', 12));
        $this->applyEffectiveRegistrationRequirement($webinars->getCollection(), $request);

        return response()->json(['success' => true, 'data' => $webinars]);
    }

    public function upcoming(Request $request)
    {
        $webinars = Webinar::where('status', 'published')->where('starts_at', '>', now())
            ->withCount('registrations')->orderBy('starts_at')->limit(3)->get();
        $this->applyEffectiveRegistrationRequirement($webinars, $request);
        return response()->json(['success' => true, 'data' => $webinars]);
    }

    public function show(Request $request, Webinar $webinar)
    {
        abort_unless($webinar->status === 'published', 404);
        $webinar->loadCount('registrations');
        $user = $this->authenticatedUser($request);
        $webinar->is_registered = $user
            ? $webinar->registrations()->where('user_id', $user->id)->exists() : false;
        $webinar->require_registration = $webinar->require_registration
            && ! $webinar->is_registered
            && $webinar->ends_at->isFuture();
        return response()->json(['success' => true, 'data' => $webinar]);
    }

    public function register(Request $request, Webinar $webinar)
    {
        abort_unless($webinar->status === 'published' && $webinar->starts_at->isFuture(), 422, 'This webinar is not open for registration.');
        abort_unless($webinar->userCanAccess($request->user()), 403, 'You do not have access to this webinar.');
        $registration = WebinarRegistration::firstOrCreate(['webinar_id' => $webinar->id, 'user_id' => $request->user()->id]);
        try {
            Mail::to($request->user()->email)->send(new WebinarRegistrationMail($webinar));
        } catch (\Throwable $e) {
            Log::warning('Webinar registration email failed', ['registration_id' => $registration->id, 'error' => $e->getMessage()]);
        }
        return response()->json(['success' => true, 'message' => 'Webinar registration successful. Details were emailed to you.', 'data' => ['registration' => $registration, 'webinar' => $webinar]], 201);
    }

    public function comments(Webinar $webinar)
    {
        abort_unless($webinar->allow_qa, 403, 'Q&A is disabled for this webinar.');
        $comments = $webinar->comments()->with('user:id,name,photo')->latest()->paginate(20);
        return response()->json(['success' => true, 'data' => $comments]);
    }

    public function storeComment(Request $request, Webinar $webinar)
    {
        abort_unless($webinar->allow_qa, 403, 'Q&A is disabled for this webinar.');
        $data = $request->validate(['comment' => 'required|string|max:1000']);
        $comment = WebinarComment::create($data + ['webinar_id' => $webinar->id, 'user_id' => $request->user()->id]);
        return response()->json(['success' => true, 'message' => 'Comment posted.', 'data' => $comment->load('user:id,name,photo')], 201);
    }

    public function myRegistrations(Request $request)
    {
        $items = WebinarRegistration::where('user_id', $request->user()->id)
            ->with(['webinar' => fn ($q) => $q->withCount('registrations')])->latest()->paginate(12);
        return response()->json(['success' => true, 'data' => $items]);
    }

    public function joinToken(Request $request, Webinar $webinar, AgoraTokenService $tokens)
    {
        abort_unless($webinar->userCanAccess($request->user()), 403, 'You do not have access to this webinar.');
        abort_unless(! $webinar->require_registration || $webinar->registrations()->where('user_id', $request->user()->id)->exists(), 403, 'Registration is required.');
        abort_unless($webinar->attendeeCanJoin(), 403, 'Attendees can join when the webinar starts.');

        return response()->json(['success' => true, 'data' => $tokens->credentials($webinar, $request->user(), false)]);
    }

    private function applyEffectiveRegistrationRequirement($webinars, Request $request): void
    {
        $userId = $this->authenticatedUser($request)?->id;
        $registeredWebinarIds = $userId
            ? WebinarRegistration::where('user_id', $userId)
                ->whereIn('webinar_id', $webinars->pluck('id'))
                ->pluck('webinar_id')
                ->all()
            : [];

        foreach ($webinars as $webinar) {
            $isRegistered = in_array($webinar->id, $registeredWebinarIds, true);
            $webinar->setAttribute('is_registered', $isRegistered);
            $webinar->setAttribute(
                'require_registration',
                $webinar->require_registration && ! $isRegistered && $webinar->ends_at->isFuture()
            );
        }
    }

    private function authenticatedUser(Request $request)
    {
        return $request->user('sanctum') ?? $request->user();
    }
}
