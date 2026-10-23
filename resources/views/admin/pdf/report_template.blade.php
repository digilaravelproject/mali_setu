<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 10px;
            font-size: 11px;
            line-height: 1.4;
        }
        .header {
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .logo-text {
            font-size: 22px;
            font-weight: bold;
            color: #0d6efd;
        }
        .report-title {
            font-size: 14px;
            color: #555;
            text-align: right;
            margin-top: -25px;
            font-weight: bold;
        }
        .summary-box {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .summary-title {
            font-size: 12px;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 3px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table th,
        .summary-table td {
            padding: 4px 6px;
            border: 1px solid #dee2e6;
        }
        .summary-table th {
            font-weight: bold;
            color: #555;
            background-color: #f1f3f5;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: fixed;
            font-size: 7px;
        }
        .report-table th {
            background-color: #0d6efd;
            color: white;
            font-weight: bold;
            text-align: left;
            padding: 6px 8px;
            border: 1px solid #dee2e6;
        }
        .report-table td {
            padding: 6px 8px;
            border: 1px solid #dee2e6;
            vertical-align: middle;
            overflow-wrap: anywhere;
        }
        .report-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .record-block {
            margin: 0 0 10px;
            page-break-inside: avoid;
        }
        .record-heading {
            background-color: #0d6efd;
            color: #fff;
            font-size: 10px;
            font-weight: bold;
            padding: 4px 6px;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 8px;
        }
        .detail-table td {
            border: 1px solid #dee2e6;
            padding: 3px 4px;
            vertical-align: top;
            overflow-wrap: anywhere;
        }
        .detail-table td.label {
            width: 11%;
            background-color: #f1f3f5;
            font-weight: bold;
            color: #444;
        }
        .detail-table td.value {
            width: 22%;
        }
        .text-right {
            text-align: right;
        }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 2px;
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
        .footer {
            position: fixed;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo-text">Mali Setu</div>
        <div class="report-title">{{ $title }}</div>
    </div>

    @if(!empty($summary))
    <div class="summary-box">
        <div class="summary-title">Report Summary Metrics</div>
        <table class="summary-table">
            <tr>
                @foreach(array_keys($summary) as $label)
                    <th>{{ $label }}</th>
                @endforeach
            </tr>
            <tr>
                @foreach($summary as $value)
                    <td>{{ $value }}</td>
                @endforeach
            </tr>
        </table>
    </div>
    @endif

    @if(count($rows) > 0)
        @foreach(array_chunk(array_keys($headers), 8) as $columnIndexes)
        <table class="report-table">
            <thead>
                <tr>
                    @foreach($columnIndexes as $columnIndex)
                        <th class="{{ str_contains(strtolower($headers[$columnIndex]), 'amount') || str_contains(strtolower($headers[$columnIndex]), 'fee') || str_contains(strtolower($headers[$columnIndex]), 'revenue') ? 'text-right' : '' }}">
                            {{ $headers[$columnIndex] }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $row)
                    <tr>
                        @foreach($columnIndexes as $columnIndex)
                            <td class="{{ str_contains(strtolower($headers[$columnIndex]), 'amount') || str_contains(strtolower($headers[$columnIndex]), 'fee') || str_contains(strtolower($headers[$columnIndex]), 'revenue') ? 'text-right' : '' }}">
                                {{ array_values($row)[$columnIndex] ?? 'N/A' }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
        @endforeach
    @else
        <div style="text-align: center; padding: 15px;">No records found for this report period.</div>
    @endif

    <div class="footer">
        Generated automatically by Mali Setu Admin System on {{ date('Y-m-d H:i:s') }}
    </div>

</body>
</html>
