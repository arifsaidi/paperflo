<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Committee Panel') - PaperFlo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: #f5f5f7;
            min-height: 100vh;
            /* display: flex; - Removed to fix layout issues with fixed sidebar */
        }

        /* Sidebar */
        .sidebar {
            width: 220px;
            background: #fff;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding: 24px 16px;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #eee;
        }

        .sidebar-logo {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 800;
            color: #FF5722;
            margin-bottom: 32px;
            padding-left: 8px;
            letter-spacing: -0.5px;
        }

        .sidebar-nav {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            text-decoration: none;
            color: #333;
            font-size: 14px;
            font-weight: 500;
            border-radius: 10px;
            transition: all 0.2s;
        }

        .sidebar-link:hover {
            background: #f5f5f7;
        }

        .sidebar-link.active {
            background: #f0f0f2;
            color: #1a1a2e;
        }

        .sidebar-link svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        .sidebar-footer {
            margin-top: auto;
            padding-top: 16px;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            background: #fff0f0;
            color: #e53935;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            width: 100%;
            transition: all 0.2s;
        }

        .logout-btn:hover {
            background: #ffe5e5;
        }

        .logout-btn svg {
            width: 20px;
            height: 20px;
        }

        /* Main Content */
        .main-content {
            margin-left: 220px;
            flex: 1;
            padding: 32px;
            min-height: 100vh;
        }

        .top-bar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
        }

        .top-bar-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            border-radius: 10px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .top-bar-icon:hover {
            background: #f0f0f2;
        }

        .top-bar-icon svg {
            width: 20px;
            height: 20px;
            color: #666;
        }
    </style>
    @yield('styles')
</head>

