@extends('layouts.committee')

@section('title', 'Reviewer Info')

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

        .details-card {
            background: #fff;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
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

        /* Use standard form control style for read-only text */
        .form-control-static {
            width: 100%;
            padding: 10px 16px;
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            color: #333;
            line-height: 1.5;
        }

        /* For actual inputs/selects */
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

        /* Buttons */
        .btn-appoint {
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

        .btn-appoint:hover {
            background: #e64a19;
        }

        .btn-remove {
            font-family: 'Inter', sans-serif;
            display: inline-block;
            background: #FF0101;
            /* User requested FF0101 */
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

        .btn-remove:hover {
            background: #d50000;
        }

        .btn-disabled {
            font-family: 'Inter', sans-serif;
            display: inline-block;
            background: #e0e0e0;
            color: #888;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            border: none;
            cursor: not-allowed;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-title">Reviewer Info</div>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    <div class="details-card">
        <form method="POST"
            action="{{ $alreadyInvited ? route('committee.reviewers.remove', [$conference_id, $reviewer->reviewer_id]) : route('committee.reviewers.appoint', [$conference_id, $reviewer->reviewer_id]) }}">
            @csrf

            <div class="section-label">Reviewer Details</div>

            <div style="display: flex; gap: 24px;">
                <div class="form-group" style="flex: 1;">
                    <label class="form-label">Reviewer Type</label>
                    @if(!$alreadyInvited)
                        <select name="reviewer_type" class="form-control" required>
                            <option value="Internal">Internal</option>
                            <option value="External">External</option>
                        </select>
                    @else
                        <div class="form-control-static">{{ $reviewer->type ?? 'Internal' }}</div>
                    @endif
                </div>

                <div class="form-group" style="flex: 1;">
                    <label class="form-label">Title</label>
                    <select class="form-control" disabled>
                        <option value="">-</option>
                        <option value="Mr." {{ ($reviewer->title ?? '') == 'Mr.' ? 'selected' : '' }}>Mr.</option>
                        <option value="Dr." {{ ($reviewer->title ?? '') == 'Dr.' ? 'selected' : '' }}>Dr.</option>
                        <option value="Prof." {{ ($reviewer->title ?? '') == 'Prof.' ? 'selected' : '' }}>Prof.</option>
                        <option value="Assoc. Prof." {{ ($reviewer->title ?? '') == 'Assoc. Prof.' ? 'selected' : '' }}>Assoc.
                            Prof.</option>
                        <option value="Mrs." {{ ($reviewer->title ?? '') == 'Mrs.' ? 'selected' : '' }}>Mrs.</option>
                        <option value="Ms." {{ ($reviewer->title ?? '') == 'Ms.' ? 'selected' : '' }}>Ms.</option>
                        <!-- Fallback for other titles not in list -->
                        @if(!in_array($reviewer->title ?? '', ['Mr.', 'Dr.', 'Prof.', 'Assoc. Prof.', 'Mrs.', 'Ms.', '', null]))
                            <option value="{{ $reviewer->title }}" selected>{{ $reviewer->title }}</option>
                        @endif
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Name</label>
                <div class="form-control-static">{{ $reviewer->name }}</div>
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <div class="form-control-static">{{ $reviewer->email }}</div>
            </div>

            <div class="form-group">
                <label class="form-label">Area</label>
                <div class="form-control-static">{{ $reviewer->area ?? '-' }}</div>
            </div>

            <div class="form-group">
                <label class="form-label">Institution</label>
                <div class="form-control-static">{{ $reviewer->institution ?? '-' }}</div>
            </div>

            <!-- Buttons Logic -->
            <div
                style="display: flex; justify-content: {{ $alreadyInvited ? 'space-between' : 'flex-end' }}; gap: 12px; margin-top: 32px;">
                @if(!$alreadyInvited)
                    <button type="submit" class="btn-appoint"
                        onclick="return confirm('Appoint {{ $reviewer->name }}?')">Appoint</button>
                @else
                    <button type="submit" class="btn-remove" onclick="return confirm('Remove {{ $reviewer->name }}?')">Remove
                        Reviewer</button>
                    <button type="button" class="btn-disabled" disabled>Already Appointed</button>
                @endif
            </div>
        </form>
    </div>
@endsection