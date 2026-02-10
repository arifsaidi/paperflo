@extends('layouts.reviewer')

@section('title', 'My Account')

@section('styles')
    <style>
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #1a1a2e;
        }

        .profile-card {
            background: #fff;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 24px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f0f0f0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: #FF5722;
            box-shadow: 0 0 0 3px rgba(255, 87, 34, 0.1);
        }

        .form-control:disabled {
            background: #f5f5f7;
            color: #888;
            cursor: not-allowed;
        }

        .btn-save {
            background: #FF5722;
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-save:hover {
            background: #e64a19;
        }

        .actions-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #f0f0f0;
        }

        .link-secondary {
            color: #2962ff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }

        .link-secondary:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
        }

        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .alert-error {
            background: #ffebee;
            color: #c62828;
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-title">My Account</div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <div class="profile-card">
        <form method="POST" action="{{ route('reviewer.profile.update') }}">
            @csrf

            <h3 class="section-title">Personal Information</h3>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $reviewer->title) }}"
                        placeholder="e.g. Dr. / Prof.">
                </div>
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $reviewer->name) }}" required>
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Email Address (Read-only)</label>
                    <input type="email" class="form-control" value="{{ $reviewer->email }}" disabled>
                </div>
            </div>

            <h3 class="section-title" style="margin-top: 24px;">Professional Details</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Research Area</label>
                    <input type="text" name="area" class="form-control" value="{{ old('area', $reviewer->area) }}"
                        placeholder="e.g. AI / Cybersecurity">
                </div>
                <div class="form-group">
                    <label class="form-label">Institution</label>
                    <input type="text" name="institution" class="form-control"
                        value="{{ old('institution', $reviewer->institution) }}" placeholder="e.g. Global University">
                </div>
            </div>

            <div class="actions-row">
                <a href="{{ route('reviewer.profile.password.edit') }}" class="link-secondary">
                    Change Password
                </a>
                <button type="submit" class="btn-save" onclick="return confirm('Save changes to your profile?');">
                    Save
                </button>
            </div>
        </form>
    </div>
@endsection