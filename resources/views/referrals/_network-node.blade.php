<div class="border rounded-4 p-3 h-100">
        <div class="d-flex justify-content-between gap-2"><div><strong>{{ $node['name'] }}</strong><div class="small text-muted">{{ $node['referral_code'] }} · Level {{ $node['level'] }}</div></div><span class="badge text-bg-light border align-self-start">{{ $node['commission_points'] }} points</span></div>
        <div class="small text-muted mt-2"><i class="fa-solid fa-location-dot me-1"></i>{{ collect($node['location'])->filter()->implode(', ') ?: 'Location not allocated' }}</div>
        @if($node['children'] !== [])<div class="mt-3 border-start ps-2">@foreach($node['children'] as $child)<div class="mb-2">@include('referrals._network-node',['node'=>$child])</div>@endforeach</div>@endif
</div>
