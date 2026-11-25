@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
        <a href="{{ route('dashboard.business.show', $item->business_id) }}" class="btn btn-light btn-sm rounded-3">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Business
        </a>
        @if(Auth::id() === $item->business->user_id)
            <a href="{{ route('dashboard.business.index') }}" class="btn btn-outline-primary btn-sm rounded-3">Manage Business</a>
        @endif
    </div>

    <div class="glass-card bg-white border shadow-sm overflow-hidden p-0">
        <div class="row g-0">
            <div class="col-lg-5 bg-light d-flex align-items-center justify-content-center" style="min-height: 420px;">
                @if($item->image_path)
                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" class="w-100 h-100" style="min-height: 420px; max-height: 560px; object-fit: cover;">
                @else
                    <div class="text-center text-secondary p-5">
                        <i class="fa-solid {{ $icon }} fa-5x mb-3"></i>
                        <p class="mb-0">No {{ strtolower($itemType) }} image available</p>
                    </div>
                @endif
            </div>
            <div class="col-lg-7 p-4 p-lg-5 text-start">
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 mb-3">{{ $itemType }}</span>
                <h2 class="fw-bold text-dark mb-2">{{ $item->name }}</h2>
                <a href="{{ route('dashboard.business.show', $item->business_id) }}" class="text-decoration-none text-secondary d-inline-block mb-4">
                    <i class="fa-solid fa-store me-1 text-primary"></i>{{ $item->business->business_name }}
                    @if($item->business->category)
                        · {{ $item->business->category->name }}
                    @endif
                </a>

                <div class="fs-3 fw-bold text-primary mb-4">₹{{ number_format($item->cost ?? 0, 2) }}</div>

                <h6 class="fw-bold text-dark">Description</h6>
                <p class="text-secondary mb-4" style="white-space: pre-line; line-height: 1.8;">{{ $item->description }}</p>

                <div class="border-top pt-4 small text-secondary">
                    @if($item->business->contact_phone)
                        <div class="mb-2"><i class="fa-solid fa-phone text-primary me-2"></i>{{ $item->business->contact_phone }}</div>
                    @endif
                    @if($item->business->contact_email)
                        <div class="mb-2"><i class="fa-solid fa-envelope text-primary me-2"></i>{{ $item->business->contact_email }}</div>
                    @endif
                    @if($item->business->address)
                        <div><i class="fa-solid fa-location-dot text-primary me-2"></i>{{ $item->business->address }}, {{ $item->business->city }}, {{ $item->business->state }}</div>
                    @endif
                </div>

                @if(Auth::id() !== $item->business->user_id)
                    <div class="border-top mt-4 pt-4">
                        <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-paper-plane text-primary me-2"></i>Send an enquiry</h5>
                        <form method="POST" action="{{ route('dashboard.business.enquiries.store') }}">
                            @csrf
                            <input type="hidden" name="{{ $itemType === 'Product' ? 'product_id' : 'service_id' }}" value="{{ $item->id }}">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Expected price</label>
                                <div class="input-group"><span class="input-group-text">₹</span><input type="number" name="price" min="0" step="0.01" value="{{ old('price', $item->cost) }}" class="form-control"></div>
                                @error('price')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Description / requirement <span class="text-danger">*</span></label>
                                <textarea name="description" rows="4" maxlength="2000" class="form-control" required placeholder="Describe quantity, requirements, preferred date, or any questions.">{{ old('description') }}</textarea>
                                @error('description')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <button class="btn btn-primary rounded-3 px-4"><i class="fa-solid fa-paper-plane me-2"></i>Send Enquiry</button>
                            <a href="{{ route('dashboard.business.enquiries.index') }}" class="btn btn-light rounded-3 ms-2">My Enquiries</a>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
