<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'PaperFlo')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/js/app.js'])
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

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
        }


        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1544531586-fde5298cdd40?auto=format&fit=crop&q=80&w=1200');
            background-size: cover;
            background-position: center;
            color: #fff;
            padding: 60px 10%;
            text-align: center;
        }

        .hero h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            letter-spacing: -1px;
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

        /* Conference Sub-Navigation */
        .conference-nav {
            background: #fff;
            border-bottom: 1px solid #eee;
            position: sticky;
            top: 72px;
            z-index: 900;
            margin-bottom: 40px;
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

        /* Notification Bell */
        .notif-btn {
            position: relative;
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            border-radius: 10px;
            transition: background 0.2s;
            color: #555;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .notif-btn:hover {
            background: #f0f0f2;
            color: #FF5722;
        }

        .notif-badge {
            position: absolute;
            top: 2px;
            right: 2px;
            background: #FF5722;
            color: #fff;
            font-size: 10px;
            font-weight: 800;
            min-width: 18px;
            height: 18px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
            line-height: 1;
        }

        /* Notification Sidebar */
        .notif-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
            z-index: 9998;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
        }

        .notif-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .notif-sidebar {
            position: fixed;
            top: 0;
            right: -420px;
            width: 420px;
            height: 100vh;
            background: #fff;
            box-shadow: -8px 0 30px rgba(0, 0, 0, 0.1);
            z-index: 9999;
            transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
        }

        .notif-sidebar.active {
            right: 0;
        }

        .notif-header {
            padding: 24px 24px 16px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .notif-header h2 {
            font-size: 20px;
            font-weight: 800;
            color: #1a1a2e;
            margin: 0;
        }

        .notif-header-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .mark-all-btn {
            background: none;
            border: none;
            color: #FF5722;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            padding: 6px 12px;
            border-radius: 8px;
            transition: background 0.2s;
        }

        .mark-all-btn:hover {
            background: #fff3e0;
        }

        .notif-close-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: #999;
            padding: 6px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .notif-close-btn:hover {
            background: #f5f5f5;
            color: #333;
        }

        .notif-list {
            flex: 1;
            overflow-y: auto;
            padding: 8px 0;
        }

        .notif-item {
            padding: 16px 24px;
            border-bottom: 1px solid #f5f5f5;
            cursor: pointer;
            transition: background 0.2s;
            display: flex;
            gap: 14px;
            align-items: flex-start;
        }

        .notif-item:hover {
            background: #fafafa;
        }

        .notif-item.unread {
            background: #fff8f5;
        }

        .notif-item.unread:hover {
            background: #fff0eb;
        }

        .notif-dot {
            width: 10px;
            height: 10px;
            min-width: 10px;
            border-radius: 50%;
            margin-top: 5px;
        }

        .notif-dot.unread {
            background: #FF5722;
        }

        .notif-dot.read {
            background: #e0e0e0;
        }

        .notif-content {
            flex: 1;
        }

        .notif-text {
            font-size: 14px;
            color: #333;
            line-height: 1.5;
            font-weight: 500;
        }

        .notif-item.unread .notif-text {
            font-weight: 600;
            color: #1a1a2e;
        }

        .notif-time {
            font-size: 12px;
            color: #999;
            margin-top: 4px;
        }

        .notif-empty {
            padding: 60px 24px;
            text-align: center;
            color: #999;
        }

        .notif-empty svg {
            margin-bottom: 16px;
            color: #ddd;
        }

        .notif-empty p {
            font-size: 15px;
            font-weight: 600;
        }
    </style>
    @yield('styles')
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

                <!-- Notification Bell -->
                <button class="notif-btn" onclick="toggleNotifSidebar()" title="Notifications" id="notifBell"
                    style="margin-left: 15px;">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                        <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                    </svg>
                    <span class="notif-badge" id="notifCount" style="display: none;">0</span>
                </button>
            @else
                <a href="{{ route('login.form') }}">Login</a>
                <a href="{{ route('participant.register.form') }}" class="btn-nav">Sign Up</a>
            @endif
        </div>
    </div>

    @yield('hero')

    @yield('content')

    <!-- Notification Overlay -->
    <div class="notif-overlay" id="notifOverlay" onclick="toggleNotifSidebar()"></div>

    <!-- Notification Sidebar -->
    <div class="notif-sidebar" id="notifSidebar">
        <div class="notif-header">
            <h2>Notifications</h2>
            <div class="notif-header-actions">
                <button class="mark-all-btn" onclick="markAllAsRead()" id="markAllBtn" style="display: none;">
                    Mark all as read
                </button>
                <button class="notif-close-btn" onclick="toggleNotifSidebar()">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 24 24">
                        <path d="M18 6L6 18M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        <div class="notif-list" id="notifList">
            <div class="notif-empty">
                <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                    <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                </svg>
                <p>No notifications yet</p>
            </div>
        </div>
    </div>

    <script>
        const NOTIF_FETCH_URL = "{{ route('notifications.index') }}";
        const NOTIF_READ_URL = "{{ url('/notifications') }}";
        const NOTIF_READ_ALL_URL = "{{ route('notifications.markAllAsRead') }}";
        const CSRF_TOKEN = "{{ csrf_token() }}";

        function toggleNotifSidebar() {
            document.getElementById('notifSidebar').classList.toggle('active');
            document.getElementById('notifOverlay').classList.toggle('active');
        }

        function timeAgo(dateStr) {
            const seconds = Math.floor((new Date() - new Date(dateStr)) / 1000);
            if (seconds < 60) return 'Just now';
            const minutes = Math.floor(seconds / 60);
            if (minutes < 60) return minutes + 'm ago';
            const hours = Math.floor(minutes / 60);
            if (hours < 24) return hours + 'h ago';
            const days = Math.floor(hours / 24);
            if (days < 7) return days + 'd ago';
            return new Date(dateStr).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        }

        function renderNotifications(data) {
            const list = document.getElementById('notifList');
            const countEl = document.getElementById('notifCount');
            const markAllBtn = document.getElementById('markAllBtn');

            if (data.unread_count > 0) {
                countEl.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                countEl.style.display = 'flex';
                markAllBtn.style.display = 'block';
            } else {
                countEl.style.display = 'none';
                markAllBtn.style.display = 'none';
            }

            if (!data.notifications || data.notifications.length === 0) {
                list.innerHTML = `
                    <div class="notif-empty">
                        <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                            <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                        </svg>
                        <p>No notifications yet</p>
                    </div>`;
                return;
            }

            list.innerHTML = data.notifications.map(n => `
                <div class="notif-item ${n.status}" onclick="handleNotifClick(${n.notification_id}, '${n.link || ''}', '${n.status}')">
                    <div class="notif-dot ${n.status}"></div>
                    <div class="notif-content">
                        <div class="notif-text">${n.content}</div>
                        <div class="notif-time">${timeAgo(n.created_at)}</div>
                    </div>
                </div>
            `).join('');
        }

        function fetchNotifications() {
            fetch(NOTIF_FETCH_URL)
                .then(response => response.json())
                .then(data => renderNotifications(data))
                .catch(error => console.error('Error fetching notifications:', error));
        }

        function markAllAsRead() {
            fetch(NOTIF_READ_ALL_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        fetchNotifications();
                    }
                });
        }

        function handleNotifClick(id, link, status) {
            if (status === 'unread') {
                fetch(`${NOTIF_READ_URL}/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    }
                });
            }
            if (link) {
                window.location.href = link;
            } else {
                fetchNotifications(); // Refresh list if simple read
            }
        }

        // Initial fetch
        document.addEventListener('DOMContentLoaded', fetchNotifications);

        // Poll every 60 seconds
        setInterval(fetchNotifications, 60000);

        // Realtime Listener
        document.addEventListener('DOMContentLoaded', () => {
            if (window.Echo) {
                window.Echo.private(`App.Models.User.{{ session('user_id') }}`)
                    .listen('NotificationSent', (e) => {
                        console.log('Notification received:', e);
                        fetchNotifications(); // Refresh list to get the new notification with full details

                        // Optional: Show a toast or immediate UI update
                        const countEl = document.getElementById('notifCount');
                        let count = parseInt(countEl.textContent || '0');
                        count++;
                        countEl.textContent = count > 99 ? '99+' : count;
                        countEl.style.display = 'flex';
                    });
            }
        });
    </script>

    @yield('scripts')
</body>

</html>