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

<!-- Date Range Filter Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden; background: linear-gradient(145deg, #ffffff, #f1f3f7); border: 1px solid #e2e8f0;">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3 text-secondary d-flex align-items-center" style="font-size: 1.05rem;">
                    <i class="fas fa-calendar-alt text-primary" style="margin-right: 8px;"></i> Filter Reports by Date Range (Optional)
                </h5>
                <div class="row align-items-end">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label for="start_date" class="form-label small fw-bold text-muted mb-2">Start Date</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-calendar-day"></i></span>
                            <input type="date" id="start_date" class="form-control border-start-0" style="border-radius: 0 6px 6px 0;">
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label for="end_date" class="form-label small fw-bold text-muted mb-2">End Date</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-calendar-day"></i></span>
                            <input type="date" id="end_date" class="form-control border-start-0" style="border-radius: 0 6px 6px 0;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="button" id="clear_dates" class="btn btn-outline-secondary w-100 d-inline-flex align-items-center justify-content-center transition-all" style="border-radius: 6px; height: 38px; font-weight: 500;">
                            <i class="fas fa-undo" style="margin-right: 8px;"></i> Reset Filter
                        </button>
                    </div>
                </div>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const clearBtn = document.getElementById('clear_dates');
    const downloadLinks = document.querySelectorAll('a[href*="/admin/reports/download"]');

    // Store original hrefs
    downloadLinks.forEach(link => {
        link.dataset.originalHref = link.getAttribute('href');
    });

    function updateLinks() {
        const startVal = startDateInput.value;
        const endVal = endDateInput.value;

        // Validation: end date cannot be before start date
        if (startVal && endVal && new Date(endVal) < new Date(startVal)) {
            alert('End date cannot be earlier than start date.');
            endDateInput.value = '';
            return;
        }

        downloadLinks.forEach(link => {
            let href = link.dataset.originalHref;
            const params = [];
            if (startVal) params.push(`start_date=${startVal}`);
            if (endVal) params.push(`end_date=${endVal}`);
            
            if (params.length > 0) {
                href += '?' + params.join('&');
            }
            link.setAttribute('href', href);
        });
    }

    startDateInput.addEventListener('change', updateLinks);
    endDateInput.addEventListener('change', updateLinks);

    clearBtn.addEventListener('click', function () {
        startDateInput.value = '';
        endDateInput.value = '';
        updateLinks();
    });
});
</script>
@endpush
