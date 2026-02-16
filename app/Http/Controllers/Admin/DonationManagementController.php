<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\DonationCause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class DonationManagementController extends Controller
{
    /**
     * Display all donations with user information
     */
    public function index(Request $request)
    {
        $query = Donation::with('user', 'cause');
        
        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        // Filter by cause
        if ($request->has('cause_id') && $request->cause_id !== '') {
            $query->where('cause_id', $request->cause_id);
        }
        
        // Search by user name or email
        if ($request->has('search') && $request->search !== '') {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filter by date range
        if ($request->has('date_from') && $request->date_from !== '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to !== '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $donations = $query->latest()->paginate(20);
        $causes = DonationCause::all();
        
        // Calculate statistics
        $stats = [
            'total' => Donation::count(),
            'total_amount' => Donation::where('status', 'completed')->sum('amount'),
            'completed' => Donation::where('status', 'completed')->count(),
            'pending' => Donation::where('status', 'pending')->count(),
            'failed' => Donation::where('status', 'failed')->count(),
        ];
        
        return view('admin.donations.index', compact('donations', 'causes', 'stats'));
    }
    
    /**
     * Display a specific donation
     */
    public function show($id)
    {
        $donation = Donation::with('user', 'cause')->findOrFail($id);
        return view('admin.donations.show', compact('donation'));
    }
    
    /**
     * Display all donation causes
     */
    public function causes(Request $request)
    {
        $query = DonationCause::query();
        
        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        // Filter by category
        if ($request->has('category') && $request->category !== '') {
            $query->where('category', $request->category);
        }
        
        // Search by title or organization
        if ($request->has('search') && $request->search !== '') {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('organization', 'like', '%' . $request->search . '%');
            });
        }
        
        $causes = $query->latest()->paginate(15);
        
        // Calculate statistics
        $stats = [
            'total' => DonationCause::count(),
            'active' => DonationCause::where('status', 'active')->count(),
            'inactive' => DonationCause::where('status', 'inactive')->count(),
            'total_target' => DonationCause::sum('target_amount'),
            'total_raised' => DonationCause::sum('raised_amount'),
        ];
        
        return view('admin.donations.causes.index', compact('causes', 'stats'));
    }
    
    /**
     * Show form to create new cause
     */
    public function createCause()
    {
        return view('admin.donations.causes.create');
    }
    
    /**
     * Store new donation cause
     */
    public function storeCause(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'organization' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:1',
            'urgency' => 'required|in:low,medium,high,critical',
            'location' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'contact_phone' => 'required|string|max:20',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        try {
            $data = $validator->validated();
            
            // Handle contact info as JSON
            $data['contact_info'] = json_encode([
                'phone' => $data['contact_phone']
            ]);
            unset($data['contact_phone']);
            
            // Handle image upload
            if ($request->hasFile('image_url')) {
                $image = $request->file('image_url');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('donations', $imageName, 'public');
                $data['image_url'] = '/storage/' . $path;
            }
            
            DonationCause::create($data);
            
            return redirect()->route('admin.donations.causes')
                ->with('success', 'Donation cause created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create donation cause: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Show form to edit cause
     */
    public function editCause($id)
    {
        $cause = DonationCause::findOrFail($id);
        return view('admin.donations.causes.edit', compact('cause'));
    }
    
    /**
     * Update donation cause
     */
    public function updateCause(Request $request, $id)
    {
        $cause = DonationCause::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'organization' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:1',
            'urgency' => 'required|in:low,medium,high,critical',
            'location' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'contact_phone' => 'required|string|max:20',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        try {
            $data = $validator->validated();
            
            // Handle contact info as JSON
            $data['contact_info'] = json_encode([
                'phone' => $data['contact_phone']
            ]);
            unset($data['contact_phone']);
            
            // Handle image upload
            if ($request->hasFile('image_url')) {
                // Delete old image
                if ($cause->image_url) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $cause->image_url));
                }
                
                $image = $request->file('image_url');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('donations', $imageName, 'public');
                $data['image_url'] = '/storage/' . $path;
            }
            
            $cause->update($data);
            
            return redirect()->route('admin.donations.causes')
                ->with('success', 'Donation cause updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update donation cause: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Delete donation cause
     */
    public function destroyCause($id)
    {
        try {
            $cause = DonationCause::findOrFail($id);
            
            // Check if there are any donations for this cause
            if ($cause->donations()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete a cause with existing donations!');
            }
            
            // Delete image
            if ($cause->image_url) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $cause->image_url));
            }
            
            $cause->delete();
            
            return redirect()->route('admin.donations.causes')
                ->with('success', 'Donation cause deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete donation cause: ' . $e->getMessage());
        }
    }
    
    /**
     * Toggle cause status
     */
    public function toggleCauseStatus($id)
    {
        try {
            $cause = DonationCause::findOrFail($id);
            $cause->status = $cause->status === 'active' ? 'inactive' : 'active';
            $cause->save();
            
            return redirect()->back()
                ->with('success', 'Cause status updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update cause status: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete a donation
     */
    public function destroy($id)
    {
        try {
            $donation = Donation::findOrFail($id);
            $donation->delete();
            
            return redirect()->back()
                ->with('success', 'Donation deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete donation: ' . $e->getMessage());
        }
    }
}
