@extends('layouts.committee')

@section('title', 'Committee Team')

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
            justify-content: space-between;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .admin-table th {
            text-align: left;
            padding: 16px;
            border-bottom: 2px solid #f5f5f7;
            color: #666;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .admin-table td {
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
            cursor: pointer;
        }

        .btn-view:hover {
            background: #FF5722;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(255, 87, 34, 0.2);
        }

        .btn-remove {
            color: #c62828;
            border-color: #ffcdd2;
            background: #ffebee;
        }

        .btn-remove:hover {
            background: #c62828;
            border-color: #c62828;
            color: #fff;
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
        <h1>Committee Team</h1>
        <p>Manage administrators and members for <strong>{{ $conference->name }}</strong></p>
    </div>

    {{-- Organizer Invite Manager Section --}}
    @if($isOrganizer)
        <div class="card" style="background: #fff8e1; border: 1px solid #ffe0b2; box-shadow: none;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <strong style="color: #f57f17; font-size: 16px;">Invite Conference Manager</strong>
                    <p style="margin-top: 4px; color: #f57f17; margin-bottom: 0;">As the Organizer, you can invite a Conference
                        Manager to lead the committee.</p>
                </div>
                <button type="button" class="btn-view" style="background: #fff; border-color: #f57f17; color: #f57f17;"
                    data-toggle="modal" data-target="#inviteManagerModal">
                    Invite Manager
                </button>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert"
            style="border-radius: 12px; margin-bottom: 30px;">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-title">
            <div style="display: flex; align-items: center; gap: 10px;">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
                Committee Directory ({{ $admins->count() }})
            </div>

            @if($isManager)
                <button type="button" class="btn-view" data-toggle="modal" data-target="#inviteAdminModal">
                    <i class="fas fa-plus fa-sm mr-2"></i> Add Admin
                </button>
            @endif
        </div>

        @if($admins->isEmpty())
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
                <p>No admins found for this conference.</p>
            </div>
        @else
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 35%;">Committee Member</th>
                        <th style="width: 25%;">Access Level</th>
                        <th style="width: 20%;">Date Added</th>
                        @if($isManager)
                            <th style="width: 20%; text-align: right;">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($admins as $admin)
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="user-details">
                                        <div class="user-name">{{ $admin->name }}</div>
                                        <div class="user-email">{{ $admin->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($isManager && $admin->admin_id != session('user_id') && $admin->email !== $conference->chairman_email)
                                    <form method="POST"
                                        action="{{ route('committee.admins.updateAccessLevel', ['conference_id' => $conference->conference_id, 'admin_id' => $admin->admin_id]) }}"
                                        onsubmit="return confirm('Are you sure you want to change this member\'s access level?');"
                                        style="display: inline;">
                                        @csrf
                                        <select name="access_level" onchange="this.form.submit()"
                                            style="padding: 6px 12px; border-radius: 8px; border: 1px solid #e0e0e0; font-size: 13px; font-weight: 600; cursor: pointer; background: #fff;">
                                            <option value="admin" {{ $admin->role_name === 'CONFERENCE_ADMIN' ? 'selected' : '' }}>Admin
                                            </option>
                                            <option value="manager" {{ $admin->role_name === 'CONFERENCE_MANAGER' ? 'selected' : '' }}>
                                                Manager</option>
                                        </select>
                                    </form>
                                @else
                                    @if($admin->role_name === 'CONFERENCE_MANAGER')
                                        <span class="badge badge-manager">Manager</span>
                                    @elseif($admin->role_name === 'CONFERENCE_ADMIN')
                                        <span class="badge badge-admin">Admin</span>
                                    @else
                                        <span class="badge badge-member">{{ $admin->role_name ?? 'Member' }}</span>
                                    @endif
                                @endif

                                @if($admin->email === $conference->chairman_email)
                                    <span class="badge badge-organizer ml-1">Organizer</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($admin->assigned_date)->format('M d, Y') }}</td>
                            @if($isManager)
                                <td style="text-align: right;">
                                    <a href="{{ route('committee.admins.show', ['conference_id' => $conference->conference_id, 'admin_id' => $admin->admin_id]) }}"
                                        class="btn-view">View</a>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Invite Manager Modal --}}
    @if($isOrganizer)
        <div class="modal fade" id="inviteManagerModal" tabindex="-1" role="dialog" aria-labelledby="inviteManagerModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('committee.admins.invite.manager', $conference->conference_id) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="inviteManagerModalLabel">Invite Conference Manager</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="managerEmail">Email Address</label>
                                <input type="email" class="form-control" id="managerEmail" name="email" required
                                    placeholder="Enter email">
                                <small class="form-text text-muted">A manager has full control over the committee team.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-warning">Send Invite</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Add Admin Modal --}}
    @if($isManager)
        <div class="modal fade" id="inviteAdminModal" tabindex="-1" role="dialog" aria-labelledby="inviteAdminModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('committee.admins.store', $conference->conference_id) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="inviteAdminModalLabel">Add Conference Admin</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="adminEmail">Email Address</label>
                                <input type="email" class="form-control" id="adminEmail" name="email" required
                                    placeholder="Enter email">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Admin</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

@endsection