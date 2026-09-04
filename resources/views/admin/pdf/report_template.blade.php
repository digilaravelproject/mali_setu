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
        }
        .summary-table td {
            padding: 3px 0;
        }
        .summary-table td.label {
            font-weight: bold;
            color: #555;
            width: 25%;
        }
        .summary-table td.value {
            color: #111;
            width: 25%;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
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
                @php $count = 0; @endphp
                @foreach($summary as $label => $value)
                    @if($count > 0 && $count % 2 == 0)
                        </tr><tr>
                    @endif
                    <td class="label">{{ $label }}:</td>
                    <td class="value">{{ $value }}</td>
                    @php $count++; @endphp
                @endforeach
                
                {{-- Balance columns if odd count --}}
                @if($count % 2 != 0)
                    <td></td><td></td>
                @endif
            </tr>
        </table>
    </div>
    @endif

    @if(count($headers) <= 10)
    <table class="report-table">
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th class="{{ str_contains(strtolower($header), 'amount') || str_contains(strtolower($header), 'fee') || str_contains(strtolower($header), 'revenue') ? 'text-right' : '' }}">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @if(count($rows) > 0)
                @foreach($rows as $row)
                    <tr>
                        @foreach($row as $cellKey => $cellValue)
                            <td class="{{ str_contains(strtolower($cellKey), 'amount') || str_contains(strtolower($cellKey), 'fee') || str_contains(strtolower($cellKey), 'revenue') ? 'text-right' : '' }}">
                                {{ $cellValue }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="{{ count($headers) }}" style="text-align: center; padding: 15px;">
                        No records found for this report period.
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
    @else
        @forelse($rows as $row)
            @php
                $detailPairs = [];
                foreach (array_values($row) as $index => $cellValue) {
                    if ($cellValue !== 'N/A' && $cellValue !== '') {
                        $detailPairs[] = [$headers[$index] ?? ('Field ' . ($index + 1)), $cellValue];
                    }
                }
            @endphp
            <div class="record-block">
                <div class="record-heading">Record {{ $loop->iteration }}</div>
                <table class="detail-table">
                    @foreach(array_chunk($detailPairs, 3) as $pairGroup)
                        <tr>
                            @foreach($pairGroup as [$label, $value])
                                <td class="label">{{ $label }}</td>
                                <td class="value">{{ $value }}</td>
                            @endforeach
                            @for($i = count($pairGroup); $i < 3; $i++)
                                <td class="label"></td><td class="value"></td>
                            @endfor
                        </tr>
                    @endforeach
                </table>
            </div>
        @empty
            <div style="text-align: center; padding: 15px;">No records found for this report period.</div>
        @endforelse
    @endif

    <div class="footer">
        Generated automatically by Mali Setu Admin System on {{ date('Y-m-d H:i:s') }}
    </div>

</body>
</html>
