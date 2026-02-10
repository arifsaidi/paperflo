@extends('layouts.committee')

@section('title', 'Conference Status')

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

        .status-overview {
            display: flex;
            align-items: center;
            gap: 24px;
            padding: 24px;
            border-radius: 16px;
            background: #f8f9fa;
            margin-bottom: 32px;
        }

        .status-badge {
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 800;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .status-draft {
            background: #e0e0e0;
            color: #666;
        }

        .status-active {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .status-closed {
            background: #ffebee;
            color: #c62828;
        }

        .btn-activate {
            background: #2e7d32;
            color: #fff;
            border: none;
            padding: 16px 32px;
            border-radius: 16px;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-activate:hover {
            background: #1b5e20;
            transform: scale(1.02);
            box-shadow: 0 8px 16px rgba(46, 125, 50, 0.2);
        }

        .info-box {
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #bee5eb;
            background: #e2f3f5;
            color: #0c5460;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 24px;
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h1>Conference Status</h1>
        <p>Current accessibility and lifecycle state of <strong>{{ $conference->name }}</strong></p>
    </div>

    <div class="card">
        <div class="card-title">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path
                    d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83" />
            </svg>
            Current Lifecycle State
        </div>

        <div class="status-overview">
            <span class="status-badge status-{{ strtolower($conference->conference_status) }}">
                {{ $conference->conference_status }}
            </span>
            <div style="font-size: 14px; color: #666;">
                @if($conference->conference_status === 'draft')
                    The conference is currently in <strong>Draft</strong> mode. It is hidden from the public list and cannot
                    receive submissions.
                @elseif($conference->conference_status === 'active')
                    The conference is <strong>Active</strong> and visible to the public. It is open for registrations according
                    to set deadlines.
                @else
                    The conference is <strong>Closed</strong>. All management functions are read-only and no further submissions
                    are accepted.
                @endif
            </div>
        </div>

        @if($conference->conference_status === 'draft')
            <div class="info-box">
                <strong>Ready to launch?</strong><br>
                Activating the conference will make it public. Note that once activated, the <strong>Conference Date</strong> in
                "Conference Details" will be locked for integrity.
            </div>

            <form method="POST" action="{{ route('conference.activate', $conference->conference_id) }}"
                onsubmit="return confirm('Launch this conference to the public?')">
                @csrf
                <button type="submit" class="btn-activate">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M5 3l14 9-14 9V3z" />
                    </svg>
                    Activate Conference Now
                </button>
            </form>
        @endif
    </div>
@endsection