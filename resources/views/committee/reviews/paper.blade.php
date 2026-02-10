@extends('layouts.committee')

@section('title', 'Paper Review Details')

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
            margin-bottom: 32px;
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

        /* Reviewer Section */
        .reviewer-section {
            background: #fff;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-bar {
            background: #f5f5f7;
            border-radius: 8px;
            padding: 8px 16px;
            width: 300px;
            font-size: 14px;
            /*  */
            display: flex;
            align-items: center;
            color: #888;
        }

        .search-input {
            border: none;
            background: transparent;
            outline: none;
            margin-left: 8px;
            width: 100%;
            font-size: 14px;
            color: #333;
        }

        .btn-assign {
            font-family: inherit;
            background: #e0e0e0;
            color: #333;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 400;
            /* Regular weight */
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s;
        }

        .btn-assign:hover {
            background: #d0d0d0;
        }

        /* Table */
        .custom-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            /* margin-top: 16px; */
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
            /* Light purple/grey background for rows */
            font-size: 14px;
            color: #333;
            border-bottom: 4px solid #fff;
            /* Spacing between rows */
        }

        .custom-table tr:first-child td {
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }

        .custom-table tr:last-child td {
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
        }

        /* Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            padding: 32px;
            border-radius: 12px;
            width: 600px;
            max-width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            position: relative;
        }

        .modal-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #1a1a2e;
        }

        .close-modal {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 24px;
            cursor: pointer;
            color: #888;
            background: none;
            border: none;
        }

        .reviewer-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        .reviewer-item:last-child {
            border-bottom: none;
        }

        .reviewer-info div {
            font-size: 14px;
            color: #333;
            font-weight: 500;
        }

        .reviewer-info span {
            font-size: 12px;
            color: #888;
        }

        .btn-add {
            font-family: inherit;
            background: #FF5722;
            /* Changed color */
            color: white;
            border: none;
            padding: 6px 16px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-add:hover {
            background: #e64a19;
        }

        .btn-view {
            display: inline-block;
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

        .btn-link {
            color: #1a1a2e;
            text-decoration: underline;
            font-size: 13px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-title">Details</div>
    </div>

    <!-- Details Card -->
    <div class="details-card">
        <div class="section-label">Paper Details</div>

        <div class="form-group">
            <label class="form-label">Paper ID</label>
            <div class="form-control-static">{{ $paper->paper_id }}</div>
        </div>

        <div class="form-group">
            <label class="form-label">Paper Title</label>
            <div class="form-control-static">{{ $paper->title }}</div>
        </div>

        @php
            $corresponding = $authors->firstWhere('is_corresponding_author', 1);
            $coAuthors = $authors->where('is_corresponding_author', 0);
        @endphp

        <div class="form-group">
            <label class="form-label">Author</label>
            <div class="form-control-static">
                @if($corresponding)
                    {{ $corresponding->author_name }} ({{ $corresponding->author_email }})
                @else
                    -
                @endif
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Co-Author</label>
            <div class="form-control-static">
                @if($coAuthors->isNotEmpty())
                    {{ $coAuthors->map(fn($a) => $a->author_name . '(' . $a->author_email . ')')->implode('; ') }}
                @else
                    -
                @endif
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Keywords</label>
            <div class="form-control-static">{{ $paper->keywords }}</div>
        </div>

        <div class="form-group">
            <label class="form-label">Track</label>
            <div class="form-control-static">{{ $paper->track_name }}</div>
        </div>

        <div class="form-group">
            <label class="form-label">Paper Score</label>
            <div class="form-control-static">
                @if($avgScore !== null)
                    {{ number_format($avgScore * 20, 2) }}%
                @else
                    -
                @endif
            </div>
        </div>
    </div>

    <!-- Reviewer Section -->
    <div class="reviewer-section">
        <div class="section-header">
            <div class="search-bar">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#888"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" id="pageSearchInput" class="search-input" placeholder="Search assigned reviewers..."
                    onkeyup="filterTable()">
            </div>

            @if(!empty($fullpaperId))
                <button class="btn-assign" onclick="openModal()">Assign Reviewer</button>
            @endif
        </div>

        <table class="custom-table" id="assignedReviewsTable">
            <thead>
                <tr>
                    <th>Reviewer</th>
                    <th>Reviewer's Area</th>
                    <th>Assigned Date</th>
                    <th>Status</th>
                    <th>Mark</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @if($assignments->isEmpty())
                    <tr>
                        <td colspan="6" style="text-align: center; color: #888; background: #fff;">No reviewers assigned yet.
                        </td>
                    </tr>
                @else
                    @foreach($assignments as $a)
                        <tr style="background-color: #f3f4fa; border-radius: 8px;">
                            <td style="border-top-left-radius: 8px; border-bottom-left-radius: 8px;">
                                {{ $a->reviewer_name }}
                            </td>
                            <td>{{ $a->reviewer_area ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($a->assigned_date)->format('d-M-Y') }}</td>
                            <td>{{ $a->review_status }}</td>
                            <td>
                                @if($a->review_status === 'Submitted' && $a->score !== null)
                                    {{ number_format($a->score * 20, 2) }}%
                                @else
                                    -
                                @endif
                            </td>
                            <td style="border-top-right-radius: 8px; border-bottom-right-radius: 8px;">
                                <a href="{{ route('committee.review.details', [$conference_id, $paper->paper_id, $a->review_id]) }}"
                                    class="btn-view">View</a>
                            </td>
                        </tr>
                        <!-- Spacer Row logic handled by border-bottom in CSS -->
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <!-- Assign Reviewer Modal -->
    <div id="assignModal" class="modal-overlay">
        <div class="modal-content">
            <button class="close-modal" onclick="closeModal()">×</button>
            <h3 class="modal-title">Assign Reviewer</h3>

            <div class="form-group">
                <input type="text" id="reviewerSearch" class="form-control-static" placeholder="Search reviewer..."
                    onkeyup="filterReviewers()" style="margin-bottom: 15px;">
            </div>

            <div id="reviewerList">
                @if($reviewers->isEmpty())
                    <p style="color: #888; text-align: center;">No accepted reviewers found.</p>
                @else
                    @foreach($reviewers as $r)
                        <div class="reviewer-item">
                            <div class="reviewer-info">
                                <div>{{ $r->name }}</div>
                                <span>{{ $r->area ?? 'No Area' }} | {{ $r->email }}</span>
                            </div>
                            <form method="POST"
                                action="{{ route('committee.review.assign', [$conference_id, $paper->paper_id, $r->reviewer_id]) }}">
                                @csrf
                                <button type="submit" class="btn-add"
                                    onclick="return confirm('Assign {{ $r->name }}?')">Assign</button>
                            </form>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('assignModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('assignModal').style.display = 'none';
        }

        // Close on outside click
        window.onclick = function (event) {
            if (event.target == document.getElementById('assignModal')) {
                closeModal();
            }
        }

        function filterReviewers() {
            var input, filter, list, items, info, i, txtValue;
            input = document.getElementById("reviewerSearch");
            filter = input.value.toUpperCase();
            list = document.getElementById("reviewerList");
            items = list.getElementsByClassName("reviewer-item");

            for (i = 0; i < items.length; i++) {
                info = items[i].getElementsByClassName("reviewer-info")[0];
                if (info) {
                    txtValue = info.textContent || info.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        items[i].style.display = "flex"; // Restore specific display
                    } else {
                        items[i].style.display = "none";
                    }
                }
            }
        }

        function filterTable() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("pageSearchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("assignedReviewsTable");
            tr = table.getElementsByTagName("tr");

            // Loop through all table rows, and hide those who don't match the search query
            for (i = 1; i < tr.length; i++) { // Start from 1 to skip header
                // Search by Reviewer Name (column index 0) and Status (column index 3)
                var tdName = tr[i].getElementsByTagName("td")[0];
                var tdStatus = tr[i].getElementsByTagName("td")[3];

                if (tdName || tdStatus) {
                    var txtName = tdName ? (tdName.textContent || tdName.innerText) : "";
                    var txtStatus = tdStatus ? (tdStatus.textContent || tdStatus.innerText) : "";

                    // trim
                    txtName = txtName.trim();
                    txtStatus = txtStatus.trim();

                    if (txtName.toUpperCase().indexOf(filter) > -1 || txtStatus.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    </script>
@endsection