<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Conference Request - PaperFlo.</title>
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
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }

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

        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1540575861501-7cf05a4b125a?auto=format&fit=crop&q=80&w=2000');
            background-size: cover;
            background-position: center;
            color: #fff;
            padding: 100px 10%;
            text-align: center;
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            letter-spacing: -1px;
        }

        .hero p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto;
            opacity: 0.9;
        }

        .container {
            max-width: 800px;
            margin: -40px auto 60px;
            padding: 0 20px;
            position: relative;
            z-index: 10;
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

        .alert-error {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }

        .card {
            background: #fff;
            border-radius: 24px;
            padding: 48px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
        }

        .card-title {
            font-size: 20px;
            font-weight: 800;
            color: #1a1a2e;
            margin-bottom: 32px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card-title svg {
            color: #FF5722;
        }

        .form-section {
            margin-bottom: 40px;
        }

        .form-section:last-child {
            margin-bottom: 0;
        }

        .section-title {
            font-size: 14px;
            font-weight: 800;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f5f5f7;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-row.full {
            grid-template-columns: 1fr;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            font-size: 13px;
            font-weight: 700;
            color: #333;
            margin-bottom: 8px;
        }

        .form-input {
            padding: 14px 18px;
            border: 2px solid #eee;
            border-radius: 12px;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: #FF5722;
            box-shadow: 0 0 0 4px rgba(255, 87, 34, 0.1);
        }

        .form-input::placeholder {
            color: #bbb;
        }

        textarea.form-input {
            resize: vertical;
            min-height: 100px;
        }

        .btn-submit {
            background: linear-gradient(135deg, #FF5722 0%, #ff7043 100%);
            color: #fff;
            border: none;
            padding: 18px 40px;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            width: 100%;
            margin-top: 16px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(255, 87, 34, 0.3);
        }

        .footer-note {
            text-align: center;
            margin-top: 24px;
            font-size: 13px;
            color: #888;
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

    <div class="hero">
        <h1>Host Your Conference</h1>
        <p>Submit a request to host your academic conference on PaperFlo. Our team will review your application and get
            back to you.</p>
    </div>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-title">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Conference Request Form
            </div>

            <form method="POST" action="{{ route('conference.request.store') }}">
                @csrf

                <div class="form-section">
                    <div class="section-title">Conference Details</div>
                    <div class="form-row full">
                        <div class="form-group">
                            <label class="form-label">Conference Title *</label>
                            <input type="text" name="conference_title" class="form-input"
                                placeholder="e.g., International Conference on AI & Machine Learning 2026"
                                value="{{ old('conference_title') }}" required>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="section-title">Organization Information</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Organization Name *</label>
                            <input type="text" name="org_name" class="form-input" placeholder="e.g., Universiti Malaya"
                                value="{{ old('org_name') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Organization Type *</label>
                            <input type="text" name="org_type" class="form-input"
                                placeholder="e.g., University, Research Institute" value="{{ old('org_type') }}"
                                required>
                        </div>
                    </div>
                    <div class="form-row full">
                        <div class="form-group">
                            <label class="form-label">Organization Address *</label>
                            <textarea name="org_address" class="form-input"
                                placeholder="Full address including city, state, and country"
                                required>{{ old('org_address') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="section-title">Applicant Contact</div>
                    <div class="form-row full">
                        <div class="form-group">
                            <label class="form-label">Your Full Name *</label>
                            <input type="text" name="applicant_name" class="form-input"
                                placeholder="e.g., Dr. Ahmad bin Abdullah" value="{{ old('applicant_name') }}" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Email Address *</label>
                            <input type="email" name="applicant_email" class="form-input"
                                placeholder="e.g., ahmad@um.edu.my" value="{{ old('applicant_email') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone Number *</label>
                            <input type="tel" name="applicant_phone" class="form-input"
                                placeholder="e.g., +60 12-345 6789" value="{{ old('applicant_phone') }}" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Submit Conference Request</button>
            </form>

            <p class="footer-note">By submitting this form, you agree to PaperFlo's terms and conditions.</p>
        </div>
    </div>
</body>

</html>