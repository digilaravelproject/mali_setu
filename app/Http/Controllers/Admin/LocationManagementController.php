<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Locality;
use App\Models\State;
use App\Models\Taluka;
use App\Services\LocationSyncService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Throwable;

class LocationManagementController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.locations.states.index');
    }

    public function states(Request $request)
    {
        $query = State::withCount('districts')->orderBy('name');
        $this->applyCommonFilters($query, $request);

        return view('admin.locations.states', ['states' => $query->paginate(25)->withQueryString()]);
    }

    public function districts(Request $request)
    {
        $query = District::with('state')->withCount('talukas')->orderBy('name');
        $this->applyCommonFilters($query, $request);
        $query->when($request->filled('state_id'), fn ($q) => $q->where('state_id', $request->integer('state_id')));

        return view('admin.locations.districts', [
            'states' => State::where('external_code', '!=', 'LEGACY')->orderBy('name')->get(),
            'districts' => $query->paginate(25)->withQueryString(),
        ]);
    }

    public function talukas(Request $request)
    {
        $query = Taluka::with('district.state')->withCount('localities')->orderBy('name');
        $this->applyCommonFilters($query, $request);
        $query->when($request->filled('state_id'), fn ($q) => $q->whereHas('district', fn ($district) => $district->where('state_id', $request->integer('state_id'))));
        $query->when($request->filled('district_id'), fn ($q) => $q->where('district_id', $request->integer('district_id')));

        return view('admin.locations.talukas', [
            'states' => State::where('external_code', '!=', 'LEGACY')->orderBy('name')->get(),
            'districts' => District::with('state')->orderBy('name')->get(),
            'talukas' => $query->paginate(25)->withQueryString(),
        ]);
    }

    public function localities(Request $request)
    {
        $query = Locality::with('taluka.district.state')->orderBy('name');
        $this->applyCommonFilters($query, $request);
        $query->when($request->filled('state_id'), fn ($q) => $q->whereHas('taluka.district', fn ($district) => $district->where('state_id', $request->integer('state_id'))));
        $query->when($request->filled('district_id'), fn ($q) => $q->whereHas('taluka', fn ($taluka) => $taluka->where('district_id', $request->integer('district_id'))));
        $query->when($request->filled('taluka_id'), fn ($q) => $q->where('taluka_id', $request->integer('taluka_id')));
        $query->when($request->filled('type'), fn ($q) => $q->where('type', $request->string('type')->toString()));

        return view('admin.locations.localities', [
            'states' => State::where('external_code', '!=', 'LEGACY')->orderBy('name')->get(),
            'districts' => District::with('state')->orderBy('name')->get(),
            'talukas' => Taluka::with('district')->orderBy('name')->get(),
            'localities' => $query->paginate(25)->withQueryString(),
        ]);
    }

    public function sync(Request $request, LocationSyncService $sync)
    {
        $data = $request->validate(['level' => 'required|in:states,districts,talukas,localities']);
        set_time_limit(0);

        try {
            $count = $sync->sync($data['level']);
        } catch (ConnectionException|RequestException $exception) {
            report($exception);
            return back()->with('error', 'The location provider is unavailable. No existing records were removed; please try again.');
        } catch (Throwable $exception) {
            report($exception);
            return back()->with('error', 'Location sync failed: '.$exception->getMessage());
        }

        return back()->with('success', number_format($count).' '.str_replace('localities', 'cities', $data['level']).' synchronized successfully.');
    }

    private function applyCommonFilters($query, Request $request): void
    {
        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = trim($request->string('search')->toString());
            $q->where('name', 'like', "%{$search}%");
        });
        $query->when($request->filled('status'), fn ($q) => $q->where('is_active', $request->string('status')->toString() === 'active'));
    }
}