<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-logo">PaperFlo.</div>

        <nav class="sidebar-nav">


            <a href="{{ route('committee.dashboard', $conference_id ?? request()->route('conference_id')) }}"
                class="sidebar-link {{ request()->routeIs('committee.dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <path d="M12 6v6l4 2" />
                </svg>
                Overview
            </a>

            {{-- Conference Settings Submenu --}}
            @php
                $isConferenceSettingsActive = request()->routeIs('conference.setup.*') ||
                    request()->routeIs('conference.contents.*') ||
                    request()->routeIs('conference.deadlines.*') ||
                    request()->routeIs('conference.fees.*') ||
                    request()->routeIs('conference.tracks.*') ||
                    request()->routeIs('conference.status.*') ||
                    request()->routeIs('conference.activate');
            @endphp
            <a href="#conferenceSettingsSubmenu" data-toggle="collapse"
                aria-expanded="{{ $isConferenceSettingsActive ? 'true' : 'false' }}"
                class="sidebar-link {{ $isConferenceSettingsActive ? 'active' : '' }}"
                style="justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path
                            d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z">
                        </path>
                    </svg>
                    <span>Conference Settings</span>
                </div>
                <i class="fas fa-chevron-right"
                    style="font-size: 12px; transition: transform 0.2s; {{ $isConferenceSettingsActive ? 'transform: rotate(90deg);' : '' }}"></i>
            </a>

            <div class="collapse {{ $isConferenceSettingsActive ? 'show' : '' }}" id="conferenceSettingsSubmenu">
                <div style="padding-left: 12px; background: #fafafa; border-radius: 0 0 10px 10px; margin-bottom: 4px;">
                    <a href="{{ route('conference.setup.edit', $conference_id ?? request()->route('conference_id')) }}"
                        class="sidebar-link {{ request()->routeIs('conference.setup.*') ? 'active' : '' }}"
                        style="font-size: 13px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            style="width: 16px; height: 16px;">
                            <circle cx="12" cy="12" r="3" />
                            <circle cx="6" cy="6" r="2" />
                            <circle cx="18" cy="6" r="2" />
                            <circle cx="6" cy="18" r="2" />
                            <circle cx="18" cy="18" r="2" />
                        </svg>
                        Details
                    </a>

                    <a href="{{ route('conference.contents.edit', $conference_id ?? request()->route('conference_id')) }}"
                        class="sidebar-link {{ request()->routeIs('conference.contents.*') ? 'active' : '' }}"
                        style="font-size: 13px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            style="width: 16px; height: 16px;">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                            <line x1="16" y1="13" x2="8" y2="13" />
                            <line x1="16" y1="17" x2="8" y2="17" />
                        </svg>
                        Contents
                    </a>

                    <a href="{{ route('conference.deadlines.index', $conference_id ?? request()->route('conference_id')) }}"
                        class="sidebar-link {{ request()->routeIs('conference.deadlines.*') ? 'active' : '' }}"
                        style="font-size: 13px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            style="width: 16px; height: 16px;">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                        Deadlines
                    </a>

                    <a href="{{ route('conference.fees.index', $conference_id ?? request()->route('conference_id')) }}"
                        class="sidebar-link {{ request()->routeIs('conference.fees.*') ? 'active' : '' }}"
                        style="font-size: 13px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            style="width: 16px; height: 16px;">
                            <line x1="12" y1="1" x2="12" y2="23" />
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                        </svg>
                        Fees
                    </a>

                    <a href="{{ route('conference.tracks.index', $conference_id ?? request()->route('conference_id')) }}"
                        class="sidebar-link {{ request()->routeIs('conference.tracks.*') ? 'active' : '' }}"
                        style="font-size: 13px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            style="width: 16px; height: 16px;">
                            <circle cx="12" cy="12" r="5" />
                            <line x1="12" y1="1" x2="12" y2="3" />
                            <line x1="12" y1="21" x2="12" y2="23" />
                            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64" />
                            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78" />
                        </svg>
                        Tracks
                    </a>

                    @inject('rbac', 'App\Services\RbacService')

                    @if($rbac->getRoleId('conference_admin', session('user_id')) == 2)
                        <a href="{{ route('conference.status.edit', $conference_id ?? request()->route('conference_id')) }}"
                            class="sidebar-link {{ request()->routeIs('conference.status.*') || request()->routeIs('conference.activate') ? 'active' : '' }}"
                            style="font-size: 13px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                style="width: 16px; height: 16px;">
                                <path
                                    d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83" />
                            </svg>
                            Status
                        </a>
                    @endif
                </div>
            </div>

            <a href="{{ route('committee.participants.index', $conference_id ?? request()->route('conference_id')) }}"
                class="sidebar-link {{ request()->routeIs('committee.participants.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="16" rx="2" />
                    <circle cx="9" cy="10" r="2" />
                    <path d="M15 8h2M15 12h2" />
                    <path d="M7 16h10" />
                </svg>
                Participants
            </a>



            <a href="{{ route('committee.reviewers.all', $conference_id ?? request()->route('conference_id')) }}"
                class="sidebar-link {{ request()->routeIs('committee.reviewers.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="9" cy="7" r="4" />
                    <path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2" />
                    <circle cx="17" cy="11" r="3" />
                    <path d="M21 21v-1a3 3 0 0 0-3-3h-1" />
                </svg>
                Reviewers
            </a>

            <a href="{{ route('committee.admins.index', $conference_id ?? request()->route('conference_id')) }}"
                class="sidebar-link {{ request()->routeIs('committee.admins.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
                Conference Committee
            </a>

            <a href="{{ route('committee.papers.index', $conference_id ?? request()->route('conference_id')) }}"
                class="sidebar-link {{ request()->routeIs('committee.papers.*') || request()->routeIs('committee.review.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                    <polyline points="10 9 9 9 8 9" />
                </svg>
                Paper Submission
            </a>

            <a href="{{ route('committee.payments.index', $conference_id ?? request()->route('conference_id')) }}"
                class="sidebar-link {{ request()->routeIs('committee.payments.*') ? 'active' : '' }}">
                <div style="position: relative; width: 20px; height: 20px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        style="position: absolute; top: 0; left: 0;">
                        <rect x="2" y="5" width="20" height="14" rx="2" />
                        <line x1="2" y1="10" x2="22" y2="10" />
                    </svg>
                    <div
                        style="position: absolute; bottom: -2px; right: -2px; background: #FF5722; color: white; border-radius: 50%; width: 10px; height: 10px; border: 1.5px solid #fff;">
                    </div>
                </div>
                Payments
            </a>
            <a href="{{ route('committee.profile.show', $conference_id ?? request()->route('conference_id')) }}"
                class="sidebar-link {{ request()->routeIs('committee.profile.*') ? 'active' : '' }}"
                style="margin-top: auto; border-top: 1px solid rgba(255,255,255,0.1);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                My Account
            </a>
        </nav>

        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                        <polyline points="16 17 21 12 16 7" />
                        <line x1="21" y1="12" x2="9" y2="12" />
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="top-bar">
            <div class="top-bar-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                    <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                </svg>
            </div>
            <div class="top-bar-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                </svg>
            </div>
        </div>

        @yield('content')
    </main>

    @yield('scripts')

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
</body>

</html>