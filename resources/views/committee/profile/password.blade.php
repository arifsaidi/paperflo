@extends('layouts.committee')

@section('title', 'Change Password')

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

        .profile-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 24px;
        }

        @media (max-width: 768px) {
            .profile-layout {
                grid-template-columns: 1fr;
            }
        }

        .profile-nav {
            background: #fff;
            border-radius: 20px;
            padding: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            height: fit-content;
        }

        .profile-nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 12px;
            color: #666;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s;
        }

        .profile-nav-link:hover {
            background: #f8f9fa;
            color: #1a1a2e;
            text-decoration: none;
        }

        .profile-nav-link.active {
            background: linear-gradient(135deg, #FF5722 0%, #FF7043 100%);
            color: #fff;
        }

        .profile-nav-link svg {
            width: 20px;
            height: 20px;
        }

        .card {
            background: #fff;
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
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

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 14px;
            color: #1a1a2e;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e8e8e8;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.2s;
            background: #fff;
        }

        .form-input:focus {
            outline: none;
            border-color: #FF5722;
            box-shadow: 0 0 0 4px rgba(255, 87, 34, 0.1);
        }

        .form-hint {
            font-size: 13px;
            color: #888;
            margin-top: 6px;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #FF5722 0%, #FF7043 100%);
            color: #fff;
            border: none;
            padding: 14px 28px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 87, 34, 0.3);
        }

        .alert-success-custom {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success-custom svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        .alert-error-custom {
            background: #ffebee;
            color: #c62828;
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-weight: 500;
        }

        .alert-error-custom ul {
            margin: 0;
            padding-left: 20px;
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h1>My Account</h1>
        <p>Manage your profile information and security settings</p>
    </div>

    <div class="profile-layout">
        <div class="profile-nav">
            <a href="{{ route('committee.profile.show', $conference->conference_id) }}" class="profile-nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                Profile Details
            </a>
            <a href="{{ route('committee.profile.password.edit', $conference->conference_id) }}"
                class="profile-nav-link active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                Change Password
            </a>
        </div>

        <div class="card">
            <div class="card-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                Change Password
            </div>

            @if(session('success'))
                <div class="alert-success-custom">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert-error-custom">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('committee.profile.password.update', $conference->conference_id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" class="form-input" id="current_password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" class="form-input" id="new_password" name="new_password" required>
                    <div class="form-hint">Must be at least 8 characters</div>
                </div>
                <div class="form-group">
                    <label for="new_password_confirmation">Confirm New Password</label>
                    <input type="password" class="form-input" id="new_password_confirmation"
                        name="new_password_confirmation" required>
                </div>
                <div style="text-align: right;">
                    <button type="submit" class="btn-primary-custom">Update Password</button>
                </div>
            </form>
        </div>
    </div>
@endsection