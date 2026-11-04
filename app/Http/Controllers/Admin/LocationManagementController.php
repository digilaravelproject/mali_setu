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

    public function districts()
    {
        return view('admin.locations.districts', [
            'districts' => District::withCount('talukas')->orderBy('name')->paginate(25),
        ]);
    }

    public function talukas()
    {
        return view('admin.locations.talukas', [
            'districts' => District::orderBy('name')->get(),
            'talukas' => Taluka::with('district')->withCount('localities')->orderBy('name')->paginate(25),
        ]);
    }

    public function localities()
    {
        return view('admin.locations.localities', [
            'talukas' => Taluka::with('district')->orderBy('name')->get(),
            'localities' => Locality::with('taluka.district')->orderBy('name')->paginate(25),
        ]);
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
