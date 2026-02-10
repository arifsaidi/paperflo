<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PaperFlo - Academic Conferences</title>
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
            width: 18px;
            height: 18px;
        }

        /* Hero Section */
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
            margin: 0 auto 2rem;
            opacity: 0.9;
        }

        /* Content Section */
        .container {
            max-width: 1200px;
            margin: 60px auto;
            padding: 0 20px;
        }

        .section-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-header h2 {
            font-size: 2.2rem;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 1rem;
        }

        .section-header p {
            color: #666;
        }

        /* Conference Grid */
        .conference-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
        }

        .conference-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
        }

        .conference-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .card-image {
            height: 200px;
            background-color: #eee;
            background-image: url('https://images.unsplash.com/photo-1505373633519-21df37946955?auto=format&fit=crop&q=80&w=800');
            background-size: cover;
            background-position: center;
        }

        .card-content {
            padding: 24px;
            flex-grow: 1;
        }

        .card-date {
            font-size: 0.85rem;
            font-weight: 600;
            color: #4E342E;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            display: block;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 12px;
            line-height: 1.4;
        }

        .card-venue {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .btn-view {
            display: inline-block;
            width: 100%;
            padding: 12px;
            background: #f1f3f5;
            color: #1a1a2e;
            text-align: center;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: background 0.2s;
        }

        .btn-view:hover {
            background: #e9ecef;
        }

        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 1rem 10%;
            text-align: center;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }

            .nav-links {
                display: none;
            }

            .conference-grid {
                grid-template-columns: 1fr;
            }
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

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <header class="hero">
        <h1>Discover Top Academic Conferences</h1>
        <p>Connect with researchers worldwide and submit your work to prestigious venues across multiple disciplines.
        </p>
    </header>

    <main class="container">
        <div class="section-header">
            <h2>Active Conferences</h2>
            <p>Browse through upcoming events and find your next opportunity.</p>
        </div>

        @if($conferences->isEmpty())
            <div style="text-align:center; padding: 40px; color:#666">
                <p>No active conferences available at the moment.</p>
            </div>
        @else
            <div class="conference-grid">
                @foreach($conferences as $conference)
                    <div class="conference-card">
                        <div class="card-image"></div>
                        <div class="card-content">
                            <span
                                class="card-date">{{ \Carbon\Carbon::parse($conference->conference_date)->format('d D, Y') }}</span>
                            <h3 class="card-title">{{ $conference->name }}</h3>
                            <div class="card-venue">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path
                                        d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z" />
                                </svg>
                                {{ $conference->conference_venue }}
                            </div>
                            <a href="{{ route('participant.conference.show', $conference->conference_id) }}" class="btn-view">
                                View Details
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </main>

    <footer style="background: #1a1a2e; color: #fff; padding: 60px 10%; text-align: center; margin-top: 100px;">
        <p style="font-weight: 700; font-size: 1.5rem; margin-bottom: 1rem;">PaperFlo</p>
        <p style="opacity: 0.6; font-size: 0.9rem;">© {{ date('Y') }} Academic Conference Management System. All rights
            reserved.</p>
    </footer>

</body>

</html>