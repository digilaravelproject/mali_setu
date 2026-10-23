<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Education;
use Illuminate\Http\Request;

class EducationManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Education::query();

        if ($request->filled('search')) {
            $query->where('highest_qualification', 'like', "%{$request->search}%")
                  ->orWhere('college', 'like', "%{$request->search}%")
                  ->orWhere('university', 'like', "%{$request->search}%");
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        $educations = $query->latest()->paginate(20);

        $stats = [
            'total' => Education::count(),
            'active' => Education::where('is_active', true)->count(),
            'inactive' => Education::where('is_active', false)->count(),
        ];

        return view('admin.educations.index', compact('educations', 'stats'));
    }

    public function create()
    {
        return view('admin.educations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'highest_qualification' => 'required|string|max:255',
            'college' => 'required|string|max:255',
            'university' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'passing_year' => 'nullable|integer',
            'percentage' => 'nullable|string|max:50',
        ]);

        Education::create($request->only([
            'user_id', 'highest_qualification', 'college', 'university', 'specialization', 'passing_year', 'percentage', 'description', 'is_active'
        ]));

        return redirect()->route('admin.educations.index')->with('success', 'Education created successfully!');
    }

    public function edit($id)
    {
        $education = Education::findOrFail($id);
        return view('admin.educations.edit', compact('education'));
    }

    public function update(Request $request, $id)
    {
        $education = Education::findOrFail($id);

        $request->validate([
            'highest_qualification' => 'required|string|max:255',
            'college' => 'required|string|max:255',
            'university' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'passing_year' => 'nullable|integer',
            'percentage' => 'nullable|string|max:50',
        ]);

        $education->update($request->only([
            'user_id', 'highest_qualification', 'college', 'university', 'specialization', 'passing_year', 'percentage', 'description', 'is_active'
        ]));

        return redirect()->route('admin.educations.index')->with('success', 'Education updated successfully!');
    }

    public function destroy($id)
    {
        $education = Education::findOrFail($id);
        $education->delete();

        return redirect()->route('admin.educations.index')->with('success', 'Education deleted successfully!');
    }

    public function toggleStatus($id)
    {
        $education = Education::findOrFail($id);
        $education->is_active = !$education->is_active;
        $education->save();

        return redirect()->back()->with('success', 'Education status updated successfully!');
    }
}
