<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Locality;
use App\Models\Taluka;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LocationManagementController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.locations.districts.index');
    }

    public function districts(Request $request)
    {
        $query = District::withCount('talukas')->orderBy('name');
        $this->applyCommonFilters($query, $request);

        return view('admin.locations.districts', [
            'districts' => $query->paginate(25)->withQueryString(),
        ]);
    }

    public function talukas(Request $request)
    {
        $query = Taluka::with('district')->withCount('localities')->orderBy('name');
        $this->applyCommonFilters($query, $request);
        $query->when($request->filled('district_id'), fn ($q) => $q->where('district_id', $request->integer('district_id')));

        return view('admin.locations.talukas', [
            'districts' => District::orderBy('name')->get(),
            'talukas' => $query->paginate(25)->withQueryString(),
        ]);
    }

    public function localities(Request $request)
    {
        $query = Locality::with('taluka.district')->orderBy('name');
        $this->applyCommonFilters($query, $request);
        $query->when($request->filled('district_id'), fn ($q) => $q->whereHas(
            'taluka', fn ($taluka) => $taluka->where('district_id', $request->integer('district_id'))
        ));
        $query->when($request->filled('taluka_id'), fn ($q) => $q->where('taluka_id', $request->integer('taluka_id')));
        $query->when($request->filled('type'), fn ($q) => $q->where('type', $request->string('type')->toString()));

        return view('admin.locations.localities', [
            'districts' => District::orderBy('name')->get(),
            'talukas' => Taluka::with('district')->orderBy('name')->get(),
            'localities' => $query->paginate(25)->withQueryString(),
        ]);
    }

    private function applyCommonFilters($query, Request $request): void
    {
        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = trim($request->string('search')->toString());
            $q->where('name', 'like', "%{$search}%");
        });
        $query->when($request->filled('status'), fn ($q) => $q->where('is_active', $request->string('status')->toString() === 'active'));
    }

    public function storeDistrict(Request $request)
    {
        District::create($request->validate(['name' => ['required', 'string', 'max:120', 'unique:districts,name']]) + ['is_active' => $request->boolean('is_active', true)]);

        return redirect()->route('admin.locations.districts.index')->with('success', 'District added successfully.');
    }

    public function updateDistrict(Request $request, District $district)
    {
        $district->update($request->validate(['name' => ['required', 'string', 'max:120', Rule::unique('districts')->ignore($district)]]) + ['is_active' => $request->boolean('is_active')]);

        return redirect()->route('admin.locations.districts.index')->with('success', 'District updated successfully.');
    }

    public function destroyDistrict(District $district)
    {
        $district->delete();

        return redirect()->route('admin.locations.districts.index')->with('success', 'District and its child locations deleted.');
    }

    public function storeTaluka(Request $request)
    {
        $data = $request->validate(['district_id' => ['required', 'exists:districts,id'], 'name' => ['required', 'string', 'max:120']]);
        $request->validate(['name' => Rule::unique('talukas')->where('district_id', $data['district_id'])]);
        Taluka::create($data + ['is_active' => $request->boolean('is_active', true)]);

        return redirect()->route('admin.locations.talukas.index')->with('success', 'Taluka added successfully.');
    }

    public function updateTaluka(Request $request, Taluka $taluka)
    {
        $data = $request->validate(['district_id' => ['required', 'exists:districts,id'], 'name' => ['required', 'string', 'max:120']]);
        $request->validate(['name' => Rule::unique('talukas')->where('district_id', $data['district_id'])->ignore($taluka)]);
        $taluka->update($data + ['is_active' => $request->boolean('is_active')]);

        return redirect()->route('admin.locations.talukas.index')->with('success', 'Taluka updated successfully.');
    }

    public function destroyTaluka(Taluka $taluka)
    {
        $taluka->delete();

        return redirect()->route('admin.locations.talukas.index')->with('success', 'Taluka and its cities/villages deleted.');
    }

    public function storeLocality(Request $request)
    {
        $data = $request->validate(['taluka_id' => ['required', 'exists:talukas,id'], 'name' => ['required', 'string', 'max:120'], 'type' => ['required', Rule::in(['city', 'village'])]]);
        $request->validate(['name' => Rule::unique('localities')->where(fn ($q) => $q->where('taluka_id', $data['taluka_id'])->where('type', $data['type']))]);
        Locality::create($data + ['is_active' => $request->boolean('is_active', true)]);

        return redirect()->route('admin.locations.localities.index')->with('success', ucfirst($data['type']).' added successfully.');
    }

    public function updateLocality(Request $request, Locality $locality)
    {
        $data = $request->validate(['taluka_id' => ['required', 'exists:talukas,id'], 'name' => ['required', 'string', 'max:120'], 'type' => ['required', Rule::in(['city', 'village'])]]);
        $request->validate(['name' => Rule::unique('localities')->where(fn ($q) => $q->where('taluka_id', $data['taluka_id'])->where('type', $data['type']))->ignore($locality)]);
        $locality->update($data + ['is_active' => $request->boolean('is_active')]);

        return redirect()->route('admin.locations.localities.index')->with('success', 'City/village updated successfully.');
    }

    public function destroyLocality(Locality $locality)
    {
        $locality->delete();

        return redirect()->route('admin.locations.localities.index')->with('success', 'City/village deleted successfully.');
    }
}
