@extends('layouts.app')

@section('content')
<style>
.req-card { border-radius: 16px; background: rgba(255,255,255,0.8); border: 1px solid rgba(132,20,79,0.08); padding: 16px; }
.req-photo { width: 52px; height: 52px; border-radius: 12px; object-fit: cover; }
.status-pill { font-size: 0.72rem; padding: 4px 12px; border-radius: 50px; font-weight: 700; }
</style>

<div class="py-4">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('matrimony.index') }}" class="btn btn-light btn-sm rounded-3"><i class="fa-solid fa-arrow-left"></i></a>
        <h4 class="fw-bold mb-0">Connection Requests</h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-4 mb-4"><i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}</div>
    @endif

    <div class="row g-4">
        {{-- Received Requests --}}
        <div class="col-lg-6">
            <div class="glass-card">
                <h5 class="fw-bold text-primary mb-4"><i class="fa-solid fa-inbox me-2"></i> Received Requests <span class="badge bg-primary rounded-pill ms-2">{{ $receivedRequests->count() }}</span></h5>

                @forelse($receivedRequests as $req)
                @php $sp = $req->sender->matrimonyProfile ?? null; @endphp
                <div class="req-card mb-3">
                    <div class="d-flex align-items-start gap-3">
                        @if($sp && !empty($sp->personal_details['photos'][0]))
                            <img src="{{ asset('storage/' . $sp->personal_details['photos'][0]) }}" class="req-photo">
                        @else
                            <img src="{{ asset('default-avatar.png') }}" class="req-photo">
                        @endif
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <h6 class="fw-bold mb-0 small">{{ $req->sender->name }}</h6>
                                @if($req->status === 'pending')
                                    <span class="status-pill bg-warning text-dark">Pending</span>
                                @elseif($req->status === 'accepted')
                                    <span class="status-pill bg-success text-white">Accepted</span>
                                @else
                                    <span class="status-pill bg-danger text-white">Rejected</span>
                                @endif
                            </div>
                            @if($sp)
                            <div class="text-muted" style="font-size:0.75rem;">
                                {{ $sp->age ?? '' }}y • {{ ($sp->location_details['city'] ?? '') }}
                                • {{ ($sp->education_details['highest_qualification'] ?? '') }}
                            </div>
                            @endif
                            @if($req->message)
                                <p class="text-secondary small mt-1 mb-0 fst-italic">"{{ $req->message }}"</p>
                            @endif
                            <div class="d-flex gap-2 mt-2 flex-wrap">
                                @if($sp)<a href="{{ route('matrimony.show', $sp->id) }}" class="btn btn-outline-secondary btn-sm rounded-3 py-1 px-3" style="font-size:0.78rem;">View</a>@endif
                                @if($req->status === 'accepted')
                                    @php $conv = $conversations->get($req->sender_id) ?? $conversations->get((string)$req->sender_id) ?? $conversations->get((int)$req->sender_id); @endphp
                                    @if($conv)
                                        <a href="{{ route('matrimony.chat', $conv->id) }}" class="btn btn-primary btn-sm rounded-3 py-1 px-3" style="font-size:0.78rem; background: var(--primary) !important; border-color: var(--primary) !important; color: #fff !important;">
                                            <i class="fa-solid fa-comments me-1"></i> Chat
                                        </a>
                                    @endif
                                @endif
                                @if($req->status === 'pending')
                                    <form action="{{ route('matrimony.request.respond', $req->id) }}" method="POST" class="d-inline">
                                        @csrf <input type="hidden" name="status" value="accepted">
                                        <button class="btn btn-success btn-sm rounded-3 py-1 px-3" style="font-size:0.78rem;"><i class="fa-solid fa-check me-1"></i>Accept</button>
                                    </form>
                                    <form action="{{ route('matrimony.request.respond', $req->id) }}" method="POST" class="d-inline">
                                        @csrf <input type="hidden" name="status" value="rejected">
                                        <button class="btn btn-outline-danger btn-sm rounded-3 py-1 px-3" style="font-size:0.78rem;"><i class="fa-solid fa-times me-1"></i>Reject</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        <i class="fa-solid fa-inbox fa-2x mb-2 opacity-25"></i>
                        <p class="small mb-0">No requests received yet.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Sent Requests --}}
        <div class="col-lg-6">
            <div class="glass-card">
                <h5 class="fw-bold text-primary mb-4"><i class="fa-solid fa-paper-plane me-2"></i> Sent Requests <span class="badge bg-primary rounded-pill ms-2">{{ $sentRequests->count() }}</span></h5>

                @forelse($sentRequests as $req)
                @php $rp = $req->receiver->matrimonyProfile ?? null; @endphp
                <div class="req-card mb-3">
                    <div class="d-flex align-items-start gap-3">
                        @if($rp && !empty($rp->personal_details['photos'][0]))
                            <img src="{{ asset('storage/' . $rp->personal_details['photos'][0]) }}" class="req-photo">
                        @else
                            <img src="{{ asset('default-avatar.png') }}" class="req-photo">
                        @endif
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <h6 class="fw-bold mb-0 small">{{ $req->receiver->name ?? 'Unknown' }}</h6>
                                @if($req->status === 'pending')
                                    <span class="status-pill bg-warning text-dark">Awaiting</span>
                                @elseif($req->status === 'accepted')
                                    <span class="status-pill bg-success text-white">Accepted <i class="fa-solid fa-heart ms-1"></i></span>
                                @else
                                    <span class="status-pill bg-danger text-white">Declined</span>
                                @endif
                            </div>
                            @if($rp)
                            <div class="text-muted" style="font-size:0.75rem;">
                                {{ $rp->age ?? '' }}y • {{ ($rp->location_details['city'] ?? '') }}
                            </div>
                            @endif
                            <div class="d-flex gap-2 mt-2 flex-wrap">
                                @if($rp)<a href="{{ route('matrimony.show', $rp->id) }}" class="btn btn-outline-secondary btn-sm rounded-3 py-1 px-3" style="font-size:0.78rem;">View Profile</a>@endif
                                @if($req->status === 'accepted')
                                    @php $conv = $conversations->get($req->receiver_id) ?? $conversations->get((string)$req->receiver_id) ?? $conversations->get((int)$req->receiver_id); @endphp
                                    @if($conv)
                                        <a href="{{ route('matrimony.chat', $conv->id) }}" class="btn btn-primary btn-sm rounded-3 py-1 px-3" style="font-size:0.78rem; background: var(--primary) !important; border-color: var(--primary) !important; color: #fff !important;">
                                            <i class="fa-solid fa-comments me-1"></i> Chat
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        <i class="fa-solid fa-paper-plane fa-2x mb-2 opacity-25"></i>
                        <p class="small mb-0">You haven't sent any requests yet.</p>
                        <a href="{{ route('matrimony.browse') }}" class="btn btn-primary btn-sm rounded-3 mt-2 px-4">Browse Profiles</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
