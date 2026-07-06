<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>User Profile Report - {{ $user->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 10px;
            font-size: 13px;
            line-height: 1.5;
        }
        .header {
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .logo-text {
            font-size: 24px;
            font-weight: bold;
            color: #0d6efd;
        }
        .report-title {
            font-size: 16px;
            color: #555;
            text-align: right;
            margin-top: -30px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #0d6efd;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-top: 25px;
            margin-bottom: 12px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 6px 10px;
            vertical-align: top;
        }
        .info-table td.label {
            font-weight: bold;
            color: #666;
            width: 30%;
        }
        .info-table td.value {
            color: #111;
            width: 70%;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 11px;
            font-weight: bold;
            border-radius: 3px;
            text-transform: uppercase;
        }
        .badge-success {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        .badge-warning {
            background-color: #fff3cd;
            color: #664d03;
        }
        .badge-danger {
            background-color: #f8d7da;
            color: #842029;
        }
        .badge-secondary {
            background-color: #e2e3e5;
            color: #41464b;
        }
        .footer {
            position: fixed;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo-text">Mali Setu</div>
        <div class="report-title">User Account Profile</div>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">User ID:</td>
            <td class="value">#{{ $user->id }}</td>
        </tr>
        <tr>
            <td class="label">Full Name:</td>
            <td class="value"><strong>{{ $user->name }}</strong></td>
        </tr>
        <tr>
            <td class="label">Email Address:</td>
            <td class="value">{{ $user->email }}</td>
        </tr>
        <tr>
            <td class="label">Phone Number:</td>
            <td class="value">{{ $user->phone ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Registration Date:</td>
            <td class="value">{{ $user->created_at->format('M d, Y H:i') }} ({{ $user->created_at->diffForHumans() }})</td>
        </tr>
    </table>

    <div class="section-title">Account Status & Verification</div>
    <table class="info-table">
        <tr>
            <td class="label">Account Status:</td>
            <td class="value">
                @if($user->status === 'active')
                    <span class="badge badge-success">Active</span>
                @elseif($user->status === 'inactive')
                    <span class="badge badge-secondary">Inactive</span>
                @elseif($user->status === 'suspended')
                    <span class="badge badge-warning text-black">Suspended</span>
                @else
                    <span class="badge badge-danger" style="color: red !important;">{{ $user->status }}</span>
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">Caste Verification:</td>
            <td class="value">
                @if($user->caste_verification_status === 'approved')
                    <span class="badge badge-success">Verified</span>
                @elseif($user->caste_verification_status === 'pending')
                    <span class="badge badge-warning text-black">Pending Review</span>
                @else
                    <span class="badge badge-danger" style="color: red !important;">Rejected</span>
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">Account Type:</td>
            <td class="value"><span class="badge badge-secondary">{{ ucfirst($user->user_type) }}</span></td>
        </tr>
        @if($user->admin_notes)
        <tr>
            <td class="label">Admin Notes:</td>
            <td class="value">{{ $user->admin_notes }}</td>
        </tr>
        @endif
    </table>

    <div class="section-title">Personal & Professional Details</div>
    <table class="info-table">
        <tr>
            <td class="label">Date of Birth:</td>
            <td class="value">{{ $user->dob ? \Carbon\Carbon::parse($user->dob)->format('M d, Y') : 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Age:</td>
            <td class="value">{{ $user->age ?? 'N/A' }} years</td>
        </tr>
        <tr>
            <td class="label">Occupation:</td>
            <td class="value">{{ $user->occupation ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Company Name:</td>
            <td class="value">{{ $user->company_name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Department:</td>
            <td class="value">{{ $user->dept_name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Designation:</td>
            <td class="value">{{ $user->designation ?? 'N/A' }}</td>
        </tr>
    </table>

    <div class="section-title">Address & Geolocation</div>
    <table class="info-table">
        <tr>
            <td class="label">Address:</td>
            <td class="value">{{ $user->address ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">City / Village:</td>
            <td class="value">{{ $user->city ?? $user->village ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">State / Pincode:</td>
            <td class="value">{{ $user->state ?? 'N/A' }} - {{ $user->pincode ?? 'N/A' }}</td>
        </tr>
        @if($user->latitude && $user->longitude)
        <tr>
            <td class="label">Coordinates:</td>
            <td class="value">Lat: {{ $user->latitude }}, Lng: {{ $user->longitude }}</td>
        </tr>
        @endif
    </table>

    <div class="footer">
        Generated automatically by Mali Setu Admin System on {{ date('Y-m-d H:i:s') }}
    </div>

</body>
</html>
