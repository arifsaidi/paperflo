@extends('layouts.participant_horizontal')

@section('title', 'Change Password')

@section('styles')
    <style>
        .container {
            max-width: 500px;
            margin: 60px auto 40px;
            padding: 0 20px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: #666;
            font-weight: 500;
            margin-bottom: 20px;
            font-size: 0.85rem;
        }

        .back-link:hover {
            color: #1a1a2e;
        }

        .profile-card {
            background: #fff;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
        }

        .page-title {
            font-size: 1.4rem;
            font-weight: 800;
            margin-bottom: 6px;
            color: #1a1a2e;
        }

        .page-subtitle {
            color: #666;
            font-size: 0.85rem;
            margin-bottom: 24px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #666;
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 0.9rem;
            color: #1a1a2e;
            font-family: inherit;
            transition: all 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: #FF5722;
            box-shadow: 0 0 0 3px rgba(255, 87, 34, 0.1);
        }

        .btn-save {
            width: 100%;
            background: #4E342E;
            color: #fff;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 8px;
        }

        .btn-save:hover {
            background: #3e2723;
        }

        .alert {
            padding: 10px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .alert-error {
            background-color: #ffebee;
            color: #c62828;
            border-left: 4px solid #c62828;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <a href="{{ route('participant.profile.show') }}" class="back-link">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd"
                    d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z" />
            </svg>
            Back to My Account
        </a>

        <div class="profile-card">
            <h1 class="page-title">Change Password</h1>
            <p class="page-subtitle">Secure your account with a new password.</p>

            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <ul style="margin:0; padding-left:20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('participant.profile.password.update') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-control" placeholder="••••••••" required>
                </div>

                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" name="new_password" class="form-control" placeholder="Min. 6 characters"
                        required>
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="new_password_confirmation" class="form-control"
                        placeholder="Confirm your new password" required>
                </div>

                <button type="submit" class="btn-save">
                    Update Password
                </button>
            </form>
        </div>
    </div>
@endsection