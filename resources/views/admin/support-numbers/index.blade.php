@extends('admin.layouts.app')

@section('title', 'Support Numbers')
@section('page-title', 'Support Numbers')

@section('content')
<div class="container-fluid">
    <div class="row g-4">
        @foreach(['primary' => 'Primary Support Number', 'secondary' => 'Secondary Support Number'] as $slot => $label)
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div><div class="text-muted small text-uppercase fw-bold">{{ $label }}</div><div class="h3 mt-3 mb-0">{{ $numbers[$slot] ?: 'Not configured' }}</div></div>
                            <div class="rounded-circle bg-light p-3 text-primary"><i class="fas fa-headset fa-lg"></i></div>
                        </div>
                    </div>
                    <div class="card-footer bg-white d-flex gap-2">
                        <a href="{{ route('admin.support-numbers.show', $slot) }}" class="btn btn-outline-primary"><i class="fas fa-eye me-1"></i> View</a>
                        <a href="{{ route('admin.support-numbers.show', [$slot, 'edit' => 1]) }}" class="btn btn-warning"><i class="fas fa-edit me-1"></i> Update</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
