@extends('admin.layouts.app')
@section('title','Registered Users')
@section('page-title','Volunteer Registered Users')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><div><h3 class="fw-bold mb-1">{{ $user->name }}</h3><div class="text-muted">Referral code: <strong>{{ $user->reffral_code }}</strong> · {{ $referrals->total() }} registered users</div></div><a href="{{ route('admin.volunteers.index') }}" class="btn btn-outline-secondary">Back</a></div>
<div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>#</th><th>Registered On</th><th>User</th><th>Type</th><th>Referral Code</th><th>Points</th><th>Status</th><th></th></tr></thead><tbody>
@forelse($referrals as $referral)@php($member=$referral->referredUser)<tr><td>{{ $referrals->firstItem()+$loop->index }}</td><td>{{ $referral->created_at?->format('d M Y, h:i A') }}</td><td><strong>{{ $member?->name ?? 'Deleted user' }}</strong><div class="small text-muted">{{ $member?->phone }}</div></td><td>{{ ucfirst((string)$member?->user_type) }}</td><td>{{ $referral->referral_code_used }}</td><td>{{ $referral->commission_points }}</td><td><span class="badge {{ $referral->status==='active'?'text-bg-success':'text-bg-secondary' }}">{{ ucfirst($referral->status) }}</span></td><td>@if($member)<a href="{{ route('admin.users.show',$member) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>@endif</td></tr>@empty<tr><td colspan="8" class="text-center text-muted py-5">No users registered through this referral code.</td></tr>@endforelse
</tbody></table></div></div><div class="mt-3">{{ $referrals->links() }}</div>
@endsection
