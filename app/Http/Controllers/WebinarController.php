<?php

namespace App\Http\Controllers;

use App\Mail\WebinarRegistrationMail;
use App\Models\Webinar;
use App\Models\WebinarComment;
use App\Models\WebinarRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
        abort_unless($webinar->userCanAccess(auth()->user()), 403);
        abort_unless(! $webinar->require_registration || $webinar->registrations()->where('user_id',auth()->id())->exists(), 403);
        abort_unless($webinar->attendeeCanJoin(), 403, 'Attendees can join when the webinar starts.');
        return view('webinars.join', ['webinar'=>$webinar, 'agoraAppId'=>config('services.agora.app_id'), 'agoraToken'=>config('services.agora.temporary_token'), 'isHost'=>false]);
    }
}
