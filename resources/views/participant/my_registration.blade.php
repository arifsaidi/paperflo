@extends('layouts.participant_horizontal')

@section('title', 'My Registration - ' . $conference->name)

@section('hero')
    <header class="hero">
        @if($conference->logo)
            <img src="{{ asset('storage/' . $conference->logo) }}" alt="Conference Logo"
                style="max-height: 100px; margin-bottom: 24px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            <br>
        @endif
        <span class="status-badge">{{ $conference->conference_status }}</span>
        <h1>{{ $conference->name }}</h1>
    </header>
@endsection

@section('styles')
    <style>
        /* Main Content */
        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .card {
            background: #fff;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 24px;
            color: #1a1a2e;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 16px 0;
            border-bottom: 1px solid #f0f0f2;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #666;
            font-weight: 500;
        }

        .detail-value {
            font-weight: 700;
            color: #1a1a2e;
        }

        .status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .status-active {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .status-pending {
            background: #fff8e1;
            color: #f57f17;
        }

        .btn-confirm {
            background: #FF5722;
            color: #fff;
            border: none;
            padding: 14px 24px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
            transition: all 0.2s;
        }

        .btn-confirm:hover {
            background: #e64a19;
            transform: translateY(-1px);
        }

        .success-msg {
            color: #2e7d32;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            background: #e8f5e9;
            padding: 12px;
            border-radius: 10px;
            margin-top: 10px;
        }
    </style>
@endsection

@section('content')
    <div class="conference-nav">
        <div class="nav-container">
            <a href="{{ route('participant.conference.show', $conference->conference_id) }}"
                class="conf-nav-item {{ request()->routeIs('participant.conference.show') ? 'active' : '' }}">
                Conference Details
            </a>
            <a href="{{ route('participant.registration.show', $conference->conference_id) }}"
                class="conf-nav-item {{ request()->routeIs('participant.registration.show') ? 'active' : '' }}">
                My Registration
            </a>
            @if($registration && $registration->registration_type === 'author')
                <a href="{{ route('conference.submissions.index', $conference->conference_id) }}"
                    class="conf-nav-item {{ request()->routeIs('conference.submissions.*') ? 'active' : '' }}">
                    My Submissions
                </a>
            @endif
        </div>
    </div>

    <main class="container">
        <div class="card">
            <h2 class="section-title">
                Registration Details
            </h2>

            @if($registration)
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span class="status-badge status-active">{{ ucfirst($registration->registration_status) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Role</span>
                    <span class="detail-value">{{ ucfirst($registration->registration_type) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Category</span>
                    <span class="detail-value">{{ str_replace('_', ' ', ucfirst($registration->registration_category)) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Registered On</span>
                    <span
                        class="detail-value">{{ \Carbon\Carbon::parse($registration->registration_date)->format('M d, Y') }}</span>
                </div>
            @else
                <p style="color:#666; text-align:center; padding: 20px;">
                    You have not completed conference registration yet.
                </p>
            @endif
        </div>

        @if($isInvitedPresenter)
            <div class="card">
                <h2 class="section-title">Presenter Registration</h2>

                <div class="detail-row">
                    <span class="detail-label">Role</span>
                    <span class="detail-value">Presenter</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span
                        class="status-badge {{ $presenterRegistrationStatus === 'Confirmed' ? 'status-active' : 'status-pending' }}">
                        {{ $presenterRegistrationStatus }}
                    </span>
                </div>

                @if($presenterRegistrationStatus === 'Pending Confirmation')
                    <form method="POST" action="{{ route('presenter.confirm', $conference->conference_id) }}">
                        @csrf
                        <button type="submit" class="btn-confirm"
                            onclick="return confirm('Confirm registration as presenter for this conference?');">
                            Confirm Registration
                        </button>
                    </form>
                @elseif($presenterRegistrationStatus === 'Confirmed')
                    <div class="success-msg">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                            <path
                                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                        </svg>
                        Presenter registration confirmed
                    </div>
                @endif
            </div>
        @endif
    </main>
@endsection