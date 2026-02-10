<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $conference->name }} - PaperFlo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700;800&display=swap"
        rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: #1a1a2e;
            line-height: 1.6;
        }

        /* Navigation */
        .top-bar {
            background: #fff;
            padding: 16px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 800;
            color: #1a1a2e;
            text-decoration: none;
            letter-spacing: -0.5px;
        }

        .logo span {
            color: #FF5722;
        }

        .nav-links {
            display: flex;
            gap: 24px;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: #666;
            font-weight: 500;
            font-size: 14px;
            transition: color 0.2s;
        }

        .nav-links a:hover {
            color: #FF5722;
        }

        .btn-nav {
            background: #fff;
            color: #FF5722 !important;
            border: 2px solid #FF5722;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 700;
            transition: background 0.2s, transform 0.2s;
        }

        .btn-nav:hover {
            background: #FF5722 !important;
            color: #fff !important;
            transform: translateY(-1px);
        }

        .btn-nav svg {
            width: 16px;
            height: 16px;
        }

        /* Layout */
        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: #666;
            font-weight: 500;
            margin-bottom: 24px;
            font-size: 0.9rem;
        }

        .back-link:hover {
            color: #1a1a2e;
        }

        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 40px;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1544531586-fde5298cdd40?auto=format&fit=crop&q=80&w=1200');
            background-size: cover;
            background-position: center;
            color: #fff;
            padding: 80px 10%;
            text-align: center;
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
            letter-spacing: -1px;
        }

        .hero .status-badge {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(4px);
        }

        /* Conference Sub-Navigation */
        .conference-nav {
            background: #fff;
            border-bottom: 1px solid #eee;
            position: sticky;
            top: 72px;
            /* Sticks below main nav which is about 72px height */
            z-index: 900;
        }

        .conference-nav .nav-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            gap: 2rem;
        }

        .conf-nav-item {
            padding: 1.2rem 0;
            text-decoration: none;
            color: #666;
            font-weight: 600;
            font-size: 0.95rem;
            border-bottom: 3px solid transparent;
            transition: all 0.2s;
        }

        .conf-nav-item:hover {
            color: #FF5722;
        }

        .conf-nav-item.active {
            color: #FF5722;
            border-bottom-color: #FF5722;
        }

        .card-body {
            padding: 40px;
        }

        .conference-meta {
            margin-bottom: 30px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            background: #e8f5e9;
            color: #2e7d32;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 24px;
            padding: 24px;
            background: #f8f9fa;
            border-radius: 12px;
            margin-bottom: 30px;
        }

        .info-item label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .info-item span {
            font-weight: 600;
            color: #1a1a2e;
        }

        .description-section {
            padding-top: 20px;
        }

        .description-section h2 {
            font-size: 1.25rem;
            margin-bottom: 16px;
        }

        .description-content {
            color: #555;
            white-space: pre-wrap;
        }

        /* Registration Section */
        .registration-card {
            background: #fff;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 60px;
        }

        .registration-card h2 {
            font-size: 1.5rem;
            margin-bottom: 24px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.95rem;
        }

        .btn-submit {
            background: #4E342E;
            color: #fff;
            border: none;
            padding: 14px 24px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: #3e2723;
        }

        .login-cta {
            text-align: center;
            padding: 24px;
            background: #fff9c4;
            border-radius: 12px;
            color: #827717;
            font-weight: 600;
        }

        .login-cta a {
            color: #4E342E;
            text-decoration: underline;
        }

        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-error {
            background: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <div class="top-bar">
        <a href="{{ route('home') }}" class="logo">PaperFlo<span>.</span></a>
        <div class="nav-links">
            <a href="{{ route('home') }}">Conferences</a>
            <a href="{{ route('conference.request.create') }}">Host a Conference</a>
            @if(session()->has('user_id'))
                <a href="{{ route('participant.profile.show') }}" class="btn-nav">
                    <svg fill="currentColor" viewBox="0 0 16 16" style="width: 16px; height: 16px;">
                        <path
                            d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z" />
                    </svg>
                    My Account
                </a>
            @else
                <a href="{{ route('login.form') }}">Login</a>
                <a href="{{ route('participant.register.form') }}" class="btn-nav">Sign Up</a>
            @endif
        </div>
    </div>

    <header class="hero">
        @if($conference->logo)
            <img src="{{ asset('storage/' . $conference->logo) }}" alt="Conference Logo"
                style="max-height: 100px; margin-bottom: 24px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            <br>
        @endif
        <span class="status-badge">{{ $conference->conference_status }}</span>
        <h1>{{ $conference->name }}</h1>
    </header>

    <div class="conference-nav">
        <div class="nav-container">
            <a href="{{ route('participant.conference.show', $conference->conference_id) }}"
                class="conf-nav-item {{ request()->routeIs('participant.conference.show') ? 'active' : '' }}">
                Conference Details
            </a>
            @if(session()->has('user_id'))
                @if(isset($registration))
                    <a href="{{ route('participant.registration.show', $conference->conference_id) }}"
                        class="conf-nav-item {{ request()->routeIs('participant.registration.show') ? 'active' : '' }}">
                        My Registration
                    </a>
                @endif
                @if(isset($registration) && in_array($registration->registration_type, ['author', 'presenter']))
                    <a href="{{ route('conference.submissions.index', $conference->conference_id) }}"
                        class="conf-nav-item {{ request()->routeIs('conference.submissions.*') ? 'active' : '' }}">
                        My Submissions
                    </a>
                @endif
            @endif
        </div>
    </div>

    <div class="container">
        <a href="{{ route('home') }}" class="back-link">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd"
                    d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z" />
            </svg>
            Back to conferences
        </a>

        <article class="card">
            <div class="card-body">

                <div class="info-grid">
                    <div class="info-item">
                        <label>Date</label>
                        <span>{{ \Carbon\Carbon::parse($conference->conference_date)->format('M d, Y') }}</span>
                    </div>
                    <div class="info-item">
                        <label>Venue</label>
                        <span>{{ $conference->conference_venue }}</span>
                    </div>
                    <div class="info-item">
                        <label>Contact Email</label>
                        <span>{{ $conference->conference_email ?? '-' }}</span>
                    </div>
                    <div class="info-item">
                        <label>Contact Phone</label>
                        <span>{{ $conference->conference_phone ?? '-' }}</span>
                    </div>
                    <div class="info-item">
                        <label>Website</label>
                        @if($conference->conference_website)
                            <a href="{{ $conference->conference_website }}" target="_blank"
                                style="color: #2962ff; font-weight:600; font-size: 0.9rem">Visit Website</a>
                        @else
                            <span>-</span>
                        @endif
                    </div>
                    <div class="info-item">
                        <label>Chairman</label>
                        <span>{{ $conference->chairman_name }}</span>
                        <div style="font-size: 0.85rem; color: #666;">{{ $conference->chairman_email }}</div>
                    </div>
                    <div class="info-item">
                        <label>Publication</label>
                        <span>{{ $conference->publication ?? 'Not specified' }}</span>
                    </div>
                    <div class="info-item" style="grid-column: 1 / -1;">
                        <label>Secretariat Address</label>
                        <span>{{ $conference->secretariat_address }}</span>
                    </div>
                </div>

                <div class="description-section">
                    <h2>About the Conference</h2>
                    <div class="description-content">{{ $conference->contents ?? 'No description provided.' }}</div>
                </div>

                <hr style="margin: 40px 0; border: 0; border-top: 1px solid #eee;">

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px;">
                    <!-- Important Dates -->
                    <div>
                        <h3 style="font-size: 1.2rem; margin-bottom: 16px; color: #1a1a2e;">Important Dates</h3>
                        @if($deadlines)
                            <ul style="list-style: none; padding: 0;">
                                <li
                                    style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f1f1;">
                                    <span style="color: #555;">Abstract Submission</span>
                                    <span
                                        style="font-weight: 600;">{{ \Carbon\Carbon::parse($deadlines->abstract_deadline)->format('M d, Y') }}</span>
                                </li>
                                <li
                                    style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f1f1;">
                                    <span style="color: #555;">Full Paper Submission</span>
                                    <span
                                        style="font-weight: 600;">{{ \Carbon\Carbon::parse($deadlines->fullpaper_deadline)->format('M d, Y') }}</span>
                                </li>
                                <li
                                    style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f1f1;">
                                    <span style="color: #555;">Camera-ready Submission</span>
                                    <span
                                        style="font-weight: 600;">{{ \Carbon\Carbon::parse($deadlines->cameraready_paper_deadline)->format('M d, Y') }}</span>
                                </li>
                                <li
                                    style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f1f1;">
                                    <span style="color: #555;">Registration Deadline</span>
                                    <span
                                        style="font-weight: 600;">{{ \Carbon\Carbon::parse($deadlines->participant_registration_deadline)->format('M d, Y') }}</span>
                                </li>
                            </ul>
                        @else
                            <p style="color: #888; font-style: italic;">No dates available.</p>
                        @endif
                    </div>

                    <!-- Conference Tracks -->
                    <div>
                        <h3 style="font-size: 1.2rem; margin-bottom: 16px; color: #1a1a2e;">Conference Tracks</h3>
                        @if($tracks->isNotEmpty())
                            <ul style="list-style: none; padding: 0;">
                                @foreach($tracks as $track)
                                    <li
                                        style="padding: 8px 12px; background: #f8f9fa; border-radius: 8px; margin-bottom: 8px; font-weight: 500; color: #333;">
                                        {{ $track->name }}
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p style="color: #888; font-style: italic;">No tracks defined yet.</p>
                        @endif
                    </div>
                </div>

                <div style="margin-top: 40px;">
                    <h3 style="font-size: 1.2rem; margin-bottom: 16px; color: #1a1a2e;">Registration Fees</h3>
                    @if($fees->isNotEmpty())
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse; min-width: 500px;">
                                <thead>
                                    <tr style="background: #f8f9fa; text-align: left;">
                                        <th style="padding: 12px 16px; border-radius: 8px 0 0 8px;">Category</th>
                                        <th style="padding: 12px 16px; border-radius: 0 8px 8px 0;">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($fees as $fee)
                                        <tr style="border-bottom: 1px solid #f1f1f1;">
                                            <td style="padding: 12px 16px; color: #333;">{{ $fee->name }}</td>
                                            <td style="padding: 12px 16px; font-weight: 600; color: #2e7d32;">
                                                {{ $fee->currency }} {{ number_format($fee->amount, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p style="color: #888; font-style: italic;">No fee information available.</p>
                    @endif
                </div>
            </div>
        </article>

        <section class="registration-card">
            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert-error">{{ session('error') }}</div>
            @endif

            @if(!session()->has('user_id'))
                <div class="login-cta">
                    Interested in joining? Please <a href="{{ route('login.form') }}">Login</a> or <a
                        href="{{ route('participant.register.form') }}">Register</a> to register for this conference.
                </div>
            @elseif(!$registration)
                <h2>Conference Registration</h2>
                <form method="POST" action="{{ route('conference.register', $conference->conference_id) }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Register as</label>
                        <select name="registration_type" class="form-select" required>
                            <option value="">Select your role</option>
                            <option value="listener">Listener</option>
                            <option value="author">Author</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Registration Category</label>
                        <select name="registration_category" class="form-select" required>
                            <option value="">Select category</option>
                            @forelse($fees as $fee)
                                <option value="{{ $fee->name }}">{{ $fee->name }} - {{ $fee->currency }}
                                    {{ number_format($fee->amount, 2) }}
                                </option>
                            @empty
                                <option value="general">General Registration</option>
                            @endforelse
                        </select>
                    </div>

                    <button type="submit" class="btn-submit">Complete Registration</button>
                </form>
            @else
                <h2>Your Registration</h2>
                <div style="background: #f1f3f5; padding: 24px; border-radius: 12px; margin-bottom: 24px;">
                    <p style="margin-bottom: 8px;"><strong>Status:</strong> <span
                            style="color: #2e7d32;">{{ ucfirst($registration->registration_status) }}</span></p>
                    <p style="margin-bottom: 8px;"><strong>Role:</strong> {{ ucfirst($registration->registration_type) }}
                    </p>
                    <p><strong>Category:</strong> {{ str_replace('_', ' ', ucfirst($registration->registration_category)) }}
                    </p>
                </div>

                <div style="display: flex; gap: 16px;">
                    <a href="{{ route('participant.registration.show', $conference->conference_id) }}"
                        style="flex:1; text-align:center; background:#4E342E; color:#fff; text-decoration:none; padding:12px; border-radius:8px; font-weight:600;">
                        View My Details
                    </a>
                    @if(in_array($registration->registration_type, ['author', 'presenter']))
                        <a href="{{ route('conference.submissions.index', $conference->conference_id) }}"
                            style="flex:1; text-align:center; background:#1a1a2e; color:#fff; text-decoration:none; padding:12px; border-radius:8px; font-weight:600;">
                            My Submissions
                        </a>
                    @endif
                </div>
            @endif
        </section>
    </div>

</body>

</html>