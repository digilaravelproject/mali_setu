<?php

namespace App\Http\Controllers;

use App\Mail\WebinarRegistrationMail;
use App\Models\Webinar;
use App\Models\WebinarComment;
use App\Models\WebinarRegistration;
use App\Services\AgoraTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;

class WebinarController extends Controller
{
    public function index(Request $request)
    {
        $query = Webinar::where('status', 'published')->withCount('registrations')->orderBy('starts_at');
        if ($request->filled('search')) { $s = trim($request->search); $query->where(fn($q) => $q->where('title','like',"%{$s}%")->orWhere('speaker_name','like',"%{$s}%")); }
        $webinars = $query->paginate(9)->withQueryString();
        return view('webinars.index', compact('webinars'));
    }

    public function show(Webinar $webinar)
    {
        abort_unless($webinar->status === 'published', 404);
        $webinar->loadCount('registrations')->load(['comments' => fn($q) => $q->with('user:id,name,photo')->latest()->limit(30)]);
        $registered = $webinar->registrations()->where('user_id', auth()->id())->exists();
        $canAccess = $webinar->userCanAccess(auth()->user());
        return view('webinars.show', compact('webinar','registered','canAccess'));
    }

    public function register(Webinar $webinar)
    {
        abort_unless($webinar->status === 'published' && $webinar->starts_at->isFuture(), 422);
        abort_unless($webinar->userCanAccess(auth()->user()), 403, 'You do not have access to this webinar.');
        WebinarRegistration::firstOrCreate(['webinar_id'=>$webinar->id,'user_id'=>auth()->id()]);
        try { Mail::to(auth()->user()->email)->send(new WebinarRegistrationMail($webinar)); }
        catch (\Throwable $e) { Log::warning('Webinar mail failed', ['error'=>$e->getMessage()]); }
        return back()->with('success','Registration successful. Webinar details and the join link were emailed to you.');
    }

    public function comment(Request $request, Webinar $webinar)
    {
        abort_unless($webinar->allow_qa, 403, 'Q&A is disabled for this webinar.');
        $data = $request->validate(['comment'=>'required|string|max:1000']);
        WebinarComment::create($data+['webinar_id'=>$webinar->id,'user_id'=>auth()->id()]);
        return back()->with('success','Comment posted.');
    }

    public function myRegistrations()
    {
        $registrations = WebinarRegistration::where('user_id',auth()->id())->with('webinar')->latest()->paginate(12);
        return view('webinars.my-registrations',compact('registrations'));
    }

    public function join(Webinar $webinar)
    {
        $this->authorizeAttendee($webinar);

        return view('webinars.join', [
            'webinar' => $webinar,
            'isHost' => false,
            'tokenUrl' => route('webinars.token', $webinar),
            'commentsUrl' => route('webinars.live-comments', $webinar),
            'controlsUrl' => route('webinars.controls', $webinar),
        ]);
    }

    public function token(Request $request, Webinar $webinar, AgoraTokenService $tokens)
    {
        $this->authorizeAttendee($webinar);

        return response()->json($tokens->credentials($webinar, $request->user(), false));
    }

    public function liveComments(Request $request, Webinar $webinar)
    {
        $this->authorizeAttendee($webinar);
        abort_unless($webinar->allow_qa, 403, 'Chat is disabled for this webinar.');

        if ($request->isMethod('post')) {
            $data = $request->validate(['comment' => 'required|string|max:1000']);
            WebinarComment::create($data + ['webinar_id' => $webinar->id, 'user_id' => $request->user()->id]);
        }

        return response()->json($this->commentsAfter($webinar, $request->integer('after')));
    }

    public function controls(Request $request, Webinar $webinar)
    {
        $this->authorizeAttendee($webinar);
        $command = Cache::get("webinar:{$webinar->id}:user:{$request->user()->id}:control");

        return response()->json($command && ($command['id'] ?? null) !== $request->query('seen') ? $command : null);
    }

    private function authorizeAttendee(Webinar $webinar): void
    {
        abort_unless($webinar->userCanAccess(auth()->user()), 403);
        abort_unless(! $webinar->require_registration || $webinar->registrations()->where('user_id', auth()->id())->exists(), 403);
        abort_unless($webinar->attendeeCanJoin(), 403, 'Attendees can join when the webinar starts.');
    }

    private function commentsAfter(Webinar $webinar, int $after)
    {
        return $webinar->comments()->with('user:id,name')->where('id', '>', $after)
            ->oldest('id')->limit(100)->get()->map(fn ($comment) => [
                'id' => $comment->id,
                'name' => $comment->user?->name ?? 'Member',
                'message' => $comment->comment,
                'time' => $comment->created_at->format('h:i A'),
                'mine' => $comment->user_id === auth()->id(),
            ]);
    }
}
