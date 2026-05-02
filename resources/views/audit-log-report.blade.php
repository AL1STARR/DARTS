<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Log Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            color: #333;
            background: white;
            padding: 20px;
        }
        .container {
            max-width: 8.5in;
            height: 11in;
            margin: 0 auto;
            background: white;
            padding: 40px;
            position: relative;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 3px solid #003366;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #0369a1, #0891b2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 24px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #000;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #ddd;
            padding-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            color: #666;
        }
        .info-value {
            text-align: right;
        }
        .table-section {
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        thead {
            background: #003366;
            color: white;
        }
        th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border: 1px solid #003366;
            font-size: 12px;
        }
        td {
            padding: 10px 12px;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        tbody tr:nth-child(even) {
            background: #f9f9f9;
        }
        tbody tr:hover {
            background: #f0f0f0;
        }
        .event-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            white-space: nowrap;
        }
        .event-badge.request_created,
        .event-badge.created,
        .event-badge.user_created {
            background: #d1fae5;
            color: #065f46;
        }
        .event-badge.request_deleted,
        .event-badge.deleted,
        .event-badge.returned {
            background: #fee2e2;
            color: #991b1b;
        }
        .event-badge.document_uploaded {
            background: #dbeafe;
            color: #1e40af;
        }
        .event-badge.document_deleted {
            background: #fecaca;
            color: #7c2d12;
        }
        .event-badge.user_request_approved,
        .event-badge.approved,
        .event-badge.accomplished {
            background: #c7d2fe;
            color: #312e81;
        }
        .event-badge.received,
        .event-badge.republished {
            background: #fcd34d;
            color: #78350f;
        }
        .event-badge.flagged {
            background: #fca5a5;
            color: #9a1313;
        }
        .type-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            background: #f3f4f6;
            color: #374151;
            font-size: 10px;
            font-weight: 600;
        }
        .empty-message {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .timestamp-col {
            white-space: nowrap;
            color: #666;
        }
        .description-col {
            max-width: 250px;
            word-wrap: break-word;
        }
        @media print {
            body {
                padding: 0;
            }
            .container {
                max-width: 100%;
                padding: 0.5in;
                margin: 0;
                height: auto;
            }
            table {
                page-break-inside: avoid;
            }
            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo-section">
                <div class="logo">📋</div>
                <div>
                    <div class="title">AUDIT LOG REPORT</div>
                </div>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Period:</span>
                <span class="info-value">{{ $monthYear }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Generated by:</span>
                <span class="info-value">{{ $adminName }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Date Generated:</span>
                <span class="info-value">{{ $generatedDate }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Organization:</span>
                <span class="info-value">DARTS Intelligence</span>
            </div>
        </div>

        <div class="table-section">
            @if($logs->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th style="width: 15%;">TIMESTAMP</th>
                            <th style="width: 15%;">EVENT</th>
                            <th style="width: 15%;">TYPE</th>
                            <th style="width: 35%;">DESCRIPTION</th>
                            <th style="width: 20%;">USER</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td class="timestamp-col">{{ $log->created_at->format('M d, Y g:i A') }}</td>
                                <td style="text-align: center;">
                                    <span class="event-badge {{ $log->event }}">
                                        {{ str_replace('_', ' ', $log->event) }}
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <span class="type-badge">{{ $log->auditable_type }}</span>
                                </td>
                                <td class="description-col">{{ $log->description }}</td>
                                <td style="text-align: center;">{{ $log->user ? $log->user->first_name . ' ' . $log->user->last_name : 'System' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-message">
                    No audit log entries found for the selected period.
                </div>
            @endif
        </div>

        <div class="footer">
            <p>This is a monthly audit report system-generated on {{ $generatedDate }} at {{ $generatedTime }}.</p>
            <p style="margin-top: 8px;">Total Entries: {{ $logs->count() }}</p>
        </div>
    </div>
</body>
</html>
