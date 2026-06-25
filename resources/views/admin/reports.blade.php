@extends('admin.layouts.app')

@section('title', 'Reports')
@section('page-title', 'PDF Reports')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body bg-light p-4 rounded">
                <h4 class="text-primary fw-bold mb-2">System Reports Dashboard</h4>
                <p class="text-muted mb-0">Generate and download comprehensive PDF reports of the platform's key operational modules.</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Users Report Card -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body d-flex flex-column justify-content-between p-4">
                <div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary-subtle text-primary p-3 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: rgba(13, 110, 253, 0.1);">
                            <i class="fas fa-users fa-lg"></i>
                        </div>
                        <h5 class="card-title fw-bold mb-0">Users Registration Report</h5>
                    </div>
                    <p class="card-text text-muted">Generate a full audit report of registered platform users, including email addresses, phone numbers, caste verification status, and registration timelines.</p>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.reports.download', 'users') }}" class="btn btn-primary d-inline-flex align-items-center">
                        <i class="fas fa-file-pdf me-2" style="margin-right: 8px;"></i> Download PDF Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Businesses Report Card -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body d-flex flex-column justify-content-between p-4">
                <div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success-subtle text-success p-3 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: rgba(25, 135, 84, 0.1);">
                            <i class="fas fa-building fa-lg"></i>
                        </div>
                        <h5 class="card-title fw-bold mb-0">Business Directory Report</h5>
                    </div>
                    <p class="card-text text-muted">Generate a detailed summary of all registered businesses, active promotions, business categories, verification status, and owner information.</p>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.reports.download', 'businesses') }}" class="btn btn-success d-inline-flex align-items-center text-white">
                        <i class="fas fa-file-pdf me-2" style="margin-right: 8px;"></i> Download PDF Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Matrimony Profiles Report Card -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body d-flex flex-column justify-content-between p-4">
                <div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-info-subtle text-info p-3 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: rgba(13, 202, 240, 0.1);">
                            <i class="fas fa-user-friends fa-lg"></i>
                        </div>
                        <h5 class="card-title fw-bold mb-0">Matrimonial Profiles Report</h5>
                    </div>
                    <p class="card-text text-muted">Generate a compilation of matrimonial profiles, showing age distribution, sub-caste divisions, approval status, and matchmaking participation metrics.</p>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.reports.download', 'matrimony') }}" class="btn btn-info d-inline-flex align-items-center text-white">
                        <i class="fas fa-file-pdf me-2" style="margin-right: 8px;"></i> Download PDF Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Payments Report Card -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body d-flex flex-column justify-content-between p-4">
                <div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-warning-subtle text-warning p-3 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: rgba(255, 193, 7, 0.1);">
                            <i class="fas fa-credit-card fa-lg"></i>
                        </div>
                        <h5 class="card-title fw-bold mb-0">Payments & Revenue Report</h5>
                    </div>
                    <p class="card-text text-muted">Generate a financial statement showing platform revenue trends, listing business subscription fees, matchmaking premium payments, and transaction histories.</p>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.reports.download', 'payments') }}" class="btn btn-warning d-inline-flex align-items-center text-dark fw-bold">
                        <i class="fas fa-file-pdf me-2" style="margin-right: 8px;"></i> Download PDF Report
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
