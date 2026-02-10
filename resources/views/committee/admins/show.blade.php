@extends('layouts.committee')

@section('title', 'Committee Member - ' . $admin->name)

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

        .badge-manager {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .badge-admin {
            background: #e3f2fd;
            color: #1976d2;
        }

        .badge-organizer {
            background: #fff8e1;
            color: #f57f17;
        }

        .badge-member {
            background: #f5f5f5;
            color: #757575;
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

        .btn-remove {
            background: #ffebee;
            color: #c62828;
            border: 1.5px solid #ffcdd2;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-remove:hover {
            background: #c62828;
            border-color: #c62828;
            color: #fff;
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <a href="{{ route('committee.admins.index', $conference->conference_id) }}" class="btn-back">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M19 12H5m7-7l-7 7 7 7" />
            </svg>
            Back to Committee Team
        </a>
        <h1>Committee Member</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert"
            style="border-radius: 12px; margin-bottom: 30px;">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

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
                        @if($isManager)
                            <form method="POST"
                                action="{{ route('committee.admins.updateName', ['conference_id' => $conference->conference_id, 'admin_id' => $admin->admin_id]) }}"
                                style="display: flex; align-items: center; gap: 10px;">
                                @csrf
                                <input type="text" name="name" value="{{ $admin->name }}" required
                                    style="font-size: 18px; font-weight: 700; color: #FF5722; border: 1px solid #e0e0e0; border-radius: 10px; padding: 8px 14px; width: 100%;">
                                <button type="submit"
                                    style="background: #FF5722; color: #fff; border: none; padding: 8px 18px; border-radius: 10px; font-weight: 700; font-size: 13px; cursor: pointer; white-space: nowrap; transition: all 0.2s;">
                                    Save
                                </button>
                            </form>
                        @else
                            <div class="info-value" style="font-size: 20px; color: #FF5722;">{{ $admin->name }}</div>
                        @endif
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email Address</div>
                        <div class="info-value">{{ $admin->email }}</div>
                    </div>
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
                            {{ \Carbon\Carbon::parse($admin->updated_at)->format('d M Y, h:i A') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Account Created</div>
                        <div class="info-value">
                            {{ \Carbon\Carbon::parse($admin->created_at)->format('d M Y, h:i A') }}</div>
                    </div>
                </div>
            </div>

            {{-- Remove Button (only for managers, and not for self or organizer) --}}
            @if($isManager && $admin->admin_id != session('user_id') && $admin->email !== $conference->chairman_email && $admin->role_name !== 'CONFERENCE_MANAGER')
                <form
                    action="{{ route('committee.admins.destroy', ['conference_id' => $conference->conference_id, 'admin_id' => $admin->admin_id]) }}"
                    method="POST" onsubmit="return confirm('Are you sure you want to remove this member from the conference?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-remove">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align: -2px; margin-right: 6px;">
                            <path d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16" />
                        </svg>
                        Remove from Conference
                    </button>
                </form>
            @endif
        </div>

        <div class="side-column">
            <div class="card status-card">
                <div class="card-title">Access Level</div>

                <div style="margin-bottom: 32px;">
                    <div class="info-label">Current Role</div>
                    <div style="margin-top: 8px;">
                        @if($admin->role_name === 'CONFERENCE_MANAGER')
                            <span class="badge badge-manager">Manager</span>
                        @elseif($admin->role_name === 'CONFERENCE_ADMIN')
                            <span class="badge badge-admin">Admin</span>
                        @else
                            <span class="badge badge-member">{{ $admin->role_name ?? 'Member' }}</span>
                        @endif

                        @if($admin->email === $conference->chairman_email)
                            <span class="badge badge-organizer" style="margin-left: 4px;">Organizer</span>
                        @endif
                    </div>
                </div>

                <div>
                    <div class="info-label">Date Added</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($admin->assigned_date)->format('d M Y') }}</div>
                </div>
            </div>

            <div class="card">
                <div class="card-title">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                        <polyline points="22,6 12,13 2,6" />
                    </svg>
                    Contact Info
                </div>

                <div class="contact-box">
                    <div class="contact-row">
                        <div class="contact-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5"
                                viewBox="0 0 24 24">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                <polyline points="22,6 12,13 2,6" />
                            </svg>
                        </div>
                        <div>
                            <div class="info-label" style="margin-bottom: 2px;">Email</div>
                            <div class="info-value">{{ $admin->email }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
