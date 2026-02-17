@extends('admin.layouts.app')

@section('title', 'Settings')
@section('page-title', 'System Settings')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">System Settings</h2>
            <button class="btn btn-primary" onclick="saveAllSettings()">
                <i class="fas fa-save me-2"></i>Save All Changes
            </button>
        </div>
    </div>
</div>

<div class="row">
    <!-- General Settings -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-cog me-2"></i>
                    General Settings
                </h5>
            </div>
            <div class="card-body">
                <form id="generalSettingsForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Application Name</label>
                        <input type="text" class="form-control" name="app_name" 
                               value="{{ $settings['app_name'] ?? 'Mali Setu' }}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Application Description</label>
                        <textarea class="form-control" name="app_description" rows="3">{{ $settings['app_description'] ?? 'Community platform connecting Mali caste members for business, matrimony, and social causes.' }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Contact Email</label>
                        <input type="email" class="form-control" name="contact_email" 
                               value="{{ $settings['contact_email'] ?? 'admin@malisetu.com' }}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Contact Phone</label>
                        <input type="tel" class="form-control" name="contact_phone" 
                               value="{{ $settings['contact_phone'] ?? '+91 9876543210' }}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Maintenance Mode</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="maintenance_mode" 
                                   {{ ($settings['maintenance_mode'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label">Enable maintenance mode</label>
                        </div>
                        <small class="text-muted">When enabled, only admins can access the application</small>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Payment Settings -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-credit-card me-2"></i>
                    Payment Settings
                </h5>
            </div>
            <div class="card-body">
                <form id="paymentSettingsForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Razorpay Key ID</label>
                        <input type="text" class="form-control" name="razorpay_key" 
                               value="{{ $settings['razorpay_key'] ?? '' }}" placeholder="rzp_test_...">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Razorpay Secret</label>
                        <input type="password" class="form-control" name="razorpay_secret" 
                               value="{{ $settings['razorpay_secret'] ?? '' }}" placeholder="Enter secret key">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Business Registration Fee (₹)</label>
                        <input type="number" class="form-control" name="business_registration_fee" 
                               value="{{ $settings['business_registration_fee'] ?? 500 }}" min="0">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Matrimony Profile Fee (₹)</label>
                        <input type="number" class="form-control" name="matrimony_profile_fee" 
                               value="{{ $settings['matrimony_profile_fee'] ?? 300 }}" min="0">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Payment Gateway Mode</label>
                        <select class="form-select" name="payment_mode">
                            <option value="test" {{ ($settings['payment_mode'] ?? 'test') == 'test' ? 'selected' : '' }}>Test Mode</option>
                            <option value="live" {{ ($settings['payment_mode'] ?? 'test') == 'live' ? 'selected' : '' }}>Live Mode</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Verification Settings -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-certificate me-2"></i>
                    Verification Settings
                </h5>
            </div>
            <div class="card-body">
                <form id="verificationSettingsForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Auto-approve Verifications</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="auto_approve_verification" 
                                   {{ ($settings['auto_approve_verification'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label">Enable auto-approval</label>
                        </div>
                        <small class="text-muted">Automatically approve caste certificate verifications</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Verification Processing Time (hours)</label>
                        <input type="number" class="form-control" name="verification_processing_time" 
                               value="{{ $settings['verification_processing_time'] ?? 24 }}" min="1" max="168">
                        <small class="text-muted">Expected time to process verification requests</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Required Documents</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="require_caste_certificate" 
                                   {{ ($settings['require_caste_certificate'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label">Caste Certificate</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="require_photo_id" 
                                   {{ ($settings['require_photo_id'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label">Photo ID Proof</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="require_address_proof" 
                                   {{ ($settings['require_address_proof'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label">Address Proof</label>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Notification Settings -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bell me-2"></i>
                    Notification Settings
                </h5>
            </div>
            <div class="card-body">
                <form id="notificationSettingsForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email Notifications</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="email_new_registration" 
                                   {{ ($settings['email_new_registration'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label">New user registrations</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="email_verification_requests" 
                                   {{ ($settings['email_verification_requests'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label">Verification requests</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="email_payment_notifications" 
                                   {{ ($settings['email_payment_notifications'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label">Payment notifications</label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">SMS Notifications</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="sms_enabled" 
                                   {{ ($settings['sms_enabled'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label">Enable SMS notifications</label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Push Notifications</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="push_enabled" 
                                   {{ ($settings['push_enabled'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label">Enable push notifications</label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Firebase Server Key</label>
                        <input type="password" class="form-control" name="firebase_server_key" 
                               value="{{ $settings['firebase_server_key'] ?? '' }}" 
                               placeholder="Enter Firebase server key">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Security Settings -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-shield-alt me-2"></i>
                    Security Settings
                </h5>
            </div>
            <div class="card-body">
                <form id="securitySettingsForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Session Timeout (minutes)</label>
                        <input type="number" class="form-control" name="session_timeout" 
                               value="{{ $settings['session_timeout'] ?? 120 }}" min="15" max="1440">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Max Login Attempts</label>
                        <input type="number" class="form-control" name="max_login_attempts" 
                               value="{{ $settings['max_login_attempts'] ?? 5 }}" min="3" max="10">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Account Lockout Duration (minutes)</label>
                        <input type="number" class="form-control" name="lockout_duration" 
                               value="{{ $settings['lockout_duration'] ?? 30 }}" min="5" max="1440">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Two-Factor Authentication</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="two_factor_enabled" 
                                   {{ ($settings['two_factor_enabled'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label">Enable 2FA for admin accounts</label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Password Requirements</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="require_strong_password" 
                                   {{ ($settings['require_strong_password'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label">Require strong passwords</label>
                        </div>
                        <small class="text-muted">Minimum 8 characters with uppercase, lowercase, number, and special character</small>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- System Information -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    System Information
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Application Version:</strong></td>
                        <td>{{ $system_info['app_version'] ?? '1.0.0' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Laravel Version:</strong></td>
                        <td>{{ $system_info['laravel_version'] ?? '12.x' }}</td>
                    </tr>
                    <tr>
                        <td><strong>PHP Version:</strong></td>
                        <td>{{ $system_info['php_version'] ?? phpversion() }}</td>
                    </tr>
                    <tr>
                        <td><strong>Database:</strong></td>
                        <td>{{ $system_info['database'] ?? 'MySQL 8.0' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Server:</strong></td>
                        <td>{{ $system_info['server'] ?? $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Last Backup:</strong></td>
                        <td>{{ $system_info['last_backup'] ?? 'Never' }}</td>
                    </tr>
                </table>
                
                <div class="d-grid gap-2 mt-3">
                    <button class="btn btn-outline-primary" onclick="createBackup()">
                        <i class="fas fa-download me-2"></i>Create Database Backup
                    </button>
                    <button class="btn btn-outline-warning" onclick="clearCache()">
                        <i class="fas fa-broom me-2"></i>Clear Application Cache
                    </button>
                    <button class="btn btn-outline-info" onclick="runMaintenance()">
                        <i class="fas fa-tools me-2"></i>Run Maintenance Tasks
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Activity Log -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>
                    Recent Settings Changes
                </h5>
                <button class="btn btn-sm btn-outline-primary" onclick="refreshActivityLog()">
                    <i class="fas fa-sync-alt me-1"></i>Refresh
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Timestamp</th>
                                <th>Admin</th>
                                <th>Setting</th>
                                <th>Old Value</th>
                                <th>New Value</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activity_log ?? [] as $log)
                            <tr>
                                <td>{{ $log['timestamp'] }}</td>
                                <td>{{ $log['admin_name'] }}</td>
                                <td><code>{{ $log['setting_key'] }}</code></td>
                                <td><span class="text-muted">{{ $log['old_value'] ?? 'N/A' }}</span></td>
                                <td><strong>{{ $log['new_value'] }}</strong></td>
                                <td><small class="text-muted">{{ $log['ip_address'] }}</small></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-3 text-muted">
                                    No recent settings changes
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function saveAllSettings() {
    const forms = ['generalSettingsForm', 'paymentSettingsForm', 'verificationSettingsForm', 'notificationSettingsForm', 'securitySettingsForm'];
    const allData = {};
    
    forms.forEach(formId => {
        const form = document.getElementById(formId);
        const formData = new FormData(form);
        
        for (let [key, value] of formData.entries()) {
            allData[key] = value;
        }
        
        // Handle checkboxes that might not be in FormData if unchecked
        const checkboxes = form.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            if (!formData.has(checkbox.name)) {
                allData[checkbox.name] = false;
            } else {
                allData[checkbox.name] = true;
            }
        });
    });
    
    // Show loading state
    const saveBtn = event.target;
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
    saveBtn.disabled = true;
    
    fetch('/admin/settings', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(allData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Settings saved successfully!', 'success');
        } else {
            showNotification('Error saving settings: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error saving settings', 'error');
    })
    .finally(() => {
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
    });
}

function createBackup() {
    if (confirm('This will create a database backup. Continue?')) {
        fetch('/admin/backup', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Database backup created successfully!', 'success');
            } else {
                showNotification('Error creating backup: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error creating backup', 'error');
        });
    }
}

function clearCache() {
    if (confirm('This will clear all application cache. Continue?')) {
        fetch('/admin/cache/clear', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Application cache cleared successfully!', 'success');
            } else {
                showNotification('Error clearing cache: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error clearing cache', 'error');
        });
    }
}

function runMaintenance() {
    if (confirm('This will run maintenance tasks. Continue?')) {
        fetch('/admin/maintenance', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Maintenance tasks completed successfully!', 'success');
            } else {
                showNotification('Error running maintenance: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error running maintenance', 'error');
        });
    }
}

function refreshActivityLog() {
    location.reload();
}

function showNotification(message, type = 'info') {
    const alertClass = type === 'success' ? 'alert-success' : 
                      type === 'error' ? 'alert-danger' : 
                      type === 'warning' ? 'alert-warning' : 'alert-info';
    
    const notification = document.createElement('div');
    notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Auto-save on form changes (debounced)
let saveTimeout;
const forms = document.querySelectorAll('form');
forms.forEach(form => {
    form.addEventListener('change', function() {
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(() => {
            // Show unsaved changes indicator
            const saveBtn = document.querySelector('button[onclick="saveAllSettings()"]');
            if (!saveBtn.classList.contains('btn-warning')) {
                saveBtn.classList.remove('btn-primary');
                saveBtn.classList.add('btn-warning');
                saveBtn.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Unsaved Changes';
            }
        }, 1000);
    });
});
</script>
@endpush