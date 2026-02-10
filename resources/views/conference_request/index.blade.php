<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conference Requests - PaperFlo.</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
        }

        .top-bar {
            background: #1a1a2e;
            padding: 16px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            color: #fff;
        }

        .logo span {
            color: #FF5722;
        }

        .user-info {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .btn-logout {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #fff;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.2s;
        }

        .btn-logout:hover {
            background: #FF5722;
            border-color: #FF5722;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px;
        }

        .page-header {
            margin-bottom: 32px;
        }

        .page-header h1 {
            font-size: 32px;
            font-weight: 800;
            color: #1a1a2e;
            margin-bottom: 8px;
        }

        .page-header p {
            color: #666;
            font-size: 15px;
        }

        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-weight: 600;
        }

        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }

        .card {
            background: #fff;
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 24px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid #f5f5f7;
            padding-bottom: 16px;
        }

        .card-title svg {
            color: #FF5722;
        }

        .request-table {
            width: 100%;
            border-collapse: collapse;
        }

        .request-table th {
            text-align: left;
            padding: 16px;
            border-bottom: 2px solid #f5f5f7;
            color: #666;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .request-table td {
            padding: 20px 16px;
            border-bottom: 1px solid #f5f5f7;
            vertical-align: middle;
        }

        .request-table tr:last-child td {
            border-bottom: none;
        }

        .conf-title {
            font-weight: 700;
            color: #1a1a2e;
            font-size: 16px;
            margin-bottom: 4px;
        }

        .conf-org {
            font-size: 13px;
            color: #888;
        }

        .applicant-name {
            font-weight: 600;
            color: #333;
        }

        .applicant-email {
            font-size: 13px;
            color: #888;
        }

        .badge {
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-pending {
            background: #fff8e1;
            color: #f57f17;
        }

        .badge-approved {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .badge-rejected {
            background: #ffebee;
            color: #c62828;
        }

        .action-btns {
            display: flex;
            gap: 8px;
        }

        .btn { padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 700; border: none; cursor: pointer; transition: all 0.2s; text-decoration: none; display: inline-block; }
        .btn-view { background: #fff; color: #FF5722; border: 1.5px solid #FF5722; }
        .btn-view:hover { background: #FF5722; color: #fff; transform: translateY(-1px); }
        .btn-approve {
            background: #2e7d32;
            color: #fff;
        }

        .btn-approve:hover {
            background: #1b5e20;
            transform: translateY(-1px);
        }

        .btn-reject {
            background: #fff;
            color: #c62828;
            border: 1.5px solid #c62828;
        }

        .btn-reject:hover {
            background: #c62828;
            color: #fff;
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
</head>

<body>
    <div class="top-bar">
        <div class="logo">PaperFlo<span>.</span></div>
        <div class="user-info">
            <span>Super Admin</span>
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        </div>
    </div>

    <div class="container">
        <div class="page-header">
            <h1>Conference Requests</h1>
            <p>Review and manage incoming conference hosting requests</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-title">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                All Requests ({{ $requests->count() }})
            </div>

            @if($requests->isEmpty())
                <div class="empty-state">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p>No conference requests have been submitted yet.</p>
                </div>
            @else
                <table class="request-table">
                    <thead>
                        <tr>
                            <th style="width: 30%;">Conference</th>
                            <th style="width: 25%;">Applicant</th>
                            <th style="width: 15%;">Submitted</th>
                            <th style="width: 12%;">Status</th>
                            <th style="width: 18%; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $r)
                            <tr>
                                <td>
                                    <div class="conf-title">{{ $r->conference_title }}</div>
                                    <div class="conf-org">{{ $r->org_name }}</div>
                                </td>
                                <td>
                                    <div class="applicant-name">{{ $r->applicant_name }}</div>
                                    <div class="applicant-email">{{ $r->applicant_email }}</div>
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($r->submitted_date)->format('d M Y') }}
                                </td>
                                <td>
                                    <span class="badge badge-{{ strtolower($r->status) }}">{{ $r->status }}</span>
                                </td>
                                <td style="text-align: right;">
                                    <a href="{{ route('conference.request.show', $r->request_id) }}"
                                        class="btn btn-view">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</body>

</html>