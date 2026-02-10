@extends('layouts.committee')

@section('title', 'Dashboard Overview')

@section('styles')
    <style>
        .dashboard-container {
            width: 100%;
        }

        .dashboard-header {
            margin-bottom: 32px;
        }

        .dashboard-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 800;
            color: #1a1a2e;
            margin-bottom: 8px;
        }

        .dashboard-header p {
            color: #666;
            font-size: 15px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: #fff;
            padding: 24px;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .stat-card .label {
            font-size: 13px;
            font-weight: 600;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card .value {
            font-size: 32px;
            font-weight: 700;
            color: #1a1a2e;
        }

        .stat-card .trend {
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .stat-card.primary {
            border-left: 4px solid #FF5722;
        }

        .stat-card.success {
            border-left: 4px solid #4CAF50;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 24px;
        }

        .summary-card {
            background: #fff;
            padding: 24px;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        }

        .summary-card h3 {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a2e;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table th {
            text-align: left;
            padding: 10px 0;
            color: #999;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            border-bottom: 1px solid #f0f0f0;
        }

        .summary-table td {
            padding: 14px 0;
            border-bottom: 1px solid #f8f8f8;
            font-size: 14px;
            color: #444;
        }

        .summary-table tr:last-child td {
            border-bottom: none;
        }

        .status-pill {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
        }

        .pill-neutral {
            background: #f5f5f7;
            color: #666;
        }

        .count-badge {
            background: #e3f2fd;
            color: #1976d2;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 13px;
        }
    </style>
@endsection

@section('content')
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Overview</h1>
            <p>Welcome to the committee panel for <strong>{{ $conference->name }}</strong>.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card primary">
                <span class="label">Total Papers</span>
                <span class="value">{{ $totalPapers }}</span>
                <span class="trend" style="color: #666;">Total submissions to date</span>
            </div>

            <div class="stat-card success">
                <span class="label">Camera-Ready Submitted</span>
                <span class="value">{{ $cameraReadySubmitted }}</span>
                <span class="trend" style="color: #4CAF50;">Final versions received</span>
            </div>

            {{-- Placeholder for a third stat if needed --}}
            <div class="stat-card">
                <span class="label">Total Amount Paid</span>
                @php
                    $paidCount = $presenterCounts->firstWhere('status', 'Paid')->total ?? 0;
                @endphp
                <span class="value">{{ $paidCount }}</span>
                <span class="trend" style="color: #666;">Confirmed registrations</span>
            </div>
        </div>

        <div class="summary-grid">
            {{-- Abstract Summary --}}
            <div class="summary-card">
                <h3>
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Abstract Status
                </h3>
                <table class="summary-table">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th style="text-align: right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($abstractCounts as $row)
                            <tr>
                                <td><span class="status-pill pill-neutral">{{ $row->status }}</span></td>
                                <td style="text-align: right;"><span class="count-badge">{{ $row->total }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Full Paper Summary --}}
            <div class="summary-card">
                <h3>
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    Full Paper Status
                </h3>
                <table class="summary-table">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th style="text-align: right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($fullpaperCounts as $row)
                            <tr>
                                <td><span class="status-pill pill-neutral">{{ $row->status }}</span></td>
                                <td style="text-align: right;"><span class="count-badge">{{ $row->total }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Payment Summary --}}
            <div class="summary-card">
                <h3>
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2" />
                        <rect x="9" y="9" width="12" height="6" rx="2" />
                        <circle cx="15" cy="12" r="1" />
                    </svg>
                    Payment / Assignment
                </h3>
                <table class="summary-table">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th style="text-align: right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($presenterCounts as $row)
                            <tr>
                                <td><span class="status-pill pill-neutral">{{ $row->status }}</span></td>
                                <td style="text-align: right;"><span class="count-badge">{{ $row->total }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection