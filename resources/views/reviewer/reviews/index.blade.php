@extends('layouts.reviewer')

@section('title', 'My Review Assignments')

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

        .review-section {
            background: #fff;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .custom-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .custom-table th {
            text-align: left;
            padding: 14px 16px;
            font-size: 13px;
            font-weight: 500;
            color: #888;
            border-bottom: 1px solid #f0f0f0;
        }

        .custom-table td {
            padding: 16px;
            background: #f9f9fc;
            font-size: 14px;
            color: #333;
            border-bottom: 4px solid #fff;
        }

        .custom-table tr:first-child td {
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }

        .custom-table tr:last-child td {
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
        }

        .btn-view {
            display: inline-block;
            font-family: 'Inter', sans-serif;
            background: #FF5722;
            color: #fff;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: background 0.2s;
        }

        .btn-view:hover {
            background: #e64a19;
            color: #fff;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pending {
            background: #fff3e0;
            color: #ef6c00;
        }

        .status-submitted {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .empty-state {
            color: #888;
            font-size: 14px;
            padding: 24px;
            text-align: center;
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-title">My Review Assignments</div>
    </div>

    <div class="review-section">
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width: 5%">No.</th>
                    <th style="width: 40%">Paper Title</th>
                    <th style="width: 25%">Conference</th>
                    <th style="width: 15%">Status</th>
                    <th style="width: 15%">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $index => $r)
                    <tr>
                        <td style="border-top-left-radius: 8px; border-bottom-left-radius: 8px;">
                            {{ $index + 1 }}
                        </td>
                        <td style="font-weight: 500;">{{ $r->title }}</td>
                        <td>{{ $r->conference_name }}</td>
                        <td>
                            <span
                                class="status-badge {{ $r->review_status === 'Submitted' ? 'status-submitted' : 'status-pending' }}">
                                {{ $r->review_status }}
                            </span>
                        </td>
                        <td style="border-top-right-radius: 8px; border-bottom-right-radius: 8px;">
                            <a href="{{ route('reviewer.reviews.show', $r->review_id) }}" class="btn-view">
                                View Review
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-state">You have no assigned reviews.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection