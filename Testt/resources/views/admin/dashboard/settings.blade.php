@extends('admin.layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">System Settings</h1>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Application Settings</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.settings.update') }}">
                        @csrf
                        
                        <!-- General Settings -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="app_name">Application Name</label>
                                    <input type="text" class="form-control @error('app_name') is-invalid @enderror" 
                                           id="app_name" name="app_name" value="{{ old('app_name', $settings['app_name']) }}">
                                    @error('app_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="support_email">Support Email</label>
                                    <input type="email" class="form-control @error('support_email') is-invalid @enderror" 
                                           id="support_email" name="support_email" value="{{ old('support_email', $settings['support_email']) }}">
                                    @error('support_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Payment Settings -->
                        <h5 class="mt-4 mb-3 text-primary">Payment Settings</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="business_registration_fee">Business Registration Fee (₹)</label>
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('business_registration_fee') is-invalid @enderror" 
                                           id="business_registration_fee" name="business_registration_fee" 
                                           value="{{ old('business_registration_fee', $settings['business_registration_fee']) }}">
                                    @error('business_registration_fee')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="matrimony_profile_fee">Matrimony Profile Fee (₹)</label>
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('matrimony_profile_fee') is-invalid @enderror" 
                                           id="matrimony_profile_fee" name="matrimony_profile_fee" 
                                           value="{{ old('matrimony_profile_fee', $settings['matrimony_profile_fee']) }}">
                                    @error('matrimony_profile_fee')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Business Settings -->
                        <h5 class="mt-4 mb-3 text-success">Business Settings</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="trial_period_days">Trial Period (Days)</label>
                                    <input type="number" min="0" 
                                           class="form-control @error('trial_period_days') is-invalid @enderror" 
                                           id="trial_period_days" name="trial_period_days" 
                                           value="{{ old('trial_period_days', $settings['trial_period_days']) }}">
                                    @error('trial_period_days')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Number of days for free trial period</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="max_job_postings">Maximum Job Postings</label>
                                    <input type="number" min="0" 
                                           class="form-control @error('max_job_postings') is-invalid @enderror" 
                                           id="max_job_postings" name="max_job_postings" 
                                           value="{{ old('max_job_postings', $settings['max_job_postings']) }}">
                                    @error('max_job_postings')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Set to 0 for unlimited job postings</small>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Gateway Settings -->
                        <h5 class="mt-4 mb-3 text-warning">Payment Gateway Settings</h5>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="razorpay_key_id">Razorpay Key ID</label>
                                    <input type="text" class="form-control" id="razorpay_key_id" 
                                           value="{{ $settings['razorpay_key_id'] ? str_repeat('*', strlen($settings['razorpay_key_id']) - 4) . substr($settings['razorpay_key_id'], -4) : 'Not configured' }}" 
                                           readonly>
                                    <small class="form-text text-muted">Configure in .env file: RAZORPAY_KEY_ID</small>
                                </div>
                            </div>
                        </div>

                        <!-- System Settings -->
                        <h5 class="mt-4 mb-3 text-info">System Settings</h5>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="maintenance_mode" 
                                               name="maintenance_mode" value="1" 
                                               {{ old('maintenance_mode', $settings['maintenance_mode']) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="maintenance_mode">
                                            Maintenance Mode
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Enable to put the application in maintenance mode</small>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Settings
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- System Information -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-secondary">System Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td><strong>Laravel Version:</strong></td>
                                    <td>{{ app()->version() }}</td>
                                </tr>
                                <tr>
                                    <td><strong>PHP Version:</strong></td>
                                    <td>{{ PHP_VERSION }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Environment:</strong></td>
                                    <td>
                                        <span class="badge badge-{{ app()->environment() === 'production' ? 'success' : 'warning' }}">
                                            {{ ucfirst(app()->environment()) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td><strong>Database:</strong></td>
                                    <td>{{ config('database.default') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Cache Driver:</strong></td>
                                    <td>{{ config('cache.default') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Queue Driver:</strong></td>
                                    <td>{{ config('queue.default') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">System Actions</h6>
                    <small class="text-muted">Use these actions to maintain and optimize your application</small>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <form method="POST" action="{{ route('admin.system.clear-cache') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-info btn-block" onclick="return confirm('Are you sure you want to clear the application cache?')">
                                    <i class="fas fa-broom"></i><br>
                                    Clear Cache
                                </button>
                            </form>
                        </div>
                        <div class="col-md-3">
                            <form method="POST" action="{{ route('admin.system.optimize') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-warning btn-block" onclick="return confirm('Are you sure you want to optimize the application?')">
                                    <i class="fas fa-rocket"></i><br>
                                    Optimize App
                                </button>
                            </form>
                        </div>
                        <div class="col-md-3">
                            <form method="POST" action="{{ route('admin.system.backup-database') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-success btn-block" onclick="return confirm('Are you sure you want to create a database backup?')">
                                    <i class="fas fa-database"></i><br>
                                    Backup Database
                                </button>
                            </form>
                        </div>
                        <div class="col-md-3">
                            <form method="POST" action="{{ route('admin.system.clear-logs') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary btn-block" onclick="return confirm('Are you sure you want to clear application logs?')">
                                    <i class="fas fa-file-alt"></i><br>
                                    Clear Logs
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <form method="POST" action="{{ route('admin.system.run-migrations') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary btn-block" onclick="return confirm('Are you sure you want to run database migrations?')">
                                    <i class="fas fa-database"></i><br>
                                    Run Migrations
                                </button>
                            </form>
                        </div>
                        <div class="col-md-3">
                            <form method="POST" action="{{ route('admin.system.generate-key') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-block" onclick="return confirm('Are you sure you want to generate a new application key? This will log out all users.')">
                                    <i class="fas fa-key"></i><br>
                                    Generate Key
                                </button>
                            </form>
                        </div>
                        <div class="col-md-3">
                            <form method="POST" action="{{ route('admin.system.storage-link') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-info btn-block" onclick="return confirm('Are you sure you want to create storage link?')">
                                    <i class="fas fa-link"></i><br>
                                    Storage Link
                                </button>
                            </form>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-dark btn-block" onclick="getSystemInfo()">
                                <i class="fas fa-info-circle"></i><br>
                                System Info
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function getSystemInfo() {
    // Show loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i><br>Loading...';
    button.disabled = true;
    
    fetch('{{ route("admin.system.info") }}')
        .then(response => response.json())
        .then(data => {
            // Create modal content
            const modalContent = `
                <div class="modal fade" id="systemInfoModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">System Information</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-primary">Application</h6>
                                        <table class="table table-sm">
                                            <tr><td><strong>PHP Version:</strong></td><td>${data.php_version}</td></tr>
                                            <tr><td><strong>Laravel Version:</strong></td><td>${data.laravel_version}</td></tr>
                                            <tr><td><strong>Server Software:</strong></td><td>${data.server_software}</td></tr>
                                            <tr><td><strong>Database Version:</strong></td><td>${data.database_version}</td></tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-success">System Resources</h6>
                                        <table class="table table-sm">
                                            <tr><td><strong>Memory Limit:</strong></td><td>${data.memory_limit}</td></tr>
                                            <tr><td><strong>Max Execution Time:</strong></td><td>${data.max_execution_time}s</td></tr>
                                            <tr><td><strong>Upload Max Size:</strong></td><td>${data.upload_max_filesize}</td></tr>
                                            <tr><td><strong>Disk Free Space:</strong></td><td>${data.disk_free_space}</td></tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Remove existing modal if any
            $('#systemInfoModal').remove();
            
            // Add modal to body and show
            $('body').append(modalContent);
            $('#systemInfoModal').modal('show');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to fetch system information.');
        })
        .finally(() => {
            // Restore button state
            button.innerHTML = originalText;
            button.disabled = false;
        });
}
</script>
@endpush