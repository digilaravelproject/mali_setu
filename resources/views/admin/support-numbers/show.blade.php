@extends('admin.layouts.app')

@section('title', ucfirst($slot).' Support Number')
@section('page-title', ucfirst($slot).' Support Number')

@section('content')
<div class="container-fluid"><div class="card border-0 shadow-sm" style="max-width:680px"><div class="card-body p-4">
    <form method="POST" action="{{ route('admin.support-numbers.update', $slot) }}">
        @csrf @method('PUT')
        <label for="number" class="form-label fw-bold">{{ ucfirst($slot) }} Support Number</label>
        <input id="number" name="number" class="form-control form-control-lg" value="{{ old('number', $number) }}" placeholder="+919876543210" required @readonly(!request()->boolean('edit'))>
        @error('number')<div class="text-danger mt-2">{{ $message }}</div>@enderror
        <div class="d-flex gap-2 mt-4">
            <a href="{{ route('admin.support-numbers.index') }}" class="btn btn-secondary">Back</a>
            @if(request()->boolean('edit'))<button class="btn btn-primary"><i class="fas fa-save me-1"></i> Save</button>@else<a href="{{ route('admin.support-numbers.show', [$slot, 'edit' => 1]) }}" class="btn btn-warning"><i class="fas fa-edit me-1"></i> Update</a>@endif
        </div>
    </form>
</div></div></div>
@endsection
