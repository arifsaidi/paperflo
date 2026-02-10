@extends('layouts.committee')

@section('title', 'Participant Payments')

@section('styles')
    <style>
        .page-header {
            margin-bottom: 32px;
        }

        .page-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 4px;
        }

        .page-header p {
            color: #666;
            font-size: 15px;
        }

        .card {
            background: #fff;
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .payment-table th {
            text-align: left;
            padding: 16px;
            border-bottom: 2px solid #f5f5f7;
            color: #666;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .payment-table td {
            padding: 20px 16px;
            border-bottom: 1px solid #f5f5f7;
            vertical-align: middle;
            color: #333;
            font-size: 15px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .user-details {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 700;
            color: #1a1a2e;
        }

        .user-email {
            font-size: 13px;
            color: #888;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-pending {
            background: #fff8e1;
            color: #f57f17;
        }

        .badge-paid {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .badge-failed {
            background: #ffebee;
            color: #c62828;
        }

        .amount {
            font-weight: 700;
            color: #1a1a2e;
        }

        .empty-state {
            text-align: center;
            padding: 60px 0;
            color: #888;
        }

        .empty-state svg {
            width: 64px;
            height: 64px;
            margin-bottom: 16px;
            color: #ddd;
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h1>Participant Payments</h1>
        <p>View all payments received from presenters and listeners for <strong>{{ $conference->name }}</strong>.</p>
    </div>

    {{-- Presenter Payments --}}
    <div class="card">
        <div class="card-title">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 2v20M2 12h20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
            </svg>
            Presenter Payments ({{ $presenterPayments->count() }})
        </div>

        @if($presenterPayments->isEmpty())
            <div class="empty-state">
                <p>No presenter payments found.</p>
            </div>
        @else
            <table class="payment-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Payer</th>
                        <th style="width: 30%;">Paper / Title</th>
                        <th style="width: 15%;">Amount</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 15%; text-align: right;">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($presenterPayments as $payment)
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="user-details">
                                        <div class="user-name">{{ $payment->participant_name }}</div>
                                        <div class="user-email">{{ $payment->participant_email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 500;">{{ Str::limit($payment->paper_title, 40) }}</div>
                                <div style="font-size: 12px; color: #888;">Presenter Status: {{ $payment->presenter_status }}</div>
                            </td>
                            <td>
                                <span class="amount">RM {{ number_format($payment->amount, 2) }}</span>
                            </td>
                            <td>
                                <span class="badge badge-{{ strtolower($payment->status) }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td style="text-align: right; color: #666; font-size: 13px;">
                                {{ $payment->created_at ? \Carbon\Carbon::parse($payment->created_at)->format('M d, Y') : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Listener Payments --}}
    <div class="card">
        <div class="card-title">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                <circle cx="9" cy="7" r="4" />
                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
            </svg>
            Listener Payments ({{ $listenerPayments->count() }})
        </div>

        @if($listenerPayments->isEmpty())
            <div class="empty-state">
                <p>No listener payments found.</p>
            </div>
        @else
            <table class="payment-table">
                <thead>
                    <tr>
                        <th style="width: 30%;">Payer</th>
                        <th style="width: 25%;">Registration Type</th>
                        <th style="width: 15%;">Amount</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 15%; text-align: right;">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($listenerPayments as $payment)
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="user-details">
                                        <div class="user-name">{{ $payment->participant_name }}</div>
                                        <div class="user-email">{{ $payment->participant_email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="font-weight: 500;">{{ $payment->registration_type }}</span>
                            </td>
                            <td>
                                <span class="amount">RM {{ number_format($payment->amount, 2) }}</span>
                            </td>
                            <td>
                                <span class="badge badge-{{ strtolower($payment->payment_status) }}">
                                    {{ ucfirst($payment->payment_status) }}
                                </span>
                            </td>
                            <td style="text-align: right; color: #666; font-size: 13px;">
                                {{ $payment->created_at ? \Carbon\Carbon::parse($payment->created_at)->format('M d, Y') : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection