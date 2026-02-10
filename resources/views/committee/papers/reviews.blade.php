@extends('layouts.committee')

@section('title', 'Paper Review Summary')

@section('styles')
    <style>
        .review-summary-container {
            max-width: 900px;
            padding: 32px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        }

        .review-summary-container h2 {
            font-size: 18px;
            font-weight: 600;
            color: #1a1a2e;
            margin-bottom: 8px;
        }

        .review-summary-container h3 {
            font-size: 15px;
            font-weight: 600;
            color: #1a1a2e;
            margin: 24px 0 12px;
        }

        .conference-name {
            font-size: 13px;
            color: #666;
            margin-bottom: 20px;
        }

        .section-divider {
            border: none;
            border-top: 1px solid #eee;
            margin: 24px 0;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .info-table th,
        .info-table td {
            padding: 12px 14px;
            text-align: left;
            border: 1px solid #eee;
            font-size: 14px;
        }

        .info-table th {
            background: #f8f8f8;
            font-weight: 500;
            width: 25%;
            color: #555;
        }

        .info-table td {
            color: #333;
        }

        .author-section p {
            margin: 8px 0;
            font-size: 14px;
            color: #333;
        }

        .author-section p b {
            color: #555;
        }

        .author-list {
            list-style: none;
            padding: 0;
            margin: 8px 0;
        }

        .author-list li {
            padding: 6px 0;
            font-size: 14px;
            color: #333;
        }

        .score-badge {
            display: inline-block;
            padding: 8px 16px;
            background: #e3f2fd;
            color: #1565c0;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
        }

        .btn {
            display: inline-block;
            padding: 10px 18px;
            font-size: 14px;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary {
            background: #1a1a2e;
            color: #fff;
            border: none;
        }

        .btn-primary:hover {
            background: #2d2d44;
        }

        .btn-link {
            color: #1a1a2e;
            background: none;
            padding: 8px 0;
        }

        .btn-link:hover {
            text-decoration: underline;
        }

        .back-link {
            display: inline-block;
            color: #666;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link:hover {
            color: #1a1a2e;
        }

        .empty-state {
            color: #888;
            font-size: 14px;
            padding: 16px;
            background: #f8f8f8;
            border-radius: 8px;
            text-align: center;
        }

        .action-bar {
            margin-bottom: 16px;
        }
    </style>
@endsection

@section('content')
    <div class="review-summary-container">
        <h2>Paper Review Summary</h2>
        <p class="conference-name">{{ $paper->conference_name }}</p>

        <hr class="section-divider">

        <h3>Paper Information</h3>
        <table class="info-table">
            <tr>
                <th>Paper ID</th>
                <td>{{ $paper->paper_id }}</td>
            </tr>
            <tr>
                <th>Paper Title</th>
                <td>{{ $paper->title }}</td>
            </tr>
            <tr>
                <th>Track</th>
                <td>{{ $paper->track_name }}</td>
            </tr>
            <tr>
                <th>Keywords</th>
                <td>{{ $paper->keywords }}</td>
            </tr>
        </table>

        <h3>Authors</h3>
        <div class="author-section">
            @php
                $corresponding = $authors->firstWhere('is_corresponding_author', 1);
                $coauthors = $authors->where('is_corresponding_author', 0);
            @endphp

            @if($corresponding)
                <p>
                    <b>Corresponding Author:</b>
                    {{ $corresponding->author_name }} ({{ $corresponding->author_email }})
                </p>
            @endif

            @if($coauthors->isNotEmpty())
                <p><b>Co-authors:</b></p>
                <ul class="author-list">
                    @foreach($coauthors as $a)
                        <li>{{ $a->author_name }} ({{ $a->author_email }})</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <hr class="section-divider">

        <h3>Average Score</h3>
        <p>
            @if($avgScore)
                <span class="score-badge">{{ number_format($avgScore * 20, 2) }}%</span>
            @else
                <span class="score-badge" style="background: #f5f5f5; color: #888;">-</span>
            @endif
        </p>

        <hr class="section-divider">

        <h3>Reviewer Scores</h3>

        <div class="action-bar">
            <a href="{{ route('committee.papers.reviews', [$conference_id, $paper->paper_id]) }}" class="btn btn-primary">
                ➕ Assign Reviewer
            </a>
        </div>

        @if($reviews->isEmpty())
            <div class="empty-state">No reviewers assigned yet.</div>
        @else
            <table class="info-table">
                <tr>
                    <th style="width: 5%">No.</th>
                    <th>Reviewer</th>
                    <th>Area</th>
                    <th>Assigned Date</th>
                    <th>Status</th>
                    <th>Score</th>
                    <th>Actions</th>
                </tr>

                @foreach($reviews as $index => $r)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $r->reviewer_name }}</td>
                        <td>{{ $r->reviewer_area ?? '-' }}</td>
                        <td>{{ $r->assigned_date ?? '-' }}</td>
                        <td>{{ $r->review_status }}</td>
                        <td>
                            {{ $r->score !== null ? number_format($r->score * 20, 2) . '%' : '-' }}
                        </td>
                        <td>
                            <a href="{{ route('committee.review.details', [$conference_id, $paper->paper_id, $r->review_id]) }}"
                                class="btn btn-link">
                                View
                            </a>
                        </td>
                    </tr>
                @endforeach
            </table>
        @endif

        <hr class="section-divider">

        <a href="{{ route('committee.papers.show', [$conference_id, $paper->paper_id]) }}" class="back-link">
            ← Back to Paper Details
        </a>
    </div>
@endsection