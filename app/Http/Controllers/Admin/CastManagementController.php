<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cast;
use App\Models\SubCast;
use Illuminate\Http\Request;

class CastManagementController extends Controller
{
    /**
     * Display all casts
     */
    public function index(Request $request)
    {
        $query = Cast::withCount('subCasts');
        
        // Search functionality
        if ($request->has('search') && $request->search !== '') {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }
        
        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }
        
        $casts = $query->latest()->paginate(20);
        
        $stats = [
            'total' => Cast::count(),
            'active' => Cast::where('is_active', true)->count(),
            'inactive' => Cast::where('is_active', false)->count(),
        ];
        
        return view('admin.casts.index', compact('casts', 'stats'));
    }
    
    /**
     * Show create cast form
     */
    public function create()
    {
        return view('admin.casts.create');
    }
    
    /**
     * Store a new cast
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:casts,name',
                'description' => 'nullable|string|max:1000',
                'is_active' => 'boolean',
            ]);
            
            Cast::create([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->has('is_active') ? $request->is_active : true,
            ]);
            
            return redirect()->route('admin.casts.index')->with('success', 'Cast created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create cast: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Show edit cast form
     */
    public function edit($id)
    {
        $cast = Cast::findOrFail($id);
        return view('admin.casts.edit', compact('cast'));
    }
    
    /**
     * Update cast
     */
    public function update(Request $request, $id)
    {
        try {
            $cast = Cast::findOrFail($id);
            
            $request->validate([
                'name' => 'required|string|max:255|unique:casts,name,' . $id,
                'description' => 'nullable|string|max:1000',
                'is_active' => 'boolean',
            ]);
            
            $cast->update([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->has('is_active') ? $request->is_active : false,
            ]);
            
            return redirect()->route('admin.casts.index')->with('success', 'Cast updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update cast: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Delete cast
     */
    public function destroy($id)
    {
        try {
            $cast = Cast::findOrFail($id);
            $cast->delete();
            
            return redirect()->route('admin.casts.index')->with('success', 'Cast deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete cast: ' . $e->getMessage());
        }
    }
    
    /**
     * Toggle cast active status
     */
    public function toggleStatus($id)
    {
        try {
            $cast = Cast::findOrFail($id);
            $cast->update(['is_active' => !$cast->is_active]);
            
            return redirect()->back()->with('success', 'Status updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }

    // SubCast Methods

    /**
     * Display all sub-casts for a cast
     */
    public function subcastIndex(Request $request, $castId)
    {
        $cast = Cast::findOrFail($castId);
        $query = SubCast::where('cast_id', $castId);
        
        // Search functionality
        if ($request->has('search') && $request->search !== '') {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }
        
        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }
        
        $subCasts = $query->latest()->paginate(20);
        
        $stats = [
            'total' => SubCast::where('cast_id', $castId)->count(),
            'active' => SubCast::where('cast_id', $castId)->where('is_active', true)->count(),
            'inactive' => SubCast::where('cast_id', $castId)->where('is_active', false)->count(),
        ];
        
        return view('admin.casts.subcasts.index', compact('cast', 'subCasts', 'stats'));
    }
    
    /**
     * Show create sub-cast form
     */
    public function subcastCreate($castId)
    {
        $cast = Cast::findOrFail($castId);
        return view('admin.casts.subcasts.create', compact('cast'));
    }
    
    /**
     * Store a new sub-cast
     */
    public function subcastStore(Request $request, $castId)
    {
        try {
            $cast = Cast::findOrFail($castId);
            
            $request->validate([
                'name' => 'required|string|max:255|unique:sub_casts,name,NULL,id,cast_id,' . $castId,
                'description' => 'nullable|string|max:1000',
                'is_active' => 'boolean',
            ]);
            
            SubCast::create([
                'cast_id' => $castId,
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->has('is_active') ? $request->is_active : true,
            ]);
            
            return redirect()->route('admin.casts.subcasts.index', $castId)
                            ->with('success', 'Sub-Cast created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create sub-cast: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Show edit sub-cast form
     */
    public function subcastEdit($castId, $subCastId)
    {
        $cast = Cast::findOrFail($castId);
        $subCast = SubCast::where('id', $subCastId)->where('cast_id', $castId)->firstOrFail();
        return view('admin.casts.subcasts.edit', compact('cast', 'subCast'));
    }
    
    /**
     * Update sub-cast
     */
    public function subcastUpdate(Request $request, $castId, $subCastId)
    {
        try {
            $cast = Cast::findOrFail($castId);
            $subCast = SubCast::where('id', $subCastId)->where('cast_id', $castId)->firstOrFail();
            
            $request->validate([
                'name' => 'required|string|max:255|unique:sub_casts,name,' . $subCastId . ',id,cast_id,' . $castId,
                'description' => 'nullable|string|max:1000',
                'is_active' => 'boolean',
            ]);
            
            $subCast->update([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->has('is_active') ? $request->is_active : false,
            ]);
            
            return redirect()->route('admin.casts.subcasts.index', $castId)
                            ->with('success', 'Sub-Cast updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update sub-cast: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Delete sub-cast
     */
    public function subcastDestroy($castId, $subCastId)
    {
        try {
            $cast = Cast::findOrFail($castId);
            $subCast = SubCast::where('id', $subCastId)->where('cast_id', $castId)->firstOrFail();
            $subCast->delete();
            
            return redirect()->route('admin.casts.subcasts.index', $castId)
                            ->with('success', 'Sub-Cast deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete sub-cast: ' . $e->getMessage());
        }
    }
    
    /**
     * Toggle sub-cast active status
     */
    public function subcastToggleStatus($castId, $subCastId)
    {
        try {
            $cast = Cast::findOrFail($castId);
            $subCast = SubCast::where('id', $subCastId)->where('cast_id', $castId)->firstOrFail();
            $subCast->update(['is_active' => !$subCast->is_active]);
            
            return redirect()->back()->with('success', 'Status updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }
}
