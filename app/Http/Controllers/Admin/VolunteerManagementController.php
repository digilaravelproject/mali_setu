<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VolunteerProfile;
use Illuminate\Http\Request;

class VolunteerManagementController extends Controller
{
    /**
     * Display all volunteers
     */
    public function index(Request $request)
    {
        $query = VolunteerProfile::with('user');
        
        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        // Search by user name or location
        if ($request->has('search') && $request->search !== '') {
            $query->where(function($q) use ($request) {
                $q->where('location', 'like', '%' . $request->search . '%')
                  ->orWhere('skills', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('name', 'like', '%' . $request->search . '%')
                                ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
            });
        }
        
        $volunteers = $query->latest()->paginate(20);
        
        $stats = [
            'total' => VolunteerProfile::count(),
            'approved' => VolunteerProfile::where('status', 'active')->count(),
            'pending' => VolunteerProfile::where('status', 'pending')->count(),
            'rejected' => VolunteerProfile::where('status', 'inactive')->count(),
        ];
        
        return view('admin.volunteers.index', compact('volunteers', 'stats'));
    }
    
    /**
     * Display pending volunteers for verification
     */
    public function verification(Request $request)
    {
        $query = VolunteerProfile::with('user')
            ->where('status', 'pending');
            
        // Search functionality
        if ($request->has('search') && $request->search !== '') {
            $query->where(function($q) use ($request) {
                $q->where('location', 'like', '%' . $request->search . '%')
                  ->orWhere('skills', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('name', 'like', '%' . $request->search . '%')
                                ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
            });
        }
        
        $pendingVolunteers = $query->latest()->paginate(15);
        
        $stats = [
            'pending_count' => VolunteerProfile::where('status', 'pending')->count(),
            'approved_today' => VolunteerProfile::where('status', 'active')
                ->whereDate('updated_at', today())->count(),
            'rejected_today' => VolunteerProfile::where('status', 'inactive')
                ->whereDate('updated_at', today())->count(),
        ];
        
        return view('admin.volunteers.verification', compact('pendingVolunteers', 'stats'));
    }
    
    /**
     * Display pending volunteers (alias for verification)
     */
    public function pending(Request $request)
    {
        return $this->verification($request);
    }
    
    /**
     * Approve volunteer
     */
    public function approve(Request $request, $volunteerId)
    {
        try {
            $volunteer = VolunteerProfile::findOrFail($volunteerId);
            
            $volunteer->update([
                'status' => 'active',
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Volunteer approved successfully!'
                ]);
            }
            
            return redirect()->back()->with('success', 'Volunteer approved successfully!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to approve volunteer: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to approve volunteer.');
        }
    }
    
    /**
     * Reject volunteer
     */
    public function reject(Request $request, $volunteerId)
    {
        try {
            \Log::info('Volunteer rejection request', [
                'volunteer_id' => $volunteerId,
                'request_data' => $request->all(),
                'rejection_reason' => $request->rejection_reason
            ]);
            
            $request->validate([
                'rejection_reason' => 'required|string|max:500'
            ]);
            
            $volunteer = VolunteerProfile::findOrFail($volunteerId);
            
            \Log::info('Volunteer found for rejection', [
                'volunteer_id' => $volunteerId,
                'current_status' => $volunteer->status
            ]);
            
            $volunteer->update([
                'status' => 'inactive',
                'rejection_reason' => $request->rejection_reason,
            ]);
            
            \Log::info('Volunteer rejected successfully', [
                'volunteer_id' => $volunteerId,
                'rejection_reason' => $request->rejection_reason
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Volunteer rejected successfully!'
                ]);
            }
            
            return redirect()->back()->with('success', 'Volunteer rejected successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Volunteer rejection validation failed', [
                'volunteer_id' => $volunteerId,
                'validation_errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all())
                ], 422);
            }
            
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            \Log::error('Failed to reject volunteer', [
                'volunteer_id' => $volunteerId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to reject volunteer: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to reject volunteer: ' . $e->getMessage());
        }
    }
    
    /**
     * View volunteer details
     */
    public function show($id)
    {
        $volunteer = VolunteerProfile::with('user')->findOrFail($id);
        
        return view('admin.volunteers.show', compact('volunteer'));
    }
    
    /**
     * Delete volunteer
     */
    public function destroy(Request $request, $id)
    {
        try {
            $volunteer = VolunteerProfile::findOrFail($id);
            
            \Log::info('Deleting volunteer', [
                'volunteer_id' => $id,
                'user_id' => $volunteer->user_id
            ]);
            
            $volunteer->delete();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Volunteer deleted successfully!'
                ]);
            }
            
            return redirect()->route('admin.volunteers.index')->with('success', 'Volunteer deleted successfully!');
        } catch (\Exception $e) {
            \Log::error('Failed to delete volunteer', [
                'volunteer_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete volunteer: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to delete volunteer.');
        }
    }
}
