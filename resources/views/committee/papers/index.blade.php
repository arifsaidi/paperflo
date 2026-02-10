@extends('layouts.committee')

@section('title', 'Paper Submission List')

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

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
            background: #fff;
            border-bottom: none;
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a2e;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .paper-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .paper-table th {
            text-align: left;
            padding: 16px;
            border-bottom: 2px solid #f5f5f7;
            color: #666;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .paper-table td {
            padding: 20px 16px;
            border-bottom: 1px solid #f5f5f7;
            vertical-align: middle;
            color: #333;
            font-size: 15px;
            background-color: transparent !important;
        }

        .paper-table tr {
            background-color: transparent !important;
        }

        .paper-title {
            font-weight: 700;
            color: #1a1a2e;
            font-size: 15px;
            line-height: 1.4;
        }

        .paper-id {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: #555;
            background: #f0f0f2;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 13px;
        }

        .author-name {
            font-size: 14px;
            color: #555;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            display: inline-block;
        }

        .badge-pending {
            background: #fff8e1;
            color: #f57f17;
        }

        .badge-submitted {
            background: #e3f2fd;
            color: #1976d2;
        }

        .badge-accepted {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .badge-rejected {
            background: #ffebee;
            color: #c62828;
        }

        .btn-view {
            background: #fff;
            color: #FF5722;
            border: 1.5px solid #FF5722;
            padding: 6px 14px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            min-width: 60px;
        }

        .btn-view:hover {
            background: #FF5722;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(255, 87, 34, 0.2);
            text-decoration: none;
        }

        .dashboard-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #f0f0f2;
            color: #333;
            text-decoration: none;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            transition: background 0.2s;
        }

        .dashboard-btn:hover {
            background: #e5e5e7;
            text-decoration: none;
            color: #111;
        }

        /* Search Bar Style */
        .search-container {
            position: relative;
            width: 300px;
        }

        .search-input {
            width: 100%;
            padding: 10px 16px 10px 40px;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            font-size: 14px;
            color: #333;
            background: #f9f9f9;
            transition: all 0.2s;
        }

        .search-input:focus {
            outline: none;
            background: #fff;
            border-color: #FF5722;
            box-shadow: 0 0 0 3px rgba(255, 87, 34, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            width: 18px;
            height: 18px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 0;
            color: #888;
        }

        .empty-state svg {
            width: 64px;
            height: 64px;
            margin-bottom: 16px;
            color: #ddd;
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>Paper Submission List</h1>
            <p>Manage submissions for <strong>{{ $conference->name ?? 'Conference' }}</strong></p>
        </div>
        <a href="{{ route('committee.dashboard', $conference_id) }}" class="dashboard-btn">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7" />
                <rect x="14" y="3" width="7" height="7" />
                <rect x="14" y="14" width="7" height="7" />
                <rect x="3" y="14" width="7" height="7" />
            </svg>
            Dashboard Summary
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                    <polyline points="10 9 9 9 8 9" />
                </svg>
                Submissions Directory ({{ count($papers) }})
            </div>

            <div class="search-container">
                <svg class="search-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" id="paperSearchInput" class="search-input"
                    placeholder="Search by title, author, or ID..." onkeyup="filterTable()">
            </div>
        </div>

        @if($papers->isEmpty())
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                    <polyline points="10 9 9 9 8 9" />
                </svg>
                <p>No papers submitted yet.</p>
            </div>
        @else
            <table class="paper-table" id="papersTable">
                <thead>
                    <tr>
                        <th width="50">No.</th>
                        <th width="100">ID</th>
                        <th>Paper Title</th>
                        <th width="20%">Author</th>
                        <th width="15%">Track</th>
                        <th width="15%">Status</th>
                        <th width="100" style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($papers as $index => $p)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <span class="paper-id">#{{ str_pad($p->paper_id, 3, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td>
                                <div class="paper-title">{{ $p->title }}</div>
                            </td>
                            <td>
                                <div class="author-name">{{ $p->main_author }}</div>
                            </td>
                            <td>
                                <span style="font-size: 13px; color: #666;">{{ $p->track_name }}</span>
                            </td>

                            {{-- Status Badge --}}
                            <td>
                                <span
                                    class="badge {{ str_contains(strtolower($p->final_status ?? ''), 'accepted') ? 'badge-accepted' : (str_contains(strtolower($p->final_status ?? ''), 'rejected') ? 'badge-rejected' : 'badge-pending') }}">
                                    {{ $p->final_status ?? 'In Progress' }}
                                </span>
                            </td>

                            <td style="text-align: center;">
                                <a href="{{ route('committee.papers.show', [$conference_id, $p->paper_id]) }}" class="btn-view">
                                    View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <script>
        function filterTable() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("paperSearchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("papersTable");
            tr = table.getElementsByTagName("tr");

            // Loop through all table rows, and hide those who don't match the search query
            // Start from 1 to skip header
            for (i = 1; i < tr.length; i++) {
                // Column 1: ID
                // Column 2: Title
                // Column 3: Author
                var tdId = tr[i].getElementsByTagName("td")[1];
                var tdTitle = tr[i].getElementsByTagName("td")[2];
                var tdAuthor = tr[i].getElementsByTagName("td")[3];

                if (tdId || tdTitle || tdAuthor) {
                    var txtId = tdId ? (tdId.textContent || tdId.innerText) : "";
                    var txtTitle = tdTitle ? (tdTitle.textContent || tdTitle.innerText) : "";
                    var txtAuthor = tdAuthor ? (tdAuthor.textContent || tdAuthor.innerText) : "";

                    if (txtId.toUpperCase().indexOf(filter) > -1
                        || txtTitle.toUpperCase().indexOf(filter) > -1
                        || txtAuthor.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    </script>
@endsection