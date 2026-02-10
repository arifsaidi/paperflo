@extends('layouts.committee')

@section('title', 'Paper Details')

@section('styles')
    <style>
        .paper-details-container {
            width: 100%;
            padding: 32px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        }

        .paper-details-container h2 {
            font-size: 18px;
            font-weight: 600;
            color: #1a1a2e;
            margin-bottom: 24px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            color: #888;
            margin-bottom: 6px;
            font-weight: 400;
        }

        .form-group .form-value {
            width: 100%;
            padding: 12px 14px;
            background: #f8f8f8;
            border: 1px solid #eee;
            border-radius: 8px;
            font-size: 14px;
            color: #333;
            box-sizing: border-box;
        }

        .form-group textarea.form-value {
            min-height: 120px;
            resize: vertical;
            line-height: 1.6;
        }

        .form-row {
            display: flex;
            gap: 20px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .section-divider {
            border: none;
            border-top: 1px solid #eee;
            margin: 28px 0;
        }

        .section-title {
            font-size: 14px;
            font-weight: 600;
            color: #1a1a2e;
            margin-bottom: 16px;
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

        .reviewer-comments {
            background: #f8f8f8;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 16px;
            font-size: 14px;
            line-height: 1.8;
            color: #333;
        }

        .reviewer-comments .section-heading {
            font-weight: 600;
            margin-top: 12px;
            margin-bottom: 4px;
        }

        .reviewer-comments .section-heading:first-child {
            margin-top: 0;
        }

        .decision-form {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-left: 16px;
        }

        .decision-form select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 13px;
            background: #fff;
            cursor: pointer;
        }

        .decision-form button {
            padding: 8px 16px;
            background: #1a1a2e;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .decision-form button:hover {
            background: #2d2d44;
        }

        .back-link {
            display: inline-block;
            margin-top: 24px;
            color: #666;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link:hover {
            color: #1a1a2e;
        }

        .action-link {
            display: inline-block;
            color: #1a1a2e;
            text-decoration: none;
            font-size: 14px;
            margin-top: 8px;
        }

        .action-link:hover {
            text-decoration: underline;
        }
    </style>
@endsection

@section('content')
    <div class="paper-details-container">
        <h2>Details</h2>

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert-error">{{ session('error') }}</div>
        @endif

        {{-- Paper ID --}}
        <div class="form-group">
            <label>Paper ID</label>
            <div class="form-value">{{ $paper->conference_name }}: {{ str_pad($paper->paper_id, 3, '0', STR_PAD_LEFT) }}
            </div>
        </div>

        {{-- Paper Title --}}
        <div class="form-group">
            <label>Paper Title</label>
            <div class="form-value">{{ $paper->title }}</div>
        </div>

        {{-- Author (Corresponding) --}}
        @php
            $corresponding = $authors->firstWhere('is_corresponding_author', 1);
            $coauthors = $authors->where('is_corresponding_author', 0);
        @endphp

        <div class="form-group">
            <label>Author</label>
            <div class="form-value">
                @if($corresponding)
                    {{ $corresponding->author_name }}({{ $corresponding->author_email }})
                @else
                    -
                @endif
            </div>
        </div>

        {{-- Co-Author --}}
        <div class="form-group">
            <label>Co-Author</label>
            <div class="form-value">
                @if($coauthors->isNotEmpty())
                    @foreach($coauthors as $index => $a)
                        {{ $a->author_name }}({{ $a->author_email }})@if(!$loop->last); @endif
                    @endforeach
                @else
                    -
                @endif
            </div>
        </div>

        {{-- Affiliation --}}
        <div class="form-group">
            <label>Affiliation</label>
            <div class="form-value">
                @if(!empty($affiliation))
                    @php
                        $parts = array_filter([
                            $affiliation->department ?? null,
                            $affiliation->faculty ?? null,
                            $affiliation->institution ?? null
                        ]);
                    @endphp
                    {{ implode(', ', $parts) ?: '-' }}
                @else
                    -
                @endif
            </div>
        </div>

        {{-- Abstract --}}
        <div class="form-group">
            <label>Abstract</label>
            <textarea class="form-value" readonly>{{ $paper->abstract_text }}</textarea>
        </div>

        {{-- Keywords --}}
        <div class="form-group">
            <label>Keywords</label>
            <div class="form-value">{{ $paper->keywords ?? '-' }}</div>
        </div>

        {{-- Track --}}
        <div class="form-group">
            <label>Track</label>
            <div class="form-value">{{ $paper->track_name }}</div>
        </div>

        {{-- Abstract Submission Date & Status --}}
        <div class="form-row">
            <div class="form-group">
                <label>Abstract Submission Date</label>
                <div class="form-value">{{ $paper->abstract_submitted_date ?? '-' }}</div>
            </div>
            <div class="form-group">
                <label>Abstract Status</label>
                <div class="form-value" style="display: flex; align-items: center; justify-content: space-between;">
                    <span>{{ $paper->abstract_status }}</span>

                    @if($paper->abstract_status === 'Submitted')
                        <form method="POST"
                            action="{{ route('committee.papers.decide.abstract', [$conference_id, $paper->paper_id]) }}"
                            class="decision-form">
                            @csrf
                            <select name="decision" required>
                                <option value="">-- Select --</option>
                                <option value="Accepted">Accepted</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                            <button type="submit">Save</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <hr class="section-divider">

        {{-- Full Paper Section --}}
        <div class="section-title">Full Paper</div>

        <div class="form-group">
            <label>Full Paper</label>
            <div class="form-value">
                @if(!empty($paper->fullpaper_file_path))
                    <a href="{{ asset('storage/' . $paper->fullpaper_file_path) }}" target="_blank" style="color: #1a1a2e;">
                        {{ basename($paper->fullpaper_file_path) }}
                    </a>
                @else
                    -
                @endif
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Full Paper Submission Date</label>
                <div class="form-value">{{ $paper->fullpaper_submitted_date ?? '-' }}</div>
            </div>
            <div class="form-group">
                <label>Paper Score</label>
                <div class="form-value">
                    @if(!is_null($avgScore))
                        {{ number_format($avgScore * 20, 2) }}%
                    @else
                        -
                    @endif
                </div>
            </div>
            <div class="form-group">
                <label>Full Paper Status</label>
                <div class="form-value" style="display: flex; align-items: center; justify-content: space-between;">
                    <span>{{ $paper->fullpaper_status ?? '-' }}</span>

                    @if(!empty($paper->fullpaper_status) && $paper->fullpaper_status === 'Submitted')
                        <form method="POST"
                            action="{{ route('committee.papers.decide.fullpaper', [$conference_id, $paper->paper_id]) }}"
                            class="decision-form">
                            @csrf
                            <select name="decision" required>
                                <option value="">-- Select --</option>
                                <option value="Accepted">Accepted</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                            <button type="submit">Save</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- Reviewer Comments --}}
        <div class="form-group">
            <label>Reviewer Comments</label>
            @if($reviews->isEmpty())
                <div class="reviewer-comments">
                    <p style="color: #888; text-align: center;">No reviews submitted yet.</p>
                </div>
            @else
                @foreach($reviews as $index => $review)
                    <div class="reviewer-comments" style="margin-bottom: 12px;">
                        <div class="section-heading">Reviewer {{ $index + 1 }}: {{ $review->reviewer_name }}</div>

                        @if($review->overall_comments)
                            <div style="margin-top: 8px;">
                                <div style="white-space: pre-wrap;">{{ $review->overall_comments }}</div>
                            </div>
                        @elseif($review->comments_to_author)
                            <div style="margin-top: 8px;">
                                <div style="white-space: pre-wrap; color: #555;">(No internal comments)
                                    {{ $review->comments_to_author }}</div>
                            </div>
                        @else
                            <div style="margin-top: 8px; color: #888; font-style: italic;">No comments provided.</div>
                        @endif

                        <div style="margin-top: 8px; font-size: 12px; color: #ccc;">
                            Submitted: {{ $review->submitted_at ?? '-' }}
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <a href="{{ route('committee.papers.reviews', [$conference_id, $paper->paper_id]) }}" class="action-link">
            📑 View Full Review Details
        </a>

        <hr class="section-divider">

        {{-- Camera-ready Paper Section --}}
        <div class="section-title">Camera-ready Paper</div>

        <div class="form-group">
            <label>Camera-ready Paper</label>
            <div class="form-value">
                @if(!empty($paper->cameraready_file_path))
                    <a href="{{ asset('storage/' . $paper->cameraready_file_path) }}" target="_blank" style="color: #1a1a2e;">
                        {{ basename($paper->cameraready_file_path) }}
                    </a>
                @else
                    -
                @endif
            </div>
        </div>

        <div class="form-group">
            <label>Camera-ready Paper Submission Date</label>
            <div class="form-value">{{ $paper->cameraready_submitted_date ?? '-' }}</div>
        </div>

        <hr class="section-divider">

        {{-- Payment Section --}}
        <div class="form-row">
            <div class="form-group">
                <label>Payment Status</label>
                <div class="form-value">{{ $paper->presenter_status ?? '-' }}</div>
            </div>
            <div class="form-group">
                <label>Payment Type</label>
                <div class="form-value">FPX</div>
            </div>
            <div class="form-group">
                <label>Payment Amount</label>
                <div class="form-value">500 MYR</div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Payment Detail</label>
                <div class="form-value">
                    @if($paper->presenter_status === 'Paid')
                        Paid
                    @else
                        -
                    @endif
                </div>
            </div>
            <div class="form-group">
                <label>Payment Date</label>
                <div class="form-value">
                    @if($paper->presenter_status === 'Paid')
                        {{ now()->format('Y-m-d') }}
                    @else
                        -
                    @endif
                </div>
            </div>
        </div>

        <a href="{{ route('committee.papers.index', $conference_id) }}" class="back-link">← Back to Paper List</a>
    </div>
@endsection