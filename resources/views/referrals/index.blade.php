@extends('layouts.app')
@section('title','My Referrals — Mali Setu')
@section('content')
<div class="container-fluid py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div><h2 class="fw-bold mb-1">My Referrals</h2><p class="text-muted mb-0">Share your link and review referral history and points.</p></div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Back to Dashboard</a>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-md-4"><div class="glass-card h-100"><div class="text-muted small">My referral code</div><div class="fs-3 fw-bold text-primary my-2">{{ $summary['referral_code'] }}</div><button class="btn btn-sm btn-outline-primary" onclick="navigator.clipboard.writeText(@js($summary['referral_code']))">Copy Code</button></div></div>
        <div class="col-md-4"><div class="glass-card h-100"><div class="text-muted small">Users referred</div><div class="fs-3 fw-bold my-2">{{ $summary['total_referrals'] }}</div><div class="small text-muted">All registrations using your code</div></div></div>
        <div class="col-md-4"><div class="glass-card h-100"><div class="text-muted small">Commission earned</div><div class="fs-3 fw-bold my-2">{{ $summary['total_earned_points'] }} points</div><div class="small text-muted">{{ $summary['commission_points'] }} points for each new referral</div></div></div>
    </div>
    <div class="glass-card mb-4">
        <label class="form-label fw-bold">Referral link</label>
        <div class="input-group"><input id="referralLink" class="form-control" readonly value="{{ $summary['referral_link'] }}"><button class="btn btn-primary" onclick="navigator.clipboard.writeText(document.getElementById('referralLink').value)"><i class="fa-solid fa-copy me-1"></i>Copy</button><a class="btn btn-success" target="_blank" href="https://wa.me/?text={{ urlencode('Join Mali Setu using my referral link: '.$summary['referral_link']) }}"><i class="fa-brands fa-whatsapp me-1"></i>Share</a></div>
    </div>
    <div class="glass-card mb-4">
        <h5 class="fw-bold mb-3">Referral Network</h5>
        @if($network['children'] === [])
            <p class="text-muted mb-0">Your network will appear here after someone registers with your link.</p>
        @else
            <div class="row g-3">@foreach($network['children'] as $node)<div class="col-md-6 col-xl-4">@include('referrals._network-node',['node'=>$node])</div>@endforeach</div>
        @endif
    </div>
    <div class="card border-0 shadow-sm"><div class="card-header bg-white py-3"><h5 class="fw-bold mb-0">Referral History</h5></div><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>Date</th><th>User</th><th>Type</th><th>Points</th><th>Status</th></tr></thead><tbody>
    @forelse($history as $referral)@php($member=$referral->referredUser)<tr><td>{{ $referral->created_at?->format('d M Y') }}</td><td><strong>{{ $member?->name ?? 'Deleted user' }}</strong><div class="small text-muted">{{ $member?->phone }}</div></td><td>{{ ucfirst((string)$member?->user_type) }}</td><td>{{ $referral->commission_points }}</td><td><span class="badge {{ $referral->status==='active'?'text-bg-success':'text-bg-secondary' }}">{{ ucfirst($referral->status) }}</span></td></tr>@empty<tr><td colspan="5" class="text-center text-muted py-5">No referrals yet. Share your link to grow your network.</td></tr>@endforelse
    </tbody></table></div></div><div class="mt-3">{{ $history->links() }}</div>
</div>
@endsection
