@extends('layouts.participant_horizontal')

@section('title', $paper->title)

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

        /* Main Content */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: #666;
            font-weight: 500;
            margin-bottom: 24px;
            font-size: 0.9rem;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: #FF5722;
        }

        .card {
            background: #fff;
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f0f0f2;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1a1a2e;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 6px;
        }

        .value {
            font-weight: 600;
            color: #1a1a2e;
            display: block;
        }

        /* Badges */
        .badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
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

        /* Form Controls */
        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .form-input,
        .form-textarea,
        .form-select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            font-family: inherit;
            font-size: 0.95rem;
            transition: border-color 0.2s;
        }

        .form-input:focus,
        .form-textarea:focus {
            border-color: #FF5722;
            outline: none;
        }

        /* Buttons */
        .btn-primary {
            background: #FF5722;
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary:hover {
            background: #e64a19;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #f1f3f5;
            color: #495057;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-outline-danger {
            background: transparent;
            color: #c62828;
            border: 2px solid #c62828;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-outline-danger:hover {
            background: #c62828;
            color: #fff;
        }

        /* Author List */
        .author-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f8f8fa;
        }

        .author-item:last-child {
            border-bottom: none;
        }

        .author-name {
            font-weight: 600;
        }

        .author-email {
            font-size: 0.85rem;
            color: #888;
        }

        .author-badge {
            font-size: 0.7rem;
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 10px;
            border-radius: 10px;
            font-weight: 700;
        }

        .info-block {
            margin-bottom: 24px;
        }

        .info-block:last-child {
            margin-bottom: 0;
        }

        .abstract-text {
            color: #555;
            white-space: pre-wrap;
            font-size: 0.95rem;
            background: #fcfcfd;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #f0f0f2;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #f8f9fa;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>
@endsection

@section('content')

    <div class="conference-nav">
        <div class="nav-container">
            <a href="{{ route('participant.conference.show', $paper->conference_id) }}" class="conf-nav-item">
                Conference Details
            </a>
            <a href="{{ route('participant.registration.show', $paper->conference_id) }}" class="conf-nav-item">
                My Registration
            </a>
            <a href="{{ route('conference.submissions.index', $paper->conference_id) }}" class="conf-nav-item active">
                My Submissions
            </a>
        </div>
    </div>

    <main class="container">
        <a href="{{ route('conference.submissions.index', $conference->conference_id) }}" class="back-link">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd"
                    d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z" />
            </svg>
            Back to My Submissions
        </a>

        @if(session('success'))
            <div class="alert-success">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="card">
            <h2 class="section-title" style="margin-bottom: 24px;">General Information</h2>

            <div class="info-block">
                <label class="label">Title</label>
                <span class="value" style="font-size: 1.2rem;">{{ $paper->title }}</span>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                <div class="info-block">
                    <label class="label">Track</label>
                    <span class="value">{{ $paper->track_name }}</span>
                </div>
                <div class="info-block">
                    <label class="label">ID</label>
                    <span class="value">#{{ $paper->paper_id }}</span>
                </div>
            </div>
        </div>

        @if($canManageSubmission && $paper->abstract_status === 'Draft')
            <div class="card">
                <div class="section-header">
                    <h2 class="section-title">Edit Submission</h2>
                    <span class="badge badge-draft">Abstract Draft</span>
                </div>
                <form method="POST" action="{{ route('submissions.update', $paper->paper_id) }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Title</label>
                        <input name="title" value="{{ $paper->title }}" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Abstract</label>
                        <textarea name="abstract_text" class="form-textarea" style="height:200px"
                            required>{{ $paper->abstract_text }}</textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                        <div class="form-group">
                            <label class="form-label">Track</label>
                            <select name="track_id" class="form-select" required>
                                @foreach($tracks as $t)
                                    <option value="{{ $t->track_id }}" {{ $t->track_id == $paper->track_id ? 'selected' : '' }}>
                                        {{ $t->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Keywords</label>
                            <input name="keywords" value="{{ $paper->keywords }}" class="form-input" required>
                        </div>
                    </div>

                    <div style="display: flex; gap: 16px; margin-top: 20px;">
                        <button type="submit" name="action" value="update" class="btn-secondary">
                            Update Draft
                        </button>
                        <button type="submit" name="action" value="submit" class="btn-primary"
                            onclick="return confirm('Are you sure you want to submit this abstract? You will not be able to edit it afterwards.');">
                            Submit Abstract
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="card">
                <div class="section-header">
                    <h2 class="section-title">Abstract Content</h2>
                    @php
                        $statusClass = 'badge-draft';
                        if ($paper->abstract_status === 'Accepted')
                            $statusClass = 'badge-accepted';
                        elseif ($paper->abstract_status === 'Rejected')
                            $statusClass = 'badge-rejected';
                        elseif ($paper->abstract_status === 'Submitted')
                            $statusClass = 'badge-pending';
                    @endphp
                    <span class="badge {{ $statusClass }}">Abstract {{ $paper->abstract_status }}</span>
                </div>
                <div class="info-block">
                    <label class="label">Keywords</label>
                    <span class="value">{{ $paper->keywords }}</span>
                </div>
                <div class="info-block">
                    <label class="label">Abstract Text</label>
                    <div class="abstract-text">{{ $paper->abstract_text }}</div>
                </div>
                @if($paper->abstract_status !== 'Draft')
                    <p style="margin-top: 20px; color: #888; font-size: 0.9rem; font-style: italic;">
                        This abstract has been submitted and can no longer be edited.
                    </p>
                @endif
            </div>
        @endif

        <div class="card">
            <h2 class="section-title" style="margin-bottom: 24px;">Authors</h2>
            <div class="author-list">
                @foreach($authors as $a)
                    <div class="author-item">
                        <div>
                            <span class="author-name">{{ $a->author_name }}</span><br>
                            <span class="author-email">{{ $a->author_email }}</span>
                        </div>
                        @if($a->is_corresponding_author)
                            <span class="author-badge">Corresponding Author</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        @if($canManageSubmission && $paper->abstract_status === 'Submitted')
            <div class="card" style="border: 1px solid #ffebee;">
                <h2 class="section-title" style="color: #c62828; margin-bottom: 20px;">Withdraw Abstract</h2>
                <p style="color: #666; margin-bottom: 24px; font-size: 0.9rem;">
                    If you wish to withdraw your submission from this conference, please provide a reason below.
                </p>
                <form method="POST" action="{{ route('submissions.withdraw', $paper->paper_id) }}"
                    onsubmit="return confirm('Are you sure you want to withdraw this abstract? This action cannot be undone.');">
                    @csrf
                    <div class="form-group">
                        <textarea name="reason" class="form-textarea" style="height:100px" required
                            placeholder="Reason for withdrawal..."></textarea>
                    </div>
                    <button type="submit" class="btn-outline-danger">Withdraw Abstract</button>
                </form>
            </div>
        @endif

        {{-- Full Paper Section --}}
        <div class="card">
            <div class="section-header">
                <h2 class="section-title">Full Paper Submission</h2>
                @if($paper->fullpaper_status)
                    @php
                        $fpClass = 'badge-pending';
                        if ($paper->fullpaper_status === 'Accepted')
                            $fpClass = 'badge-accepted';
                        elseif ($paper->fullpaper_status === 'Rejected')
                            $fpClass = 'badge-rejected';
                    @endphp
                    <span class="badge {{ $fpClass }}">{{ $paper->fullpaper_status }}</span>
                @endif
            </div>

            <p style="margin-bottom: 24px; font-size: 0.95rem; color: #666;">
                <strong>Deadline:</strong>
                {{ $fullpaperDeadline ? \Carbon\Carbon::parse($fullpaperDeadline)->format('M d, Y') : 'Not set' }}
            </p>

            @if($paper->abstract_status !== 'Accepted')
                <div style="background: #f8f9fa; padding: 24px; border-radius: 12px; text-align: center; color: #888;">
                    Full paper upload will be available after the abstract is <strong>Accepted</strong>.
                </div>
            @elseif(!empty($isFullpaperDeadlinePassed) && $isFullpaperDeadlinePassed)
                <div style="background: #fff5f5; color: #c62828; padding: 20px; border-radius: 12px; font-weight: 600;">
                    Submission is closed (deadline passed).
                </div>
            @else
                @if(!empty($paper->fullpaper_status))
                    <div class="info-block"
                        style="background: #fcfcfd; padding: 20px; border-radius: 12px; border: 1px solid #f0f0f2; margin-bottom: 24px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <label class="label">Current File</label>
                                <a href="{{ asset('storage/' . $paper->fullpaper_file_path) }}" target="_blank" class="value"
                                    style="color: #FF5722;">
                                    View Uploaded Paper
                                </a>
                                <span style="font-size: 0.8rem; color: #888; margin-top: 4px; display: block;">
                                    Submitted on:
                                    {{ \Carbon\Carbon::parse($paper->fullpaper_submitted_date)->format('M d, Y H:i') }}
                                </span>
                            </div>
                            @if($paper->fullpaper_status === 'Submitted')
                                <span
                                    style="font-size: 0.85rem; background: #fff8e1; color: #f57f17; padding: 6px 12px; border-radius: 50px; font-weight: 600;">
                                    Under Review
                                </span>
                            @endif
                        </div>
                    </div>
                @endif

                @if(!empty($paper->fullpaper_status) && in_array($paper->fullpaper_status, ['Accepted', 'Rejected']))
                    <p style="color:#888; font-style: italic; font-size: 0.9rem;">
                        Full paper has been decided by the committee and can no longer be modified.
                    </p>
                @elseif(!empty($paper->fullpaper_status) && $paper->fullpaper_status === 'Withdrawn')
                    <p style="color:#c62828; font-style: italic; font-size: 0.9rem;">
                        This full paper has been withdrawn.
                    </p>
                @elseif(empty($paper->fullpaper_status))
                    @if($canManageSubmission)
                        <form method="POST" action="{{ route('submissions.fullpaper.upload', $paper->paper_id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">Upload Paper (PDF/DOC/DOCX)</label>
                                <input type="file" name="fullpaper_file" class="form-input" required>
                                <p style="font-size: 0.8rem; color: #888; margin-top: 8px;">
                                    Max file size: 10MB.
                                </p>
                            </div>
                            <button type="submit" class="btn-primary" onclick="return confirm('Submit full paper now?');">
                                Submit Full Paper
                            </button>
                        </form>
                    @endif
                @endif
            @endif
        </div>

        @if($canManageSubmission && !empty($paper->fullpaper_status) && $paper->fullpaper_status === 'Submitted')
            <div class="card" style="border: 1px solid #ffebee;">
                <h2 class="section-title" style="color: #c62828; margin-bottom: 20px;">Withdraw Full Paper</h2>
                <p style="color: #666; margin-bottom: 24px; font-size: 0.9rem;">
                    If you wish to withdraw your full paper submission, please provide a reason below.
                    This can only be done before the committee has made a decision.
                </p>
                <form method="POST" action="{{ route('submissions.fullpaper.withdraw', $paper->paper_id) }}"
                    onsubmit="return confirm('Are you sure you want to withdraw this full paper? This action cannot be undone.');">
                    @csrf
                    <div class="form-group">
                        <textarea name="reason" class="form-textarea" style="height:100px" required
                            placeholder="Reason for withdrawal..."></textarea>
                    </div>
                    <button type="submit" class="btn-outline-danger">Withdraw Full Paper</button>
                </form>
            </div>
        @endif

        {{-- Presenter Section --}}
        @if($paper->fullpaper_status === 'Accepted')
            <div class="card">
                <h2 class="section-title" style="margin-bottom: 24px;">Presenter Assignment</h2>

                @php
                    $presenter = DB::table('presenter_assignment')
                        ->where('paper_id', $paper->paper_id)
                        ->first();
                @endphp

                @if($presenter)
                    @php
                        if ($presenter->participant_id) {
                            $presenterInfo = DB::table('participant')->where('participant_id', $presenter->participant_id)->first();
                            $presenterName = $presenterInfo->name ?? '-';
                            $presenterEmail = $presenterInfo->email ?? '-';
                        } else {
                            $authorPresenter = DB::table('paper_author')->where('paper_id', $paper->paper_id)->where('author_email', $presenter->presenter_email)->first();
                            $presenterName = $authorPresenter->author_name ?? '-';
                            $presenterEmail = $authorPresenter->author_email ?? '-';
                        }
                    @endphp

                    <div
                        style="background: #fcfcfd; padding: 24px; border-radius: 12px; border: 1px solid #f0f0f2; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <label class="label">Assigned Presenter</label>
                            <span class="value" style="font-size: 1.1rem;">{{ $presenterName }}</span>
                            <span style="color: #888; font-size: 0.9rem;">{{ $presenterEmail }}</span>
                        </div>
                        <div style="text-align: right;">
                            <label class="label">Registration Status</label>
                            <span class="badge {{ $presenter->presenter_status === 'Paid' ? 'badge-accepted' : 'badge-pending' }}">
                                {{ $presenter->presenter_status }}
                            </span>
                        </div>
                    </div>
                @elseif($canManageSubmission)
                    <form method="POST" action="{{ route('submissions.presenter.select', $paper->paper_id) }}">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Select Presenter among Authors</label>
                            <select name="author_email" class="form-select" required>
                                <option value="">-- Select --</option>
                                @foreach($authors as $a)
                                    <option value="{{ $a->author_email }}">
                                        {{ $a->author_name }} ({{ $a->author_email }})
                                        @if($a->is_corresponding_author) – Corresponding @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn-primary" onclick="return confirm('Confirm selected presenter?');">
                            Confirm Presenter
                        </button>
                    </form>
                @endif
            </div>

            {{-- Payment Section --}}
            @if($canPresenterActions)
                @php
                    $presenterAssignment = DB::table('presenter_assignment')->where('paper_id', $paper->paper_id)->first();
                    $presenterFee = 'RM 500';
                @endphp

                @if($presenterAssignment)
                    <div class="card">
                        <h2 class="section-title" style="margin-bottom: 24px;">Presenter Payment</h2>
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; background: #fafafa; padding: 24px; border-radius: 12px;">
                            <div>
                                <label class="label">Presenter Fee</label>
                                <span class="value" style="font-size: 1.5rem; color: #1a1a2e;">{{ $presenterFee }}</span>
                            </div>
                            <div>
                                @if($presenterAssignment->presenter_status === 'Confirmed')
                                    <form method="POST" action="{{ route('presenter.pay', $paper->paper_id) }}">
                                        @csrf
                                        <button type="submit" class="btn-primary"
                                            onclick="return confirm('Confirm presenter fee payment?');">
                                            Pay Fee Now
                                        </button>
                                    </form>
                                @elseif($presenterAssignment->presenter_status === 'Paid')
                                    <div style="color: #2e7d32; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                            <path
                                                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                                        </svg>
                                        Payment Completed
                                    </div>
                                @else
                                    <span style="color: #888; font-style: italic;">Locked (Pending Confirmation)</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        @endif

        {{-- Camera Ready Section --}}
        @if($paper->fullpaper_status === 'Accepted')
            <div class="card">
                <div class="section-header">
                    <h2 class="section-title">Camera-ready Submission</h2>
                    @php
                        $cr = DB::table('cameraready_submission')->where('paper_id', $paper->paper_id)->first();
                    @endphp
                    @if($cr)
                        <span class="badge badge-accepted">{{ $cr->cameraready_status }}</span>
                    @endif
                </div>

                @if(!$presenter)
                    <div style="background: #f8f9fa; padding: 24px; border-radius: 12px; text-align: center; color: #888;">
                        A presenter must be selected before camera-ready submission.
                    </div>
                @elseif($presenter->presenter_status !== 'Paid')
                    <div style="background: #f8f9fa; padding: 24px; border-radius: 12px; text-align: center; color: #888;">
                        Locked until presenter fee is <strong>Paid</strong>.
                    </div>
                @else
                    @if(!$canManageSubmission)
                        <div style="background: #f8f9fa; padding: 24px; border-radius: 12px; text-align: center; color: #888;">
                            Only the <strong>corresponding author</strong> may upload the camera-ready paper.
                        </div>
                    @elseif($cr)
                        <div class="info-block"
                            style="background: #fcfcfd; padding: 24px; border-radius: 12px; border: 1px solid #f0f0f2;">
                            <label class="label">Submitted Camera-ready</label>
                            <a href="{{ asset('storage/' . $cr->file_path) }}" target="_blank" class="value"
                                style="color: #FF5722; font-size: 1.1rem;">
                                View Final Paper
                            </a>
                            <p style="font-size: 0.85rem; color: #888; margin-top: 10px;">
                                Submitted on: {{ \Carbon\Carbon::parse($cr->submitted_date)->format('M d, Y') }}
                            </p>
                            <p style="color: #2e7d32; font-weight: 600; font-size: 0.9rem; margin-top: 16px;">
                                Camera-ready submission is final.
                            </p>
                        </div>
                    @else
                        <form method="POST" action="{{ route('submissions.cameraready.upload', $paper->paper_id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">Upload Camera-ready Paper (PDF/DOC/DOCX)</label>
                                <input type="file" name="cameraready_file" class="form-input" required>
                            </div>
                            <button type="submit" class="btn-primary"
                                onclick="return confirm('Submit camera-ready paper? This action is final.');">
                                Submit Camera-ready Paper
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        @endif
    </main>
@endsection