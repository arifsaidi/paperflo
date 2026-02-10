@extends('layouts.committee')

@section('title', 'Registered Participants')

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

        .participant-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .participant-table th {
            text-align: left;
            padding: 16px;
            border-bottom: 2px solid #f5f5f7;
            color: #666;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .participant-table td {
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

        .badge-registered {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .badge-confirmed {
            background: #e1f5fe;
            color: #0277bd;
        }

        .badge-canceled {
            background: #ffebee;
            color: #c62828;
        }

        .reg-type {
            font-size: 13px;
            font-weight: 600;
            color: #666;
            background: #f8f9fa;
            padding: 4px 10px;
            border-radius: 6px;
            border: 1px solid #eee;
        }

        .btn-view {
            background: #fff;
            color: #FF5722;
            border: 1.5px solid #FF5722;
            padding: 8px 16px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 13px;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-view:hover {
            background: #FF5722;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(255, 87, 34, 0.2);
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
        <h1>Registered Participants</h1>
        <p>Manage all researchers and attendees registered for <strong>{{ $conference->name }}</strong></p>
    </div>

    <div class="card">
        <div class="card-title">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                <circle cx="9" cy="7" r="4" />
                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
            </svg>
            Participant Directory ({{ $participants->count() }})
        </div>

        @if($participants->isEmpty())
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
                <p>No participants have registered for this conference yet.</p>
            </div>
        @else
            <table class="participant-table">
                <thead>
                    <tr>
                        <th style="width: 30%;">Participant</th>
                        <th style="width: 25%;">Institution</th>
                        <th style="width: 15%;">Type</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 15%; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($participants as $p)
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="user-details">
                                        <div class="user-name">{{ $p->name }}</div>
                                        <div class="user-email">{{ $p->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 500;">{{ $p->institution ?: '-' }}</div>
                                <div style="font-size: 12px; color: #888;">{{ $p->department ?: '' }}</div>
                            </td>
                            <td>
                                <span class="reg-type">{{ $p->registration_type }}</span>
                            </td>
                            <td>
                                <span class="badge badge-{{ strtolower($p->registration_status) }}">
                                    {{ $p->registration_status }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('committee.participants.show', [$conference->conference_id, $p->participant_id]) }}" class="btn-view">
                                    View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection