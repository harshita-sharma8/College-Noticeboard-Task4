<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: security/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Notice Board</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased min-h-screen">

    <div class="max-w-6xl mx-auto px-4 py-8">

        <!-- 1. TOP DIGITAL NOTICE BOARD BANNER -->
        <header class="bg-gradient-to-r from-blue-700 to-indigo-800 text-white rounded-2xl p-8 mb-6 shadow-lg flex flex-col md:flex-row justify-between items-center">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="bg-blue-500/30 text-blue-200 text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-wider border border-blue-400/30">Live Portal</span>
                    <span class="text-blue-200 text-xs">• Updated Real-time</span>
                </div>
                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight">Digital Notice Board</h1>
                <p class="text-blue-100 mt-2 text-sm md:text-base max-w-xl">Stay updated with the latest academic announcements, events, exams, and placement schedules in one place.</p>
            </div>
            <div class="mt-6 md:mt-0">
                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-4 text-center">
                    <span class="block text-2xl font-bold" id="live-clock">--:--</span>
                    <span class="text-xs text-blue-200 uppercase tracking-widest">Campus Time</span>
                </div>
            </div>
        </header>

        <!-- 2. WELCOME SECTION -->
        <section class="mb-6 bg-white border border-gray-100 rounded-xl p-5 shadow-sm flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Welcome to our notice board </h2>
                <p class="text-gray-500 text-sm mt-0.5">Explore categories below to filter announcements tailored for you.</p>
            </div>
            <a href="security/logout.php" class="btn btn-danger bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-md text-xs">Logout</a>

            <!-- Notification Bell & Dropdown Container -->
            <div class="relative">

                <button onclick="toggleNotifications()" class="relative bg-white/10 hover:bg-white/20 text-black p-2.5 rounded-xl transition flex items-center gap-2 border border-black/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="text-sm font-medium hidden md:inline">Notifications</span>

                    <!-- Notification Unread Badge -->
                    <span id="notif-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full hidden">0</span>
                </button>

                <!-- Dropdown List Box -->
                <div id="notif-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-100 p-3 z-50 text-gray-800">
                    <div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-100">
                        <h4 class="font-bold text-sm text-gray-700">Recent Notifications</h4>
                        <span class="text-xs text-blue-600 font-medium">Alerts</span>
                    </div>
                    <div id="notif-list" class="space-y-2 max-h-64 overflow-y-auto">
                        <!-- Dynamic notification items will populate here -->
                        <p class="text-gray-400 text-xs text-center py-4">No new notifications</p>
                    </div>
                </div>
            </div>
            
        </section>

        <!-- 3. CATEGORY NAVBAR / TABS -->
        <nav class="flex flex-wrap gap-2 mb-8 bg-white p-3 rounded-xl border border-gray-200 shadow-sm" id="category-tabs">
            <button onclick="loadNotices('All')" class="tab-btn bg-blue-600 text-white px-5 py-2.5 rounded-lg text-sm font-semibold shadow-sm transition duration-200 hover:bg-blue-700">All Notices</button>
            <button onclick="loadNotices('Events')" class="tab-btn bg-gray-100 text-gray-700 px-5 py-2.5 rounded-lg text-sm font-semibold transition duration-200 hover:bg-gray-200">Events</button>
            <button onclick="loadNotices('Holidays')" class="tab-btn bg-gray-100 text-gray-700 px-5 py-2.5 rounded-lg text-sm font-semibold transition duration-200 hover:bg-gray-200">Holidays</button>
            <button onclick="loadNotices('Exams')" class="tab-btn bg-gray-100 text-gray-700 px-5 py-2.5 rounded-lg text-sm font-semibold transition duration-200 hover:bg-gray-200">Exams</button>
            <button onclick="loadNotices('Internships')" class="tab-btn bg-gray-100 text-gray-700 px-5 py-2.5 rounded-lg text-sm font-semibold transition duration-200 hover:bg-gray-200">Internships</button>
            <button onclick="loadNotices('Placements')" class="tab-btn bg-gray-100 text-gray-700 px-5 py-2.5 rounded-lg text-sm font-semibold transition duration-200 hover:bg-gray-200">Placements</button>
        </nav>

        <!-- 4. DYNAMIC NOTICE CARDS GRID -->
        <main id="notice-container" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Cards will be dynamically injected here via JavaScript -->
        </main>

    </div>

    <!-- JavaScript for Live Clock & AJAX Notice Loading -->
    <script>
        // Real-time Clock Function
        function updateClock() {
            const now = new Date();
            document.getElementById('live-clock').innerText = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }
        setInterval(updateClock, 1000);
        updateClock();

        // AJAX Notice Loader Function with Tab Highlighting & Card Grid Layout
        async function loadNotices(category = 'All') {
            // 1. Update Active Tab UI Styles
            document.querySelectorAll('.tab-btn').forEach(btn => {
                if (btn.innerText.includes(category) || (category === 'All' && btn.innerText.includes('All'))) {
                    btn.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                    btn.classList.add('bg-blue-600', 'text-white', 'shadow-md');
                } else {
                    btn.classList.remove('bg-blue-600', 'text-white', 'shadow-md');
                    btn.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                }
            });

            try {
                // 2. Fetch notices and notifications from backend API
                const res = await fetch(`api/get_notices.php?category=${encodeURIComponent(category)}`);
                const data = await res.json();

                // 3. Update Notification Badge Count
                const badge = document.getElementById('notif-badge');
                if (badge) {
                    if (data.unread_notifications && data.unread_notifications > 0) {
                        badge.innerText = data.unread_notifications;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }

                // 4. Populate Notifications Dropdown List
                const notifList = document.getElementById('notif-list');
                if (notifList && data.notifications) {
                    if (data.notifications.length > 0) {
                        notifList.innerHTML = data.notifications.map(n => `
                            <div class="p-2.5 bg-gray-50 rounded-lg border-l-4 border-blue-500 hover:bg-blue-50/50 transition">
                                <strong class="block text-xs font-bold text-gray-800">${n.title}</strong>
                                <span class="text-gray-400 text-[10px]">${n.category} • ${n.created_at}</span>
                            </div>
                        `).join('');
                    } else {
                        notifList.innerHTML = `<p class="text-gray-400 text-xs text-center py-4">No notifications available</p>`;
                    }
                }

                // 5. Render Notice Cards in Grid
                const container = document.getElementById('notice-container');
                if (!data.notices || data.notices.length === 0) {
                    container.innerHTML = `
                        <div class="col-span-full text-center py-12 bg-white rounded-xl border border-dashed border-gray-300">
                            <p class="text-gray-500 font-medium">No notices found under the "${category}" category.</p>
                        </div>`;
                    return;
                }

                container.innerHTML = data.notices.map(n => `
                    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition duration-200 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-xs bg-blue-50 text-blue-700 font-bold px-3 py-1 rounded-full uppercase tracking-wider">${n.category}</span>
                                <span class="text-xs text-gray-400 font-medium">${n.created_at}</span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800 mb-2 leading-snug">${n.title}</h3>
                            <p class="text-gray-600 text-sm leading-relaxed mb-4">${n.content}</p>
                        </div>
                    </div>
                `).join('');

            } catch (error) {
                console.error("Error fetching data:", error);
            }
        }
        // Toggle Dropdown Visibility Function
        function toggleNotifications() {
            const dropdown = document.getElementById('notif-dropdown');
            dropdown.classList.toggle('hidden');
        }

        // Initial Page Load
        loadNotices('All');
    </script>
</body>
</html>