@extends('layouts.committee')

@section('title', 'Invite Reviewer')

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

        .invite-card {
            background: #fff;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            /* Full width matching show.blade.php */
        }

        .section-label {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a2e;
            margin-bottom: 24px;
            display: block;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            color: #888;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 10px 16px;
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            color: #333;
            outline: none;
            transition: border 0.2s;
        }

        .form-control:focus {
            border-color: #888;
        }

        .helper-text {
            font-size: 12px;
            color: #888;
            margin-top: 6px;
            display: block;
        }

        /* Buttons matching show.blade.php style */
        .btn-invite {
            font-family: 'Inter', sans-serif;
            display: inline-block;
            background: #FF5722;
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-invite:hover {
            background: #e64a19;
        }

        .btn-cancel {
            font-family: 'Inter', sans-serif;
            display: inline-block;
            background: #e0e0e0;
            color: #333;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-cancel:hover {
            background: #d0d0d0;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-error ul {
            margin: 0;
            padding-left: 20px;
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-title">Invite Reviewer</div>
    </div>

    @if ($errors->any())
        <div class="alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="invite-card">
        <form method="POST" action="{{ route('committee.reviewers.store', $conference_id) }}">
            @csrf

            <div class="section-label">Reviewer Details</div>

            <!-- Side-by-side Type and Title -->
            <div style="display: flex; gap: 24px;">
                <div class="form-group" style="flex: 1;">
                    <label class="form-label">Reviewer Type</label>
                    <select name="reviewer_type" class="form-control" required>
                        <option value="">-- Select --</option>
                        <option value="Internal">Internal</option>
                        <option value="External">External</option>
                    </select>
                </div>

                <div class="form-group" style="flex: 1;">
                    <label class="form-label">Title</label>
                    <select name="title" class="form-control">
                        <option value="">-- Select --</option>
                        <option value="Mr." {{ old('title') == 'Mr.' ? 'selected' : '' }}>Mr.</option>
                        <option value="Dr." {{ old('title') == 'Dr.' ? 'selected' : '' }}>Dr.</option>
                        <option value="Prof." {{ old('title') == 'Prof.' ? 'selected' : '' }}>Prof.</option>
                        <option value="Assoc. Prof." {{ old('title') == 'Assoc. Prof.' ? 'selected' : '' }}>Assoc. Prof.
                        </option>
                        <option value="Mrs." {{ old('title') == 'Mrs.' ? 'selected' : '' }}>Mrs.</option>
                        <option value="Ms." {{ old('title') == 'Ms.' ? 'selected' : '' }}>Ms.</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Area of Expertise</label>
                <input type="text" name="area" class="form-control" value="{{ old('area') }}">
            </div>

            <div class="form-group">
                <label class="form-label">Institution</label>
                <input type="text" name="institution" class="form-control" value="{{ old('institution') }}">
            </div>

            <div class="form-group">
                <label class="form-label">Temporary Password</label>
                <input type="text" name="temp_password" class="form-control" required>
                <small class="helper-text">This password will be emailed to the reviewer.</small>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 32px;">
                <a href="{{ route('committee.reviewers.all', $conference_id) }}" class="btn-cancel">
                    Cancel
                </a>
                <button type="submit" class="btn-invite"
                    onclick="return confirm('Invite this reviewer and create their account?');">
                    Invite Reviewer
                </button>
            </div>
        </form>
    </div>
@endsection