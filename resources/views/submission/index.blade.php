@extends('layouts.participant_horizontal')

@section('title', 'My Submissions - ' . $conference->name)

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
        /* Page Header */
        .page-header {
            margin-bottom: 24px;
        }

        .page-header h1 {
            font-size: 1.8rem;
            font-weight: 800;
            color: #1a1a2e;
        }

        .page-header p {
            color: #666;
            margin-top: 4px;
        }

        /* Content */
        .submissions-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 30px 0 24px;
        }

        .submissions-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a1a2e;
        }

        .btn-new-submission {
            background: #FF5722;
            color: #fff;
            padding: 12px 24px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-new-submission:hover {
            background: #e64a19;
            transform: translateY(-1px);
        }

        .card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .submission-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .submission-table th {
            padding: 18px 24px;
            background: #fcfcfd;
            border-bottom: 1px solid #f0f0f2;
            color: #888;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .submission-table td {
            padding: 20px 24px;
            border-bottom: 1px solid #f8f8fa;
            color: #333;
            font-size: 15px;
            vertical-align: middle;
        }

        .submission-table tr:hover td {
            background: #fafafb;
        }

        .paper-title {
            font-weight: 600;
            color: #1a1a2e;
            display: block;
            margin-bottom: 4px;
        }

        /* Badges */
        .badge {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-pending {
            background: #fff8e1;
            color: #f57f17;
        }

        .badge-accepted {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .badge-rejected {
            background: #ffebee;
            color: #c62828;
        }

        .badge-draft {
            background: #f5f5f7;
            color: #666;
        }

        .btn-view {
            padding: 8px 16px;
            border-radius: 8px;
            background: #f8f9fa;
            color: #666;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-view:hover {
            background: #FF5722;
            color: #fff;
        }

        .empty-state {
            padding: 60px 24px;
            text-align: center;
            color: #888;
        }

        .alert {
            padding: 16px 24px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-weight: 500;
            font-size: 14px;
        }

        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #2e7d32;
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
            <a href="{{ route('conference.submissions.index', $conference->conference_id) }}"
                class="conf-nav-item {{ request()->routeIs('conference.submissions.*') ? 'active' : '' }}">
                My Submissions
            </a>
        </div>
    </div>

    <main class="container">
        <div class="submissions-header">
            <h2>My Submissions</h2>
            @if(!isset($isPresenter) || !$isPresenter)
                <a href="{{ route('conference.submissions.create', $conference->conference_id) }}" class="btn-new-submission">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 4v16m8-8H4" />
                    </svg>
                    New Submission
                </a>
            @endif
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <table class="submission-table">
                <thead>
                    <tr>
                        <th width="45%">Paper Details</th>
                        <th>Track</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th style="text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($papers as $p)
                        <tr>
                            <td>
                                <span class="paper-title">{{ $p->title }}</span>
                                <span style="font-size: 13px; color: #888;">ID: #{{ $p->paper_id }}</span>
                            </td>
                            <td>{{ $p->track_name }}</td>
                            <td>{{ $p->role }}</td>
                            <td>
                                @php
                                    $statusClass = 'badge-pending';
                                    $status = $p->final_status;
                                    if (str_contains(strtolower($status), 'accepted')) {
                                        $statusClass = 'badge-accepted';
                                    } elseif (str_contains(strtolower($status), 'rejected')) {
                                        $statusClass = 'badge-rejected';
                                    } elseif (str_contains(strtolower($status), 'draft')) {
                                        $statusClass = 'badge-draft';
                                    }
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ $status }}</span>
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('submissions.show', $p->paper_id) }}" class="btn-view">
                                    Manage
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <p>No submissions found for this conference.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>
@endsection