<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Webinar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    public function create() { return view('admin.webinars.form', ['webinar' => new Webinar]); }
    public function edit(Webinar $webinar) { return view('admin.webinars.form', compact('webinar')); }

    public function store(Request $request)
    {
        $data = $this->validated($request);
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

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255', 'description' => 'required|string|max:5000',
            'banner' => 'nullable|image|max:5120', 'speaker_name' => 'nullable|string|max:255',
            'agenda' => 'nullable|string|max:5000', 'starts_at' => 'required|date',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'audience' => 'required|in:all,subscribers', 'status' => 'required|in:draft,published,cancelled',
        ]);
    }
}
