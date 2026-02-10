@extends('layouts.committee')

@section('title', 'Participant Detail - ' . $participant->name)

@section('styles')
    <style>
        .page-header {
            margin-bottom: 32px;
        }

        .btn-back {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #666;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: color 0.2s;
            margin-bottom: 12px;
        }

        .btn-back:hover {
            color: #FF5722;
        }

        .page-header h1 {
            font-size: 32px;
            font-weight: 800;
            color: #1a1a2e;
            margin: 0;
        }

        .profile-container {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 32px;
        }

        .card {
            background: #fff;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(0, 0, 0, 0.02);
            margin-bottom: 32px;
        }

        .card-title {
            font-size: 20px;
            font-weight: 800;
            color: #1a1a2e;
            margin-bottom: 32px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card-title svg {
            color: #FF5722;
        }

        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        .info-item {
            margin-bottom: 8px;
        }

        .info-label {
            font-size: 11px;
            font-weight: 800;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 10px;
        }

        .info-value {
            font-size: 16px;
            color: #1a1a2e;
            font-weight: 600;
            line-height: 1.5;
        }

        .badge {
            padding: 8px 16px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
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

        .status-card {
            background: linear-gradient(135deg, #1a1a2e 0%, #2a2a4e 100%);
            color: #fff;
        }

        .status-card .card-title {
            color: #fff;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 20px;
        }

        .status-card .info-label {
            color: rgba(255, 255, 255, 0.5);
        }

        .status-card .info-value {
            color: #fff;
        }

        .contact-box {
            background: #f8f9fa;
            border-radius: 20px;
            padding: 24px;
            border: 1px solid #eee;
            margin-top: 24px;
        }

        .contact-row {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
        }

        .contact-row:last-child {
            margin-bottom: 0;
        }

        .contact-icon {
            width: 44px;
            height: 44px;
            background: #fff;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FF5722;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .reg-type-tag {
            display: inline-block;
            background: #FF5722;
            color: #fff;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            text-transform: capitalize;
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <a href="{{ route('committee.participants.index', $conference->conference_id) }}" class="btn-back">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M19 12H5m7-7l-7 7 7 7" />
            </svg>
            Back to Directory
        </a>
        <h1>Participant Profile</h1>
    </div>

    <div class="profile-container">
        <div class="main-column">
            <div class="card">
                <div class="card-title">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                    Account Information
                </div>

                <div class="info-section">
                    <div class="info-item">
                        <div class="info-label">Full Name</div>
                        <div class="info-value" style="font-size: 20px; color: #FF5722;">{{ $participant->name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email Address</div>
                        <div class="info-value">{{ $participant->email }}</div>
                    </div>
                </div>

                <div style="margin-top: 40px;" class="info-section">
                    <div class="info-item">
                        <div class="info-label">Institution</div>
                        <div class="info-value">{{ $participant->institution ?: 'Not specified' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Department / Faculty</div>
                        <div class="info-value">{{ $participant->department ?: '-' }}<br><span
                                style="color: #888; font-size: 14px;">{{ $participant->faculty ?: '' }}</span></div>
                    </div>
                </div>

                <div style="margin-top: 40px;" class="info-item">
                    <div class="info-label">Address</div>
                    <div class="info-value">{{ $participant->address ?: 'No address provided' }}</div>
                </div>
            </div>

            <div class="card">
                <div class="card-title">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    System Logs
                </div>
                <div class="info-section">
                    <div class="info-item">
                        <div class="info-label">Last Updated</div>
                        <div class="info-value">
                            {{ \Carbon\Carbon::parse($participant->updated_at)->format('d M Y, h:i A') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Account Created</div>
                        <div class="info-value">
                            {{ \Carbon\Carbon::parse($participant->created_at)->format('d M Y, h:i A') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="side-column">
            <div class="card status-card">
                <div class="card-title">Registration Status</div>

                <div style="margin-bottom: 32px;">
                    <div class="info-label">Registration ID</div>
                    <div class="info-value" style="font-family: monospace; font-size: 24px; letter-spacing: 1px;">
                        #{{ $participant->registration_id }}</div>
                </div>

                <div style="margin-bottom: 32px;">
                    <div class="info-label">Current Status</div>
                    <div style="margin-top: 8px;">
                        <span class="badge badge-{{ strtolower($participant->registration_status) }}">
                            {{ $participant->registration_status }}
                        </span>
                    </div>
                </div>

                <div style="margin-bottom: 32px;">
                    <div class="info-label">Participant Category</div>
                    <div style="margin-top: 8px;">
                        <span class="reg-type-tag">{{ $participant->registration_type }}</span>
                    </div>
                </div>

                <div>
                    <div class="info-label">Date of Registration</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($participant->registered_at)->format('d M Y') }}</div>
                </div>
            </div>

            <div class="card">
                <div class="card-title">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path
                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    Contact Info
                </div>

                <div class="contact-box">
                    <div class="contact-row">
                        <div class="contact-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5"
                                viewBox="0 0 24 24">
                                <path
                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                            </svg>
                        </div>
                        <div>
                            <div class="info-label" style="margin-bottom: 2px;">Phone Number</div>
                            <div class="info-value">{{ $participant->phone_num ?: 'Not provided' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection