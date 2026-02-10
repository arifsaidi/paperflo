<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Detail - PaperFlo.</title>
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
            max-width: 900px;
            margin: 0 auto;
            padding: 40px;
        }

        .btn-back {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #666;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: color 0.2s;
            margin-bottom: 16px;
        }

        .btn-back:hover {
            color: #FF5722;
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

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 32px;
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

        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            margin-bottom: 24px;
        }

        .info-item {}

        .info-label {
            font-size: 11px;
            font-weight: 800;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 8px;
        }

        .info-value {
            font-size: 16px;
            color: #1a1a2e;
            font-weight: 600;
            line-height: 1.5;
        }

        .badge {
            padding: 8px 16px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
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

        .status-card {
            background: linear-gradient(135deg, #1a1a2e 0%, #2a2a4e 100%);
            color: #fff;
        }

        .status-card .card-title {
            color: #fff;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .status-card .info-label {
            color: rgba(255, 255, 255, 0.5);
        }

        .status-card .info-value {
            color: #fff;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 24px;
        }

        .btn {
            padding: 14px 24px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
            width: 100%;
        }

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
            border: 2px solid #c62828;
        }

        .btn-reject:hover {
            background: #c62828;
            color: #fff;
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
        <a href="{{ route('conference.request.index') }}" class="btn-back">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M19 12H5m7-7l-7 7 7 7" />
            </svg>
            Back to All Requests
        </a>

        <div class="page-header">
            <h1>{{ $request->conference_title }}</h1>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="detail-grid">
            <div class="main-column">
                <div class="card">
                    <div class="card-title">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Organization Details
                    </div>
                    <div class="info-section">
                        <div class="info-item">
                            <div class="info-label">Organization Name</div>
                            <div class="info-value">{{ $request->org_name }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Organization Type</div>
                            <div class="info-value">{{ $request->org_type }}</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Address</div>
                        <div class="info-value">{{ $request->org_address }}</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-title">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Applicant Information
                    </div>
                    <div class="info-section">
                        <div class="info-item">
                            <div class="info-label">Full Name</div>
                            <div class="info-value">{{ $request->applicant_name }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Email Address</div>
                            <div class="info-value">{{ $request->applicant_email }}</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Phone Number</div>
                        <div class="info-value">{{ $request->applicant_phone }}</div>
                    </div>
                </div>
            </div>

            <div class="side-column">
                <div class="card status-card">
                    <div class="card-title">Request Status</div>

                    <div style="margin-bottom: 24px;">
                        <div class="info-label">Request ID</div>
                        <div class="info-value" style="font-family: monospace; font-size: 20px;">
                            #{{ $request->request_id }}</div>
                    </div>

                    <div style="margin-bottom: 24px;">
                        <div class="info-label">Current Status</div>
                        <div style="margin-top: 8px;">
                            <span class="badge badge-{{ strtolower($request->status) }}">{{ $request->status }}</span>
                        </div>
                    </div>

                    <div>
                        <div class="info-label">Submitted On</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($request->submitted_date)->format('d M Y') }}
                        </div>
                    </div>

                    @if($request->status === 'Pending')
                        <div class="action-buttons">
                            <form method="POST" action="{{ route('conference.request.approve', $request->request_id) }}">
                                @csrf
                                <button type="submit" class="btn btn-approve">Approve Request</button>
                            </form>
                            <form method="POST" action="{{ route('conference.request.reject', $request->request_id) }}">
                                @csrf
                                <button type="submit" class="btn btn-reject">Reject Request</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>

</html>