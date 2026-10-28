@extends('admin.layouts.app')

@section('title', 'Matrimony Request Analysis')
@section('page-title', 'Matrimony - Requests Analysis')

@push('styles')
<style>
    .request-page { --request-accent:#a60753; --request-soft:#fff1f6; }
    .request-shell { border:1px solid #e6e9ef; border-radius:14px; background:#fff; }
    .request-tabs { display:flex; overflow-x:auto; border-bottom:1px solid #e6e9ef; }
    .request-tabs .nav-link { min-width:130px; padding:1rem; border:0; border-right:1px solid #edf0f4; border-radius:0; color:#4b5563; white-space:nowrap; }
    .request-tabs .nav-link.active { color:var(--request-accent); background:#fff; border-bottom:3px solid var(--request-accent); font-weight:700; }
    .request-stat { min-height:92px; border:1px solid #e8ebf0; border-radius:12px; padding:1rem; display:flex; align-items:center; gap:.9rem; background:#fff; }
    .request-stat-icon { width:48px; height:48px; border-radius:50%; display:grid; place-items:center; color:var(--request-accent); background:var(--request-soft); font-size:1.15rem; flex:0 0 auto; }
    .request-stat.success .request-stat-icon { color:#13985b; background:#e8f8f0; }
    .request-stat.danger .request-stat-icon { color:#dc3545; background:#ffebed; }
    .request-stat.muted .request-stat-icon { color:#64748b; background:#eef2f6; }
    .request-stat-value { color:#182230; font-size:1.25rem; font-weight:800; line-height:1.1; }
    .request-stat-label { color:#64748b; font-size:.78rem; }
    .request-filter label { color:#475569; font-size:.75rem; font-weight:700; margin-bottom:.35rem; }
    .request-filter .form-control, .request-filter .form-select { min-height:42px; border-color:#dce2e9; }
    .request-search-btn { min-height:42px; background:var(--request-accent); border-color:var(--request-accent); color:#fff; }
    .request-search-btn:hover { background:#830440; border-color:#830440; color:#fff; }
    .request-table thead th { padding:.85rem .75rem; background:#f8fafc; color:#344054; font-size:.78rem; white-space:nowrap; }
    .request-table tbody td { padding:.8rem .75rem; vertical-align:middle; }
    .member-cell { display:flex; align-items:center; gap:.65rem; min-width:190px; }
    .member-avatar { width:42px; height:42px; border-radius:50%; object-fit:cover; background:#f1f5f9; flex:0 0 auto; }
    .member-name { max-width:175px; color:#111827; font-size:.86rem; font-weight:750; }
    .member-meta { max-width:175px; color:#64748b; font-size:.72rem; }
    .status-pill { display:inline-block; min-width:78px; padding:.38rem .65rem; border-radius:7px; text-align:center; font-size:.72rem; font-weight:700; }
    .status-pending { background:#fff1cf; color:#b76b00; }
    .status-accepted { background:#dff7e9; color:#087a42; }
    .status-rejected { background:#ffe2e5; color:#c82938; }
    .status-removed, .status-cancelled { background:#edf0f4; color:#52606f; }
    .request-detail-header { padding:1rem; border-bottom:1px solid #e6e9ef; font-weight:800; }
    .request-details-panel { width:min(430px, 100vw) !important; }
    .detail-person { padding:1rem; border:1px solid #e6e9ef; border-radius:12px; }
    .detail-avatar { width:64px; height:64px; border-radius:50%; object-fit:cover; background:#f1f5f9; }
    .detail-arrow { color:#64748b; font-size:1.35rem; text-align:center; padding:.45rem; }
    .request-empty { padding:3rem 1rem !important; text-align:center; color:#64748b; }
    @media print { .sidebar, .top-header, .request-filter, .request-tabs, .request-detail, .btn { display:none !important; } .main-content { margin-left:0 !important; } }
</style>
@endpush

@section('content')
@php
    $requestTabs = [
        ['Overview', 'fa-table-cells-large', '', ''],
        ['Sent Requests', 'fa-paper-plane', '', 'sent'],
        ['Received Requests', 'fa-download', '', 'received'],
        ['Accepted', 'fa-circle-check', 'accepted', ''],
        ['Rejected', 'fa-circle-xmark', 'rejected', ''],
        ['Removed', 'fa-ban', 'removed', ''],
    ];
@endphp
<div class="container-fluid request-page">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div><h3 class="fw-bold mb-1">Matrimony Request Analysis</h3><div class="text-muted small">Track who sent a request, who received it, and the current response.</div></div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.matrimony.connections.export', request()->query()) }}"><i class="fas fa-download me-1"></i> Export</a>
    </div>

    <div class="request-shell mb-3">
        <nav class="request-tabs">
            @foreach($requestTabs as [$label, $icon, $status, $direction])
                <a class="nav-link {{ request('status', '') === $status && request('direction', '') === $direction ? 'active' : '' }}" href="{{ route('admin.matrimony.connections', array_filter(array_merge(request()->except('page', 'status', 'direction'), ['status' => $status, 'direction' => $direction]), fn($value) => $value !== null && $value !== '')) }}"><i class="fas {{ $icon }} me-2"></i>{{ $label }}</a>
            @endforeach
        </nav>

        <div class="p-3"><div class="row g-2">
            @foreach([
                ['total','Total Requests','fa-paper-plane',''], ['sent','Sent Requests','fa-arrow-up-right-from-square',''],
                ['received','Received Requests','fa-download',''], ['accepted','Accepted','fa-check','success'],
                ['rejected','Rejected','fa-xmark','danger'], ['removed','Removed','fa-ban','muted'],
            ] as [$key,$label,$icon,$class])
                <div class="col-6 col-lg-4 col-xxl-2"><div class="request-stat {{ $class }}"><div class="request-stat-icon"><i class="fas {{ $icon }}"></i></div><div><div class="request-stat-value">{{ number_format($stats[$key] ?? 0) }}</div><div class="request-stat-label">{{ $label }}</div></div></div></div>
            @endforeach
        </div></div>

        <form class="request-filter px-3 pb-3" method="GET" action="{{ route('admin.matrimony.connections') }}">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <input type="hidden" name="direction" value="{{ request('direction') }}">
            <div class="row g-2 align-items-end">
                <div class="col-sm-6 col-xl-3"><label for="requestSearch">Search sender or receiver</label><input id="requestSearch" name="search" value="{{ request('search') }}" class="form-control" placeholder="Name, mobile or email"></div>
                <div class="col-sm-6 col-xl-2"><label for="fromMember">From Member</label><select id="fromMember" name="from_member" class="form-select"><option value="">All Members</option>@foreach($members as $member)<option value="{{ $member->id }}" @selected((string)request('from_member') === (string)$member->id)>{{ $member->matrimonyProfile?->candidate_name ?: $member->name }} {{ $member->phone ? '('.$member->phone.')' : '' }}</option>@endforeach</select></div>
                <div class="col-sm-6 col-xl-2"><label for="toMember">To Member</label><select id="toMember" name="to_member" class="form-select"><option value="">All Members</option>@foreach($members as $member)<option value="{{ $member->id }}" @selected((string)request('to_member') === (string)$member->id)>{{ $member->matrimonyProfile?->candidate_name ?: $member->name }} {{ $member->phone ? '('.$member->phone.')' : '' }}</option>@endforeach</select></div>
                <div class="col-6 col-sm-3 col-xl-1"><label>Sender</label><select name="sender_gender" class="form-select"><option value="">Gender</option><option value="male" @selected(request('sender_gender') === 'male')>Male</option><option value="female" @selected(request('sender_gender') === 'female')>Female</option></select></div>
                <div class="col-6 col-sm-3 col-xl-1"><label>Receiver</label><select name="receiver_gender" class="form-select"><option value="">Gender</option><option value="male" @selected(request('receiver_gender') === 'male')>Male</option><option value="female" @selected(request('receiver_gender') === 'female')>Female</option></select></div>
                <div class="col-6 col-sm-3 col-xl-1"><label>Age Range</label><select name="age_range" class="form-select"><option value="">All</option>@foreach(['18-25','26-30','31-35','36-45','46-60'] as $range)<option value="{{ $range }}" @selected(request('age_range') === $range)>{{ $range }}</option>@endforeach</select></div>
                <div class="col-6 col-sm-3 col-xl-1"><button class="btn request-search-btn w-100"><i class="fas fa-search me-1"></i> Search</button></div>
                <div class="col-12 col-xl-1"><a class="btn btn-light border w-100" href="{{ route('admin.matrimony.connections') }}">Reset</a></div>
            </div>
            <div class="row g-2 mt-1">
                <div class="col-sm-3 col-xl-2"><label for="dateFrom">From Date</label><input id="dateFrom" type="date" name="date_from" value="{{ request('date_from') }}" class="form-control"></div>
                <div class="col-sm-3 col-xl-2"><label for="dateTo">To Date</label><input id="dateTo" type="date" name="date_to" value="{{ request('date_to') }}" class="form-control"></div>
            </div>
        </form>
    </div>

    <div class="request-shell overflow-hidden">
            <div class="table-responsive"><table class="table request-table align-middle mb-0">
                <thead><tr><th>#</th><th>Sender</th><th></th><th>Receiver</th><th>Sent On</th><th>Status</th><th>Responded On</th><th>Action</th></tr></thead>
                <tbody>
                @forelse($connections as $connection)
                    @php
                        $senderProfile = $connection->sender?->matrimonyProfile;
                        $receiverProfile = $connection->receiver?->matrimonyProfile;
                        $senderName = $senderProfile?->candidate_name ?: ($connection->sender?->name ?? 'Deleted user');
                        $receiverName = $receiverProfile?->candidate_name ?: ($connection->receiver?->name ?? 'Deleted user');
                        $senderLocation = collect([$senderProfile?->location_details['city'] ?? null, $senderProfile?->location_details['state'] ?? null])->filter()->implode(', ');
                        $receiverLocation = collect([$receiverProfile?->location_details['city'] ?? null, $receiverProfile?->location_details['state'] ?? null])->filter()->implode(', ');
                        $senderPhoto = $connection->sender?->photo ? asset('storage/'.$connection->sender->photo) : asset('default-avatar.png');
                        $receiverPhoto = $connection->receiver?->photo ? asset('storage/'.$connection->receiver->photo) : asset('default-avatar.png');
                        $details = [
                            'sender_name' => $senderName, 'sender_phone' => $connection->sender?->phone ?: '-', 'sender_photo' => $senderPhoto,
                            'sender_meta' => collect([$senderProfile?->age ? $senderProfile->age.' Years' : null, $senderLocation])->filter()->implode(' · '),
                            'sender_profile_url' => $senderProfile ? route('admin.matrimony.show', $senderProfile->id) : '',
                            'receiver_name' => $receiverName, 'receiver_phone' => $connection->receiver?->phone ?: '-', 'receiver_photo' => $receiverPhoto,
                            'receiver_meta' => collect([$receiverProfile?->age ? $receiverProfile->age.' Years' : null, $receiverLocation])->filter()->implode(' · '),
                            'receiver_profile_url' => $receiverProfile ? route('admin.matrimony.show', $receiverProfile->id) : '',
                            'sent_on' => $connection->created_at?->format('d M Y, h:i A') ?: '-', 'responded_on' => $connection->responded_at?->format('d M Y, h:i A') ?: '-',
                            'status' => ucfirst($connection->status), 'status_key' => $connection->status, 'message' => $connection->message ?: '-', 'response' => $connection->response_message ?: '-',
                        ];
                    @endphp
                    <tr>
                        <td>{{ $connections->firstItem() + $loop->index }}</td>
                        <td><div class="member-cell"><img src="{{ $senderPhoto }}" class="member-avatar" alt=""><div><div class="member-name">{{ $senderName }}{{ $senderProfile?->age ? ' ('.$senderProfile->age.')' : '' }}</div><div class="member-meta">{{ $connection->sender?->phone ?: ($senderLocation ?: '-') }}</div></div></div></td>
                        <td class="text-muted"><i class="fas fa-arrow-right"></i></td>
                        <td><div class="member-cell"><img src="{{ $receiverPhoto }}" class="member-avatar" alt=""><div><div class="member-name">{{ $receiverName }}{{ $receiverProfile?->age ? ' ('.$receiverProfile->age.')' : '' }}</div><div class="member-meta">{{ $connection->receiver?->phone ?: ($receiverLocation ?: '-') }}</div></div></div></td>
                        <td class="text-nowrap small">{{ $connection->created_at?->format('d M Y, h:i A') }}</td>
                        <td><span class="status-pill status-{{ $connection->status }}">{{ ucfirst($connection->status) }}</span></td>
                        <td class="text-nowrap small">{{ $connection->responded_at?->format('d M Y, h:i A') ?? '-' }}</td>
                        <td><button type="button" class="btn btn-sm btn-outline-primary view-request" data-bs-toggle="offcanvas" data-bs-target="#requestDetailsPanel" aria-controls="requestDetailsPanel" data-details="{{ json_encode($details) }}"><i class="fas fa-eye me-1"></i> View</button></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="request-empty"><i class="fas fa-magnifying-glass fa-2x mb-3 d-block"></i>No matrimony requests match these filters.</td></tr>
                @endforelse
                </tbody>
            </table></div>
            @if($connections->hasPages())<div class="p-3 border-top">{{ $connections->links('pagination::bootstrap-5') }}</div>@endif
    </div>

    <div class="offcanvas offcanvas-end request-details-panel" tabindex="-1" id="requestDetailsPanel" aria-labelledby="requestDetailsTitle">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold" id="requestDetailsTitle">Request Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
                <div class="detail-person"><div class="small fw-bold mb-2">Sender (Sent Request)</div><div class="d-flex align-items-center gap-3"><img id="detailSenderPhoto" class="detail-avatar" alt=""><div><div id="detailSenderName" class="fw-bold"></div><div id="detailSenderPhone" class="small text-muted"></div><div id="detailSenderMeta" class="small text-muted"></div></div></div><a id="detailSenderLink" class="btn btn-sm btn-light border w-100 mt-3">View Profile</a></div>
                <div class="detail-arrow"><i class="fas fa-arrow-down"></i></div>
                <div class="detail-person"><div class="small fw-bold text-danger mb-2">Receiver (Received Request)</div><div class="d-flex align-items-center gap-3"><img id="detailReceiverPhoto" class="detail-avatar" alt=""><div><div id="detailReceiverName" class="fw-bold"></div><div id="detailReceiverPhone" class="small text-muted"></div><div id="detailReceiverMeta" class="small text-muted"></div></div></div><a id="detailReceiverLink" class="btn btn-sm btn-light border w-100 mt-3">View Profile</a></div>
                <div class="bg-light rounded-3 p-3 mt-3 small"><div class="d-flex justify-content-between mb-2"><span class="text-muted">Sent On</span><strong id="detailSent"></strong></div><div class="d-flex justify-content-between mb-2"><span class="text-muted">Responded On</span><strong id="detailResponded"></strong></div><div class="d-flex justify-content-between mb-2"><span class="text-muted">Status</span><span id="detailStatus"></span></div><div class="mb-2"><span class="text-muted d-block">Message</span><span id="detailMessage"></span></div><div><span class="text-muted d-block">Response</span><span id="detailResponse"></span></div></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const buttons = [...document.querySelectorAll('.view-request')];
    const setText = (id, value) => { document.getElementById(id).textContent = value || '-'; };
    function showDetails(button) {
        const data = JSON.parse(button.dataset.details);
        document.getElementById('detailSenderPhoto').src = data.sender_photo; document.getElementById('detailReceiverPhoto').src = data.receiver_photo;
        setText('detailSenderName', data.sender_name); setText('detailSenderPhone', data.sender_phone); setText('detailSenderMeta', data.sender_meta);
        setText('detailReceiverName', data.receiver_name); setText('detailReceiverPhone', data.receiver_phone); setText('detailReceiverMeta', data.receiver_meta);
        setText('detailSent', data.sent_on); setText('detailResponded', data.responded_on); setText('detailMessage', data.message); setText('detailResponse', data.response);
        const status = document.getElementById('detailStatus'); status.textContent = data.status; status.className = 'status-pill status-' + data.status_key;
        [['detailSenderLink', data.sender_profile_url], ['detailReceiverLink', data.receiver_profile_url]].forEach(([id, url]) => { const link = document.getElementById(id); link.href = url || '#'; link.style.display = url ? '' : 'none'; });
        buttons.forEach(item => item.classList.remove('active')); button.classList.add('active');
    }
    buttons.forEach(button => button.addEventListener('click', () => showDetails(button)));
});
</script>
@endpush
