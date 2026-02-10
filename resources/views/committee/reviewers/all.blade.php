@extends('layouts.committee')

@section('title', 'Reviewers')

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
            background: #fff; /* Override default bootstrap grey */
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

        .reviewer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .reviewer-table th {
            text-align: left;
            padding: 16px;
            border-bottom: 2px solid #f5f5f7;
            color: #666;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .reviewer-table td {
            padding: 20px 16px;
            border-bottom: 1px solid #f5f5f7;
            vertical-align: middle;
            color: #333;
            font-size: 15px;
            background-color: transparent !important; /* Ensure transparent/white background */
        }

        /* Override potential row styles */
        .reviewer-table tr {
            background-color: transparent !important;
        }

        .user-name {
            font-weight: 700;
            color: #1a1a2e;
        }

        .user-email {
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

        .badge-area {
            background: #f3e5f5;
            color: #7b1fa2;
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
        <h1>Reviewers</h1>
        <p>Manage list of reviewers for <strong>{{ $conference->name ?? 'Conference' }}</strong></p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="background: #e8f5e9; color: #2e7d32; border-radius: 12px; padding: 16px; margin-bottom: 24px; border: 1px solid #c8e6c9;">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="color: inherit;">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background: #ffebee; color: #c62828; border-radius: 12px; padding: 16px; margin-bottom: 24px; border: 1px solid #ffcdd2;">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="color: inherit;">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div class="card-title">
               <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="9" cy="7" r="4" />
                    <path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2" />
                    <circle cx="17" cy="11" r="3" />
                    <path d="M21 21v-1a3 3 0 0 0-3-3h-1" />
                </svg>
                Reviewer Directory ({{ $reviewers->count() }})
            </div>
            
            <div style="display: flex; gap: 12px; align-items: center;">
                <div class="search-container">
                    <svg class="search-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="reviewerSearchInput" class="search-input" placeholder="Search by name, email, or area..." onkeyup="filterTable()">
                </div>

                <a href="{{ route('committee.reviewers.create', $conference_id) }}" class="btn-view" style="padding: 8px 16px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 6px;">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                   Invite Reviewer
                </a>
            </div>
        </div>

        @if($reviewers->isEmpty())
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="9" cy="7" r="4" />
                    <path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2" />
                    <circle cx="17" cy="11" r="3" />
                    <path d="M21 21v-1a3 3 0 0 0-3-3h-1" />
                </svg>
                <p>No reviewers found for this conference.</p>
            </div>
        @else
            <table class="reviewer-table" id="reviewersTable">
                <thead>
                    <tr>
                        <th style="width: 25%">Name</th>
                        <th style="width: 30%">Email</th>
                        <th style="width: 25%">Area of Expertise</th>
                        <th style="width: 20%; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reviewers as $r)
                        <tr>
                            <td>
                                <div class="user-name">{{ $r->name }}</div>
                            </td>
                            <td>
                                <div class="user-email">{{ $r->email }}</div>
                            </td>
                            <td>
                                @if($r->area)
                                    <span class="badge badge-area">{{ $r->area }}</span>
                                @else
                                    <span style="color: #999;">-</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('committee.reviewers.show', [$conference_id, $r->reviewer_id]) }}" class="btn-view">
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
            input = document.getElementById("reviewerSearchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("reviewersTable");
            tr = table.getElementsByTagName("tr");

            // Loop through all table rows, and hide those who don't match the search query
            // Start from 1 to skip header
            for (i = 1; i < tr.length; i++) {
                // Column 0: Name
                // Column 1: Email
                // Column 2: Area
                var tdName = tr[i].getElementsByTagName("td")[0];
                var tdEmail = tr[i].getElementsByTagName("td")[1];
                var tdArea = tr[i].getElementsByTagName("td")[2];

                if (tdName || tdEmail || tdArea) {
                    var txtName = tdName ? (tdName.textContent || tdName.innerText) : "";
                    var txtEmail = tdEmail ? (tdEmail.textContent || tdEmail.innerText) : "";
                    var txtArea = tdArea ? (tdArea.textContent || tdArea.innerText) : "";

                    if (txtName.toUpperCase().indexOf(filter) > -1 
                        || txtEmail.toUpperCase().indexOf(filter) > -1
                        || txtArea.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    </script>
@endsection