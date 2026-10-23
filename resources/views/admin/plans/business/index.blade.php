@extends('admin.layouts.app')

@section('title', 'Business Plans')
@section('page-title', 'Manage Business Plans')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Business Plans</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.plans.business.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Add Plan
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Company Type</th>
                                    <th>Duration (yrs)</th>
                                    <th>Price (₹)</th>
                                    <th>Active</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($plans as $k => $plan)
                                    <tr>
                                        <td>#{{ $k+1 }}</td>
                                        <td>{{ $plan->company_type }}</td>
                                        <td class="text-center">{{ $plan->duration_years }}</td>
                                        <td>₹{{ number_format($plan->price, 2) }}</td>
                                        <td>{!! $plan->active ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' !!}</td>
                                        <td>
                                            <a href="{{ route('admin.plans.business.edit', $plan->id) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('admin.plans.business.destroy', $plan->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger" onclick="return confirm('Remove plan?')"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
