<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CasteCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserManagementController extends Controller
{
    /**
     * Display all users
     */
    public function index(Request $request)
    {
        $query = User::with(['casteCertificate']);
        
        // Filter by user type if provided
        if ($request->has('user_type') && $request->user_type !== '') {
            $query->where('user_type', $request->user_type);
        }
        
        // Filter by verification status if provided
        if ($request->has('verification_status') && $request->verification_status !== '') {
            $query->where('caste_verification_status', $request->verification_status);
        }
        
        // Search by name or email
        if ($request->has('search') && $request->search !== '') {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        $users = $query->latest()->paginate(20);
        
        $stats = [
            'total' => User::count(),
            'verified' => User::where('caste_verification_status', 'approved')->count(),
            'pending' => User::where('caste_verification_status', 'pending')->count(),
            'rejected' => User::where('caste_verification_status', 'rejected')->count(),
            'by_type' => User::selectRaw('user_type, COUNT(*) as count')
                ->groupBy('user_type')
                ->pluck('count', 'user_type')
                ->toArray()
        ];
        
        return view('admin.users.index', compact('users', 'stats'));
    }
    
    /**
     * Display pending verifications
     */
    public function pendingVerifications(Request $request)
    {
        $query = User::with(['casteCertificate'])
            ->where('caste_verification_status', 'pending');
            
        // Search functionality
        if ($request->has('search') && $request->search !== '') {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        $pendingVerifications = $query->latest()->paginate(15);
        
        $stats = [
            'pending_count' => User::where('caste_verification_status', 'pending')->count(),
            'approved_today' => User::where('caste_verification_status', 'approved')
                ->whereDate('updated_at', today())->count(),
            'rejected_today' => User::where('caste_verification_status', 'rejected')
                ->whereDate('updated_at', today())->count(),
            'caste_certificates' => User::where('caste_verification_status', 'pending')->count(),
            'businesses' => \App\Models\Business::where('verification_status', 'pending')->count(),
            'matrimony_profiles' => \App\Models\MatrimonyProfile::where('approval_status', 'pending')->count(),
        ];
        
        return view('admin.users.verification.pending', compact('stats', 'pendingVerifications'))->with('pending_verifications', $pendingVerifications);
    }
    
    /**
     * Approve caste certificate
     */
    public function approveCertificate(Request $request, $userId)
    {
        try {
            $user = User::findOrFail($userId);
            
            $user->update([
                'caste_verification_status' => 'approved'
            ]);
            
            if ($user->casteCertificate) {
                $user->casteCertificate->update([
                    'admin_notes' => $request->admin_notes,
                    'verified_by' => auth()->id(),
                    'verified_at' => now()
                ]);
            }
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Certificate approved successfully!'
                ]);
            }
            
            return redirect()->back()->with('success', 'Certificate approved successfully!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to approve certificate: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to approve certificate.');
        }
    }
    
    /**
     * Reject caste certificate
     */
    public function rejectCertificate(Request $request, $userId)
    {
        try {
            $request->validate([
                'admin_notes' => 'nullable|string|max:500'
            ]);
            
            $user = User::findOrFail($userId);
            
            $user->update([
                'caste_verification_status' => 'rejected'
            ]);
            
            if ($user->casteCertificate) {
                $user->casteCertificate->update([
                    'admin_notes' => $request->admin_notes ?? 'Certificate rejected by admin',
                    'verified_by' => auth()->id(),
                    'verified_at' => now()
                ]);
            }
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Certificate rejected successfully!'
                ]);
            }
            
            return redirect()->back()->with('success', 'Certificate rejected successfully!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to reject certificate: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to reject certificate.');
        }
    }
    
    /**
     * View user details
     */
    public function show($id)
    {
        $user = User::with(['casteCertificate', 'business', 'matrimonyProfile', 'volunteer'])
            ->findOrFail($id);
            
        return view('admin.users.show', compact('user'));
    }
    
    /**
     * Suspend user account
     */
    public function suspend(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->update(['status' => 'suspended']);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User suspended successfully!',
                    'user' => [
                        'id' => $user->id,
                        'status' => $user->status
                    ]
                ]);
            }
            
            return redirect()->back()->with('success', 'User suspended successfully!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to suspend user: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to suspend user.');
        }
    }
    
    /**
     * Activate user account
     */
    public function activate(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->update(['status' => 'active']);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User activated successfully!',
                    'user' => [
                        'id' => $user->id,
                        'status' => $user->status
                    ]
                ]);
            }
            
            return redirect()->back()->with('success', 'User activated successfully!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to activate user: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to activate user.');
        }
    }
    
    /**
     * Verify user account
     */
    public function verify($id)
    {
        $user = User::findOrFail($id);
        
        $user->update([
            'caste_verification_status' => 'approved',
            'status' => 'active'
        ]);
        
        if ($user->casteCertificate) {
            $user->casteCertificate->update([
                'verified_by' => auth()->id(),
                'verified_at' => now()
            ]);
        }
        
        return redirect()->back()->with('success', 'User verified successfully!');
    }
    
    /**
     * Show the form for editing the specified user
     */
    public function edit($id)
    {
        $user = User::with(['casteCertificate', 'business', 'matrimonyProfile'])
            ->findOrFail($id);
            
        return view('admin.users.edit', compact('user'));
    }
    
    /**
     * Update the specified user in storage
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required|string|max:20',
            'user_type' => 'required|in:general,individual,business,matrimony,volunteer',
            'caste_verification_status' => 'required|in:pending,approved,rejected',
            'status' => 'required|in:active,suspended,banned',
            'admin_notes' => 'nullable|string|max:1000',
            'password' => 'nullable|string|min:8|confirmed'
        ]);
        
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'user_type' => $request->user_type,
            'caste_verification_status' => $request->caste_verification_status,
            'status' => $request->status,
            'admin_notes' => $request->admin_notes
        ];
        
        // Handle email verification status
        if ($request->has('email_verified')) {
            $updateData['email_verified_at'] = $request->email_verified ? now() : null;
        }
        
        // Update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        }
        
        $user->update($updateData);
        
        // Update caste certificate metadata when verification status changes
        if ($request->caste_verification_status === 'approved' && $user->casteCertificate) {
            $user->casteCertificate->update([
                'verified_by' => auth()->id(),
                'verified_at' => now()
            ]);
        } elseif ($request->caste_verification_status === 'rejected' && $user->casteCertificate) {
            $user->casteCertificate->update([
                'verified_by' => auth()->id(),
                'verified_at' => now()
            ]);
        } elseif ($request->caste_verification_status === 'pending' && $user->casteCertificate) {
            $user->casteCertificate->update([
                'verified_by' => null,
                'verified_at' => null
            ]);
        }
        
        return redirect()->route('admin.users.show', $id)
            ->with('success', 'User updated successfully!');
    }
    
    /**
     * Remove the specified user from storage
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Delete associated files if they exist
            if ($user->casteCertificate && $user->casteCertificate->certificate_path) {
                Storage::delete($user->casteCertificate->certificate_path);
            }
            
            // Delete the user (this will cascade delete related records)
            $user->delete();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User deleted successfully!'
                ]);
            }
            
            return redirect()->route('admin.users.index')
                ->with('success', 'User deleted successfully!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete user: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to delete user.');
        }
    }
    
    /**
     * Display pending verifications (alias for backward compatibility)
     */
    public function pending(Request $request)
    {
        return $this->pendingVerifications($request);
    }
}