<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Participant Panel') - PaperFlo</title>
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
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: #f5f5f7;
            min-height: 100vh;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: 240px;
            background: #fff;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding: 24px 16px;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #eee;
            z-index: 1000;
        }

        .sidebar-logo {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 800;
            color: #FF5722;
            margin-bottom: 32px;
            padding-left: 8px;
            letter-spacing: -0.5px;
            text-decoration: none;
            display: block;
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
            color: #FF5722;
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
            margin-left: 240px;
            flex: 1;
            padding: 32px;
            min-height: 100vh;
        }

        .top-bar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 32px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: #FF5722;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
        }

        .user-name {
            font-size: 14px;
            font-weight: 600;
            color: #1a1a2e;
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
    <!-- Sidebar -->
    <aside class="sidebar">
        <a href="{{ route('home') }}" class="sidebar-logo">PaperFlo.</a>

        <nav class="sidebar-nav">
            <a href="{{ route('participant.conferences') }}"
                class="sidebar-link {{ request()->routeIs('participant.conferences') || request()->routeIs('home') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" />
                    <rect x="14" y="3" width="7" height="7" />
                    <rect x="14" y="14" width="7" height="7" />
                    <rect x="3" y="14" width="7" height="7" />
                </svg>
                Dashboard
            </a>

            <a href="{{ route('participant.profile.show') }}"
                class="sidebar-link {{ request()->routeIs('participant.profile.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                </svg>
                My Profile
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

    <!-- Main Content -->
    <main class="main-content">
        <div class="top-bar">
            <div style="display: flex; align-items: center; gap: 12px;">
                <!-- Notification Bell -->
                <button class="notif-btn" onclick="toggleNotifSidebar()" title="Notifications" id="notifBell">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                        <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                    </svg>
                    <span class="notif-badge" id="notifCount" style="display: none;">0</span>
                </button>

                <div class="user-profile">
                    <div class="user-avatar">{{ substr(session('user_name', 'U'), 0, 1) }}</div>
                    <span class="user-name">{{ session('user_name', 'User') }}</span>
                </div>
            </div>
        </div>

        @yield('content')
    </main>

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
            fetch(NOTIF_FETCH_URL, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(r => r.json())
                .then(data => renderNotifications(data))
                .catch(err => console.error('Notification fetch error:', err));
        }

        function handleNotifClick(id, link, status) {
            if (status === 'unread') {
                fetch(NOTIF_READ_URL + '/' + id + '/read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(() => fetchNotifications());
            }
            if (link) {
                window.location.href = link;
            }
        }

        function markAllAsRead() {
            fetch(NOTIF_READ_ALL_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(() => fetchNotifications());
        }

        // Initial fetch + poll every 30 seconds
        fetchNotifications();
        setInterval(fetchNotifications, 30000);

        // Realtime Listener
        document.addEventListener('DOMContentLoaded', () => {
            if (window.Echo) {
                window.Echo.private(`App.Models.User.{{ session('user_id') }}`)
                    .listen('NotificationSent', (e) => {
                        console.log('Notification received:', e);
                        fetchNotifications();

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