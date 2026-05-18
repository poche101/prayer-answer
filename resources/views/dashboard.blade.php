<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Early Morning Prayer & Answer Reports</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- SheetJS for Excel export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    @vite('resources/css/app.css')
    <style>
        *, *::before, *::after { box-sizing: border-box; }

        :root {
            --bg:        #faf6ef;
            --bg-card:   rgba(255,255,255,0.78);
            --bg-hover:  rgba(255,255,255,0.95);
            --border:    rgba(180,140,90,0.18);
            --border-md: rgba(180,140,90,0.28);
            --border-strong: rgba(180,140,90,0.45);
            --text-1:    #2c1f0f;
            --text-2:    #7a5230;
            --text-3:    #b89a72;
            --text-4:    #d4b896;
            --amber:     #b8894a;
            --amber-dk:  #7a5230;
            --amber-lt:  rgba(184,137,74,0.10);
            --amber-glow:rgba(184,137,74,0.18);
            --success:   #5a8a5a;
            --success-bg:rgba(90,138,90,0.08);
            --danger:    #a03030;
            --danger-bg: rgba(160,48,48,0.08);
            --shadow-sm: 0 1px 4px rgba(160,120,70,0.07);
            --shadow-md: 0 4px 20px rgba(160,120,70,0.10);
            --shadow-lg: 0 8px 40px rgba(160,120,70,0.13);
            --radius-sm: 10px;
            --radius-md: 14px;
            --radius-lg: 20px;
            --radius-xl: 28px;
        }

        [x-cloak] { display: none !important; }

        body {
            background-color: var(--bg);
            color: var(--text-1);
            font-family: 'Instrument Sans', sans-serif;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* Ambient bg blobs */
        .bg-blobs { position: fixed; inset: 0; pointer-events: none; z-index: 0; overflow: hidden; }
        .bg-blobs span { position: absolute; border-radius: 50%; }
        .bg-blobs span:nth-child(1) { top: -10%; left: -5%; width: 50%; height: 50%; background: radial-gradient(circle, rgba(214,174,120,0.15) 0%, transparent 70%); }
        .bg-blobs span:nth-child(2) { bottom: -10%; right: -5%; width: 45%; height: 45%; background: radial-gradient(circle, rgba(188,145,100,0.12) 0%, transparent 70%); }
        .bg-blobs span:nth-child(3) { top: 35%; left: 50%; width: 35%; height: 35%; background: radial-gradient(circle, rgba(230,200,160,0.10) 0%, transparent 70%); }

        /* Cards */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-sm);
            backdrop-filter: blur(12px);
        }
        .card-hover { transition: box-shadow 0.2s, border-color 0.2s, background 0.2s; }
        .card-hover:hover { box-shadow: var(--shadow-md); border-color: var(--border-md); background: var(--bg-hover); }

        /* Nav */
        nav {
            background: rgba(250,246,239,0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            position: sticky; top: 0; z-index: 100;
        }
        .nav-inner {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 20px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .nav-brand { display: flex; align-items: center; gap: 10px; min-width: 0; flex-shrink: 1; }
        .nav-brand-text { display: flex; align-items: baseline; gap: 4px; min-width: 0; }
        .nav-brand-title { font-size: 15px; font-weight: 700; color: var(--text-1); white-space: nowrap; }
        .nav-brand-sub { font-size: 13px; font-weight: 400; color: var(--text-2); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .nav-right { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
        .nav-count { font-size: 12px; color: var(--text-3); padding-right: 12px; border-right: 1px solid var(--border); white-space: nowrap; }

        /* Stat cards */
        .stat-icon {
            width: 48px; height: 48px; flex-shrink: 0;
            border-radius: var(--radius-md);
            background: var(--amber-lt);
            border: 1px solid var(--border-md);
            display: flex; align-items: center; justify-content: center;
            color: var(--amber);
            transition: background 0.3s, color 0.3s;
        }
        .stat-card:hover .stat-icon { background: var(--amber-dk); color: #fdf8f2; }

        /* Stats grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 28px;
        }

        /* Table */
        table { width: 100%; border-collapse: collapse; }
        thead th {
            padding: 14px 20px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--text-3);
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
            background: rgba(250,246,239,0.5);
        }
        tbody tr { border-bottom: 1px solid var(--border); transition: background 0.15s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: rgba(255,255,255,0.6); }
        tbody td { padding: 14px 20px; vertical-align: middle; }

        /* Inputs */
        .field {
            background: rgba(250,246,239,0.8);
            border: 1px solid var(--border-md);
            border-radius: var(--radius-md);
            padding: 11px 16px;
            font-size: 14px;
            color: var(--text-1);
            font-family: 'Instrument Sans', sans-serif;
            width: 100%;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .field:focus { border-color: var(--amber); box-shadow: 0 0 0 3px rgba(184,137,74,0.12); }
        .field::placeholder { color: var(--text-4); }
        input[type="date"]::-webkit-calendar-picker-indicator { opacity: 0.4; cursor: pointer; }

        /* Buttons */
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 10px 20px;
            font-size: 13px; font-weight: 600;
            border-radius: var(--radius-md);
            border: 1px solid transparent;
            cursor: pointer; transition: all 0.18s;
            font-family: 'Instrument Sans', sans-serif;
            white-space: nowrap;
        }
        .btn-ghost { background: rgba(255,255,255,0.5); border-color: var(--border-md); color: var(--text-2); }
        .btn-ghost:hover { background: rgba(255,255,255,0.9); border-color: var(--border-strong); }
        .btn-primary { background: var(--amber-dk); color: #fdf8f2; border-color: var(--amber-dk); box-shadow: 0 3px 14px rgba(122,82,48,0.22); }
        .btn-primary:hover { background: #8f6238; box-shadow: 0 4px 18px rgba(122,82,48,0.28); }
        .btn-danger { background: var(--danger); color: #fff; border-color: var(--danger); box-shadow: 0 3px 14px rgba(160,48,48,0.2); }
        .btn-danger:hover { background: #b83535; }
        .btn-success { background: #3d7a3d; color: #fff; border-color: #3d7a3d; box-shadow: 0 3px 14px rgba(61,122,61,0.22); }
        .btn-success:hover { background: #2f6030; box-shadow: 0 4px 18px rgba(61,122,61,0.28); }
        .btn-icon {
            width: 36px; height: 36px; padding: 0; flex-shrink: 0;
            border-radius: var(--radius-sm);
            background: transparent; border-color: transparent; color: var(--text-3);
        }
        .btn-icon:hover { background: var(--amber-lt); color: var(--amber-dk); border-color: var(--border-md); }
        .btn-icon.danger:hover { background: var(--danger-bg); color: var(--danger); border-color: rgba(160,48,48,0.2); }

        /* Badges */
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 100px; font-size: 11px; font-weight: 600; }
        .badge-success { background: var(--success-bg); color: var(--success); border: 1px solid rgba(90,138,90,0.2); }
        .badge-amber   { background: var(--amber-lt);   color: var(--amber-dk); border: 1px solid var(--border-md); }

        /* Report Link cell */
        .link-cell { display: flex; align-items: center; gap: 8px; max-width: 200px; }
        .link-url { font-size: 12px; color: var(--amber-dk); font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-decoration: none; flex: 1; min-width: 0; }
        .link-url:hover { text-decoration: underline; }
        .link-no-url { font-size: 12px; color: var(--text-4); font-style: italic; }
        .btn-copy {
            flex-shrink: 0; width: 28px; height: 28px; padding: 0; border-radius: 8px;
            background: transparent; border: 1px solid transparent; color: var(--text-3);
            cursor: pointer; display: inline-flex; align-items: center; justify-content: center;
            transition: all 0.18s; font-family: 'Instrument Sans', sans-serif;
        }
        .btn-copy:hover { background: var(--amber-lt); color: var(--amber-dk); border-color: var(--border-md); }
        .btn-copy.copied { background: var(--success-bg); color: var(--success); border-color: rgba(90,138,90,0.2); }

        /* Toast */
        .toast-wrap { position: fixed; bottom: 20px; right: 20px; z-index: 200; display: flex; flex-direction: column; gap: 10px; max-width: calc(100vw - 40px); }
        .toast {
            display: flex; align-items: center; gap: 12px;
            padding: 14px 20px; border-radius: var(--radius-md);
            background: rgba(255,255,255,0.97); border: 1px solid var(--border-strong);
            box-shadow: var(--shadow-lg); min-width: 240px; max-width: 360px;
            font-size: 13px; font-weight: 600; color: var(--text-1);
        }
        .toast-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .toast-dot.success { background: var(--success); }
        .toast-dot.error   { background: var(--danger); }

        /* Modal overlay */
        .overlay {
            position: fixed; inset: 0; z-index: 150;
            display: flex; align-items: flex-end; justify-content: center; padding: 0;
            background: rgba(44,31,15,0.35); backdrop-filter: blur(8px);
        }
        .modal {
            background: #fdf8f2; border: 1px solid var(--border-strong);
            border-radius: var(--radius-xl) var(--radius-xl) 0 0;
            box-shadow: var(--shadow-lg); width: 100%; max-width: 480px;
            padding: 28px 24px 36px; position: relative; z-index: 1;
            max-height: 90vh; overflow-y: auto;
        }
        .modal-center {
            align-items: center;
        }
        .modal-center .modal { border-radius: var(--radius-xl); max-height: calc(100vh - 40px); margin: 20px; }
        .modal-lg { max-width: 640px; }
        .modal-label { display: block; font-size: 10px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase; color: var(--text-3); margin-bottom: 7px; }

        /* Pagination */
        .page-btn {
            width: 36px; height: 36px; border-radius: var(--radius-sm);
            border: 1px solid var(--border-md); background: rgba(255,255,255,0.6); color: var(--text-2);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.15s;
        }
        .page-btn:hover:not(:disabled) { background: var(--amber-dk); color: #fdf8f2; border-color: var(--amber-dk); }
        .page-btn:disabled { opacity: 0.3; cursor: not-allowed; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border-strong); border-radius: 10px; }

        /* Loading spinner */
        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner { animation: spin 0.8s linear infinite; }

        /* Progress bar */
        .progress-bar { height: 3px; border-radius: 100px; background: var(--border-md); overflow: hidden; }
        .progress-fill { height: 100%; background: var(--amber-dk); border-radius: 100px; transition: width 0.4s; }

        /* Search icon offset */
        .search-wrap { position: relative; }
        .search-wrap .field { padding-left: 42px; }
        .search-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-3); pointer-events: none; }

        /* Filters row */
        .filters-row {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            align-items: center;
        }
        .filters-row .search-wrap { flex: 1; min-width: 180px; }
        .filters-date { width: auto; min-width: 130px; }

        /* ── Weekly Leaderboard ── */
        @keyframes shimmer { 0% { background-position: -200% center; } 100% { background-position: 200% center; } }
        @keyframes rise { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes bar-grow { from { width: 0%; } }
        @keyframes crown-pulse {
            0%, 100% { transform: scale(1) rotate(-8deg); filter: drop-shadow(0 0 4px rgba(184,137,74,0.4)); }
            50%       { transform: scale(1.18) rotate(-8deg); filter: drop-shadow(0 0 10px rgba(184,137,74,0.8)); }
        }

        .leaderboard-strip {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 12px;
            margin-bottom: 28px;
        }

        .day-card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: var(--radius-lg); padding: 18px 16px 16px;
            position: relative; overflow: hidden;
            backdrop-filter: blur(12px); box-shadow: var(--shadow-sm);
            transition: box-shadow 0.2s, border-color 0.2s, transform 0.2s;
            animation: rise 0.4s ease both;
        }
        .day-card:hover { box-shadow: var(--shadow-md); border-color: var(--border-md); transform: translateY(-2px); }

        .day-card.is-today {
            background: linear-gradient(145deg, rgba(122,82,48,0.97) 0%, rgba(90,58,30,0.97) 100%);
            border-color: var(--amber); box-shadow: 0 6px 28px rgba(122,82,48,0.30);
        }
        .day-card.is-today .day-label   { color: rgba(253,248,242,0.6); }
        .day-card.is-today .day-name    { color: #fdf8f2; }
        .day-card.is-today .winner-name { color: #fdf8f2; }
        .day-card.is-today .winner-church { color: rgba(253,248,242,0.65); }
        .day-card.is-today .att-count   { color: #fdf8f2; }
        .day-card.is-today .att-label   { color: rgba(253,248,242,0.5); }
        .day-card.is-today .bar-track   { background: rgba(255,255,255,0.12); }
        .day-card.is-today .bar-fill    { background: linear-gradient(90deg, rgba(253,248,242,0.5), rgba(253,248,242,0.9)); }
        .day-card.is-today .no-data     { color: rgba(253,248,242,0.4); }

        .day-card.is-today::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,0.06) 50%, transparent 60%);
            background-size: 200% 100%; animation: shimmer 3s linear infinite; pointer-events: none;
        }

        .day-label { font-size: 9px; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase; color: var(--text-4); margin: 0 0 2px; }
        .day-name  { font-size: 14px; font-weight: 700; color: var(--text-1); margin: 0 0 14px; }

        .crown-wrap {
            position: absolute; top: 14px; right: 14px; font-size: 16px; line-height: 1;
            animation: crown-pulse 2.4s ease-in-out infinite;
        }
        .day-card:not(.is-today) .crown-wrap { animation: none; opacity: 0.7; }

        .winner-name   { font-size: 12px; font-weight: 700; color: var(--text-1); margin: 0 0 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .winner-church { font-size: 10px; font-weight: 600; letter-spacing: 0.08em; color: var(--text-3); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0 0 12px; }

        .bar-track { height: 4px; border-radius: 100px; background: var(--border-md); overflow: hidden; margin-bottom: 8px; }
        .bar-fill  { height: 100%; border-radius: 100px; background: linear-gradient(90deg, var(--amber) 0%, var(--amber-dk) 100%); animation: bar-grow 0.7s cubic-bezier(0.22, 1, 0.36, 1) both; }

        .att-row   { display: flex; align-items: baseline; gap: 5px; }
        .att-count { font-size: 20px; font-weight: 700; color: var(--text-1); line-height: 1; }
        .att-label { font-size: 10px; font-weight: 600; letter-spacing: 0.1em; color: var(--text-3); text-transform: uppercase; }

        .no-data { font-size: 11px; color: var(--text-4); margin: 12px 0 0; font-style: italic; }

        .leaderboard-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; flex-wrap: wrap; gap: 8px; }
        .leaderboard-title { font-size: 11px; font-weight: 700; letter-spacing: 0.16em; text-transform: uppercase; color: var(--text-3); display: flex; align-items: center; gap: 7px; }
        .leaderboard-week  { font-size: 11px; color: var(--text-4); font-weight: 500; }

        .day-card:nth-child(1) { animation-delay: 0.05s; }
        .day-card:nth-child(2) { animation-delay: 0.10s; }
        .day-card:nth-child(3) { animation-delay: 0.15s; }
        .day-card:nth-child(4) { animation-delay: 0.20s; }
        .day-card:nth-child(5) { animation-delay: 0.25s; }

        /* Mobile card layout for table rows */
        .mobile-card {
            display: none;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 16px;
            margin-bottom: 10px;
            backdrop-filter: blur(8px);
        }
        .mobile-card-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 12px; gap: 10px; }
        .mobile-card-title  { font-size: 14px; font-weight: 700; color: var(--text-1); margin: 0 0 3px; }
        .mobile-card-sub    { font-size: 10px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: var(--amber); opacity: 0.8; }
        .mobile-card-actions { display: flex; gap: 4px; flex-shrink: 0; }
        .mobile-card-row    { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; font-size: 13px; color: var(--text-2); }
        .mobile-card-row:last-child { margin-bottom: 0; }
        .mobile-card-label  { font-size: 10px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: var(--text-3); min-width: 70px; }

        /* ── Responsive breakpoints ── */

        /* Tablet: ≤ 900px */
        @media (max-width: 900px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .leaderboard-strip { grid-template-columns: repeat(3, 1fr); }
            .leaderboard-strip .day-card:nth-child(4),
            .leaderboard-strip .day-card:nth-child(5) { display: block; }

            thead th:nth-child(3),  /* Testimony */
            thead th:nth-child(5),  /* Report Link */
            tbody td:nth-child(3),
            tbody td:nth-child(5)   { display: none; }
        }

        /* Mobile: ≤ 640px */
        @media (max-width: 640px) {
            .nav-inner { height: 56px; padding: 0 16px; }
            .nav-brand-sub { display: none; }
            .nav-count { display: none; }

            main { padding: 20px 14px 60px; }

            h1 { font-size: 20px !important; }

            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; margin-bottom: 20px; }
            .stat-icon { width: 40px; height: 40px; }
            .stat-icon i { width: 16px !important; height: 16px !important; }
            .card[style*="padding:24px"] { padding: 16px !important; }

            .leaderboard-strip { grid-template-columns: repeat(2, 1fr); gap: 10px; }
            .leaderboard-strip .day-card:nth-child(5) { display: none; }

            .filters-row { gap: 8px; }
            .filters-date { width: 100%; }
            .btn-export-label { display: none; }

            /* Hide desktop table, show mobile cards */
            .desktop-table { display: none !important; }
            .mobile-cards-list { display: block !important; }

            .card { border-radius: var(--radius-lg); }

            .pagination-footer { flex-direction: column; gap: 10px; align-items: flex-start !important; padding: 14px 16px !important; }

            .toast { min-width: 0; width: 100%; }
            .toast-wrap { right: 14px; left: 14px; bottom: 16px; }

            .modal { padding: 24px 16px 32px; border-radius: var(--radius-xl) var(--radius-xl) 0 0 !important; margin: 0 !important; }
            .modal-center .modal { border-radius: var(--radius-xl) !important; margin: 16px !important; }
        }

        /* Very small: ≤ 400px */
        @media (max-width: 400px) {
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .leaderboard-strip { grid-template-columns: 1fr 1fr; }
            .leaderboard-strip .day-card:nth-child(5) { display: none; }
        }

        .mobile-cards-list { display: none; padding: 14px; }
    </style>
</head>
<body x-data="dashboard()">

    <div class="bg-blobs"><span></span><span></span><span></span></div>

    {{-- ── Toasts ── --}}
    <div class="toast-wrap">
        <template x-for="toast in toasts" :key="toast.id">
            <div class="toast"
                 x-transition:enter="transition ease-out duration-250"
                 x-transition:enter-start="opacity-0 translate-y-3"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-180"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                <div class="toast-dot" :class="toast.type === 'success' ? 'success' : 'error'"></div>
                <span x-text="toast.message"></span>
            </div>
        </template>
    </div>

    {{-- ── Edit Modal ── --}}
    <div class="overlay" x-show="showEditModal" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="absolute inset-0" @click="showEditModal = false"></div>
        <div class="modal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0">

            <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;">
                <div style="width:40px;height:40px;flex-shrink:0;border-radius:10px;background:var(--amber-lt);border:1px solid var(--border-md);display:flex;align-items:center;justify-content:center;color:var(--amber-dk);">
                    <i data-lucide="edit-3" style="width:18px;height:18px;"></i>
                </div>
                <div>
                    <h3 style="font-size:17px;font-weight:700;color:var(--text-1);margin:0;">Edit Report</h3>
                    <p style="font-size:12px;color:var(--text-3);margin:0;" x-text="editForm.church"></p>
                </div>
            </div>

            <div style="display:grid;gap:14px;">
                <div>
                    <label class="modal-label">Church Name</label>
                    <input type="text" x-model="editForm.church" class="field">
                </div>
                <div>
                    <label class="modal-label">Group Name</label>
                    <input type="text" x-model="editForm.group" class="field">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label class="modal-label">Meeting Date</label>
                        <input type="date" x-model="editForm.meeting_date" class="field">
                    </div>
                    <div>
                        <label class="modal-label">Attendance</label>
                        <input type="number" x-model="editForm.attendance" min="1" class="field">
                    </div>
                </div>
                <div>
                    <label class="modal-label">Report Link</label>
                    <input type="url" x-model="editForm.prayer_link" class="field">
                </div>
            </div>

            <div style="display:flex;gap:10px;margin-top:24px;">
                <button @click="showEditModal = false" class="btn btn-ghost" style="flex:1;">Cancel</button>
                <button @click="confirmUpdate" class="btn btn-primary" style="flex:1;">Save Changes</button>
            </div>
        </div>
    </div>

    {{-- ── Delete Modal ── --}}
    <div class="overlay modal-center" x-show="showDeleteModal" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="absolute inset-0" @click="showDeleteModal = false"></div>
        <div class="modal" style="max-width:420px;text-align:center;"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <div style="width:56px;height:56px;border-radius:50%;background:var(--danger-bg);border:1px solid rgba(160,48,48,0.2);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;color:var(--danger);">
                <i data-lucide="trash-2" style="width:24px;height:24px;"></i>
            </div>
            <h3 style="font-size:18px;font-weight:700;margin:0 0 8px;color:var(--text-1);">Delete this record?</h3>
            <p style="font-size:13px;color:var(--text-2);line-height:1.6;margin:0 0 28px;">
                You are about to permanently remove the report for
                <strong x-text="reportToDelete?.church" style="color:var(--text-1);"></strong>.
                This cannot be undone.
            </p>
            <div style="display:flex;gap:10px;">
                <button @click="showDeleteModal = false" class="btn btn-ghost" style="flex:1;">Keep it</button>
                <button @click="confirmDelete" class="btn btn-danger" style="flex:1;">Delete</button>
            </div>
        </div>
    </div>

    {{-- ── Testimony Modal ── --}}
    <div class="overlay" x-show="showTestimonyModal" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="absolute inset-0" @click="showTestimonyModal = false"></div>
        <div class="modal modal-lg"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0">

            <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;gap:10px;">
                <div style="min-width:0;">
                    <h3 style="font-size:17px;font-weight:700;color:var(--text-1);margin:0 0 3px;word-break:break-word;" x-text="selectedTestimony?.church"></h3>
                    <p style="font-size:10px;font-weight:700;letter-spacing:0.14em;text-transform:uppercase;color:var(--amber);margin:0;" x-text="selectedTestimony?.group"></p>
                </div>
                <button @click="showTestimonyModal = false" class="btn btn-icon" style="flex-shrink:0;">
                    <i data-lucide="x" style="width:16px;height:16px;"></i>
                </button>
            </div>

            <div style="background:rgba(250,246,239,0.8);border:1px solid var(--border);border-radius:var(--radius-md);padding:18px;max-height:45vh;overflow-y:auto;">
                <p style="font-size:14px;line-height:1.75;color:var(--text-1);white-space:pre-line;margin:0;" x-text="selectedTestimony?.testimony"></p>
            </div>

            <div style="display:flex;justify-content:flex-end;margin-top:18px;">
                <button @click="showTestimonyModal = false" class="btn btn-ghost">Close</button>
            </div>
        </div>
    </div>

    {{-- ── Nav ── --}}
    <nav>
        <div class="nav-inner">
            <div class="nav-brand">
                <div style="width:36px;height:36px;flex-shrink:0;border-radius:10px;background:var(--amber-lt);border:1px solid var(--border-md);display:flex;align-items:center;justify-content:center;">
                    <img src="/images/lz5.png" style="width:22px;height:22px;object-fit:contain;" alt="Logo">
                </div>
                <div class="nav-brand-text">
                    <span class="nav-brand-title">Celz5</span>
                    <span class="nav-brand-sub">· Early Morning Reports</span>
                </div>
            </div>

            <div class="nav-right">
                <div class="nav-count" x-text="`${reports.length} records`"></div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-ghost" style="font-size:12px;padding:8px 14px;">
                        <i data-lucide="log-out" style="width:14px;height:14px;"></i>
                        <span style="display:none;" class="sm-show">Sign Out</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    {{-- ── Main ── --}}
    <main style="max-width:1280px;margin:0 auto;padding:28px 20px 60px;position:relative;z-index:1;">

        {{-- Page header --}}
        <div style="margin-bottom:24px;">
            <h1 style="font-size:24px;font-weight:700;color:var(--text-1);margin:0 0 4px;">Reports Dashboard</h1>
            <p style="font-size:13px;color:var(--text-3);margin:0;">Manage prayer meeting submissions.</p>
        </div>

        {{-- ── Stat cards ── --}}
        <div class="stats-grid">
            <template x-for="stat in stats" :key="stat.label">
                <div class="card card-hover stat-card" style="padding:20px;display:flex;align-items:center;gap:14px;cursor:default;">
                    <div class="stat-icon">
                        <i :data-lucide="stat.icon" style="width:20px;height:20px;"></i>
                    </div>
                    <div>
                        <p style="font-size:10px;font-weight:700;letter-spacing:0.15em;text-transform:uppercase;color:var(--text-3);margin:0 0 4px;" x-text="stat.label"></p>
                        <p style="font-size:22px;font-weight:700;color:var(--text-1);margin:0;line-height:1;" x-text="stat.value"></p>
                    </div>
                </div>
            </template>
        </div>

        {{-- ── Weekly Leaderboard ── --}}
        <div x-show="!loading">
            <div class="leaderboard-header">
                <div class="leaderboard-title">
                    <span>&#x1F451;</span> Top Group This Week
                </div>
                <div class="leaderboard-week" x-text="weekLabel"></div>
            </div>

            <div class="leaderboard-strip">
                <template x-for="day in weeklyLeaders" :key="day.name">
                    <div class="day-card" :class="day.isToday ? 'is-today' : ''">
                        <div class="crown-wrap" x-show="day.winner">&#x1F451;</div>
                        <p class="day-label" x-text="day.shortName"></p>
                        <p class="day-name"  x-text="day.name"></p>
                        <template x-if="day.winner">
                            <div>
                                <p class="winner-name"   x-text="day.winner.group"></p>
                                <p class="winner-church" x-text="day.winner.church"></p>
                                <div class="bar-track"><div class="bar-fill" :style="`width:${day.pct}%`"></div></div>
                                <div class="att-row">
                                    <span class="att-count" x-text="day.winner.attendance.toLocaleString()"></span>
                                    <span class="att-label">present</span>
                                </div>
                            </div>
                        </template>
                        <template x-if="!day.winner">
                            <p class="no-data">No report yet</p>
                        </template>
                    </div>
                </template>
            </div>
        </div>

        {{-- ── Filters ── --}}
        <div class="filters-row">
            <div class="search-wrap">
                <i data-lucide="search" class="search-icon" style="width:16px;height:16px;"></i>
                <input type="text" x-model="search" @input="currentPage = 1"
                       placeholder="Search by church or group…" class="field">
            </div>

            <input type="date" x-model="filterDate" @change="currentPage = 1"
                   class="field filters-date">

            <button @click="resetFilters" class="btn btn-ghost" style="padding:10px 14px;" title="Reset filters">
                <i data-lucide="x-circle" style="width:14px;height:14px;"></i>
                <span class="btn-export-label">Reset</span>
            </button>

            <button @click="fetchReports(true)" class="btn btn-ghost" style="padding:10px 14px;" title="Refresh">
                <i data-lucide="refresh-cw" style="width:15px;height:15px;" :class="loading ? 'spinner' : ''"></i>
            </button>

            {{-- ── Excel Export Button ── --}}
            <button @click="exportToExcel" class="btn btn-success" title="Export to Excel">
                <i data-lucide="file-spreadsheet" style="width:15px;height:15px;"></i>
                <span class="btn-export-label">Export Excel</span>
            </button>
        </div>

        {{-- ── Table card (desktop) ── --}}
        <div class="card desktop-table" style="overflow:hidden;position:relative;">

            {{-- Loading overlay --}}
            <div x-show="loading" x-cloak
                 style="position:absolute;inset:0;background:rgba(250,246,239,0.65);backdrop-filter:blur(4px);z-index:20;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:12px;">
                <div style="width:36px;height:36px;border:3px solid var(--border-strong);border-top-color:var(--amber-dk);border-radius:50%;" class="spinner"></div>
                <span style="font-size:10px;font-weight:700;letter-spacing:0.18em;text-transform:uppercase;color:var(--text-3);">Loading</span>
            </div>

            <div style="overflow-x:auto;min-height:300px;">
                <table>
                    <thead>
                        <tr>
                            <th style="padding-left:24px;">Group / Church</th>
                            <th>Attendance</th>
                            <th>Testimony</th>
                            <th>Meeting Date</th>
                            <th>Report Link</th>
                            <th style="text-align:right;padding-right:24px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="report in paginatedReports" :key="report.id">
                            <tr>
                                <td style="padding-left:24px;">
                                    <div style="font-size:14px;font-weight:600;color:var(--text-1);" x-text="report.church"></div>
                                    <div style="font-size:10px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:var(--amber);margin-top:2px;opacity:0.8;" x-text="report.group"></div>
                                </td>
                                <td>
                                    <span class="badge badge-success">
                                        <i data-lucide="users" style="width:11px;height:11px;"></i>
                                        <span x-text="report.attendance"></span>
                                    </span>
                                </td>
                                <td>
                                    <p @click="openTestimony(report)"
                                       style="font-size:13px;color:var(--text-2);max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;cursor:pointer;margin:0;"
                                       :title="report.testimony ? 'Click to read full testimony' : ''"
                                       x-text="report.testimony || '—'"></p>
                                </td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:6px;font-size:13px;color:var(--text-2);">
                                        <i data-lucide="calendar" style="width:13px;height:13px;color:var(--text-3);flex-shrink:0;"></i>
                                        <span x-text="formatDate(report.meeting_date)"></span>
                                    </div>
                                </td>
                                <td>
                                    <template x-if="report.prayer_link">
                                        <div class="link-cell">
                                            <a :href="report.prayer_link" target="_blank" rel="noopener noreferrer"
                                               class="link-url" :title="report.prayer_link"
                                               x-text="report.prayer_link"></a>
                                            <button class="btn-copy" :class="copiedId === report.id ? 'copied' : ''"
                                                    :title="copiedId === report.id ? 'Copied!' : 'Copy link'"
                                                    @click="copyLink(report)">
                                                <template x-if="copiedId !== report.id">
                                                    <i data-lucide="copy" style="width:13px;height:13px;"></i>
                                                </template>
                                                <template x-if="copiedId === report.id">
                                                    <i data-lucide="check" style="width:13px;height:13px;"></i>
                                                </template>
                                            </button>
                                        </div>
                                    </template>
                                    <template x-if="!report.prayer_link">
                                        <span class="link-no-url">No link</span>
                                    </template>
                                </td>
                                <td style="text-align:right;padding-right:24px;">
                                    <div style="display:flex;align-items:center;justify-content:flex-end;gap:4px;">
                                        <button @click="triggerEdit(report)" class="btn btn-icon" title="Edit">
                                            <i data-lucide="edit-2" style="width:15px;height:15px;"></i>
                                        </button>
                                        <button @click="triggerDelete(report)" class="btn btn-icon danger" title="Delete">
                                            <i data-lucide="trash-2" style="width:15px;height:15px;"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <tr x-show="filteredReports.length === 0 && !loading">
                            <td colspan="6" style="padding:56px 24px;text-align:center;">
                                <div style="display:inline-flex;flex-direction:column;align-items:center;gap:10px;opacity:0.4;">
                                    <i data-lucide="inbox" style="width:36px;height:36px;color:var(--text-3);"></i>
                                    <p style="font-size:14px;font-weight:600;color:var(--text-1);margin:0;">No records found</p>
                                    <p style="font-size:12px;color:var(--text-3);margin:0;">Try adjusting your search or date filter</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Pagination footer --}}
            <div class="pagination-footer" style="display:flex;align-items:center;justify-content:space-between;padding:14px 24px;border-top:1px solid var(--border);background:rgba(250,246,239,0.4);">
                <div style="display:flex;align-items:center;gap:14px;">
                    <span style="font-size:12px;color:var(--text-3);"
                          x-text="`${filteredReports.length} result${filteredReports.length !== 1 ? 's' : ''} · Page ${currentPage} of ${totalPages}`"></span>
                    <div class="progress-bar" style="width:60px;">
                        <div class="progress-fill" :style="`width:${(currentPage/totalPages)*100}%`"></div>
                    </div>
                </div>
                <div style="display:flex;gap:6px;">
                    <button @click="prevPage" :disabled="currentPage === 1" class="page-btn">
                        <i data-lucide="chevron-left" style="width:16px;height:16px;"></i>
                    </button>
                    <button @click="nextPage" :disabled="currentPage === totalPages" class="page-btn">
                        <i data-lucide="chevron-right" style="width:16px;height:16px;"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- ── Mobile cards list ── --}}
        <div class="mobile-cards-list">

            {{-- Loading state --}}
            <div x-show="loading" style="text-align:center;padding:40px 0;display:flex;flex-direction:column;align-items:center;gap:12px;">
                <div style="width:32px;height:32px;border:3px solid var(--border-strong);border-top-color:var(--amber-dk);border-radius:50%;" class="spinner"></div>
                <span style="font-size:11px;font-weight:700;letter-spacing:0.15em;text-transform:uppercase;color:var(--text-3);">Loading…</span>
            </div>

            {{-- Empty state --}}
            <div x-show="filteredReports.length === 0 && !loading"
                 style="text-align:center;padding:56px 16px;display:flex;flex-direction:column;align-items:center;gap:10px;opacity:0.4;">
                <i data-lucide="inbox" style="width:32px;height:32px;color:var(--text-3);"></i>
                <p style="font-size:14px;font-weight:600;color:var(--text-1);margin:0;">No records found</p>
                <p style="font-size:12px;color:var(--text-3);margin:0;">Try adjusting your filters</p>
            </div>

            <template x-for="report in paginatedReports" :key="report.id">
                <div class="mobile-card">
                    <div class="mobile-card-header">
                        <div style="min-width:0;">
                            <p class="mobile-card-title" x-text="report.church"></p>
                            <p class="mobile-card-sub" x-text="report.group"></p>
                        </div>
                        <div class="mobile-card-actions">
                            <button @click="triggerEdit(report)" class="btn btn-icon" title="Edit">
                                <i data-lucide="edit-2" style="width:15px;height:15px;"></i>
                            </button>
                            <button @click="triggerDelete(report)" class="btn btn-icon danger" title="Delete">
                                <i data-lucide="trash-2" style="width:15px;height:15px;"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Attendance</span>
                        <span class="badge badge-success">
                            <i data-lucide="users" style="width:11px;height:11px;"></i>
                            <span x-text="report.attendance"></span>
                        </span>
                    </div>

                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Date</span>
                        <span style="font-size:13px;color:var(--text-2);" x-text="formatDate(report.meeting_date)"></span>
                    </div>

                    <template x-if="report.testimony">
                        <div class="mobile-card-row" @click="openTestimony(report)" style="cursor:pointer;align-items:flex-start;">
                            <span class="mobile-card-label" style="flex-shrink:0;">Testimony</span>
                            <span style="font-size:13px;color:var(--amber-dk);font-weight:500;overflow:hidden;text-overflow:ellipsis;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;" x-text="report.testimony"></span>
                        </div>
                    </template>

                    <template x-if="report.prayer_link">
                        <div class="mobile-card-row">
                            <span class="mobile-card-label">Link</span>
                            <div style="display:flex;align-items:center;gap:6px;min-width:0;flex:1;">
                                <a :href="report.prayer_link" target="_blank" rel="noopener noreferrer"
                                   style="font-size:12px;color:var(--amber-dk);font-weight:500;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1;min-width:0;text-decoration:none;"
                                   x-text="report.prayer_link"></a>
                                <button class="btn-copy" :class="copiedId === report.id ? 'copied' : ''" @click="copyLink(report)">
                                    <template x-if="copiedId !== report.id">
                                        <i data-lucide="copy" style="width:13px;height:13px;"></i>
                                    </template>
                                    <template x-if="copiedId === report.id">
                                        <i data-lucide="check" style="width:13px;height:13px;"></i>
                                    </template>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            {{-- Mobile pagination --}}
            <div x-show="filteredReports.length > 0" style="display:flex;align-items:center;justify-content:space-between;padding:12px 4px;margin-top:4px;">
                <span style="font-size:12px;color:var(--text-3);"
                      x-text="`Page ${currentPage} of ${totalPages} · ${filteredReports.length} records`"></span>
                <div style="display:flex;gap:6px;">
                    <button @click="prevPage" :disabled="currentPage === 1" class="page-btn">
                        <i data-lucide="chevron-left" style="width:16px;height:16px;"></i>
                    </button>
                    <button @click="nextPage" :disabled="currentPage === totalPages" class="page-btn">
                        <i data-lucide="chevron-right" style="width:16px;height:16px;"></i>
                    </button>
                </div>
            </div>
        </div>

    </main>

    <script>
        function dashboard() {
            return {
                reports: [],
                loading: true,
                search: '',
                filterDate: '',
                currentPage: 1,
                pageSize: 8,
                toasts: [],
                copiedId: null,

                showDeleteModal: false,
                reportToDelete: null,
                showEditModal: false,
                showTestimonyModal: false,
                selectedTestimony: null,
                editForm: { id: null, church: '', group: '', meeting_date: '', prayer_link: '', attendance: 0, testimony: '' },

                async init() {
                    await this.fetchReports();
                    this.refreshIcons();
                },

                refreshIcons() {
                    setTimeout(() => lucide.createIcons(), 50);
                },

                formatDate(dateString) {
                    if (!dateString || dateString === '0000-00-00') return 'N/A';
                    try {
                        const date = new Date(dateString);
                        if (isNaN(date.getTime())) return 'N/A';
                        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', timeZone: 'UTC' });
                    } catch (e) { return 'N/A'; }
                },

                addToast(message, type = 'success') {
                    const id = Date.now();
                    this.toasts.push({ id, message, type });
                    this.refreshIcons();
                    setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, 4000);
                },

                openTestimony(report) {
                    if (!report.testimony) return;
                    this.selectedTestimony = report;
                    this.showTestimonyModal = true;
                    this.refreshIcons();
                },

                async copyLink(report) {
                    if (!report.prayer_link) return;
                    try {
                        await navigator.clipboard.writeText(report.prayer_link);
                        this.copiedId = report.id;
                        this.refreshIcons();
                        setTimeout(() => { this.copiedId = null; this.refreshIcons(); }, 2000);
                    } catch (e) {
                        this.addToast('Could not copy link', 'error');
                    }
                },

                async fetchReports(manual = false) {
                    this.loading = true;
                    try {
                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const response = await fetch('/api-v1/praise-reports', {
                            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token }
                        });
                        if (!response.ok) throw new Error(`HTTP ${response.status}`);
                        const result = await response.json();
                        this.reports = result.data ? result.data : result;
                        if (manual) this.addToast('Records refreshed successfully');
                    } catch (e) {
                        this.addToast('Connection failed. Please retry.', 'error');
                    }
                    this.loading = false;
                    this.refreshIcons();
                },

                triggerEdit(report) {
                    this.editForm = { ...report };
                    if (this.editForm.meeting_date) {
                        try { this.editForm.meeting_date = new Date(this.editForm.meeting_date).toISOString().split('T')[0]; }
                        catch(e) { this.editForm.meeting_date = ''; }
                    }
                    this.showEditModal = true;
                    this.refreshIcons();
                },

                async confirmUpdate() {
                    this.loading = true;
                    try {
                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const response = await fetch(`/api-v1/praise-reports/${this.editForm.id}`, {
                            method: 'PUT',
                            headers: { 'X-CSRF-TOKEN': token, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify(this.editForm)
                        });
                        if (response.ok) {
                            const updatedData = await response.json();
                            const index = this.reports.findIndex(r => r.id === this.editForm.id);
                            if (index !== -1) this.reports[index] = updatedData.data || updatedData;
                            this.addToast('Report updated successfully');
                            this.showEditModal = false;
                        } else throw new Error();
                    } catch (e) {
                        this.addToast('Failed to update record', 'error');
                    } finally {
                        this.loading = false;
                        this.refreshIcons();
                    }
                },

                triggerDelete(report) {
                    this.reportToDelete = report;
                    this.showDeleteModal = true;
                    this.refreshIcons();
                },

                async confirmDelete() {
                    if (!this.reportToDelete) return;
                    const id = this.reportToDelete.id;
                    this.showDeleteModal = false;
                    try {
                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const response = await fetch(`/api-v1/praise-reports/${id}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                        });
                        if (response.ok) {
                            this.reports = this.reports.filter(r => r.id !== id);
                            this.addToast('Entry removed permanently');
                            if (this.paginatedReports.length === 0 && this.currentPage > 1) this.currentPage--;
                        } else throw new Error();
                    } catch (e) {
                        this.addToast('Unable to delete record', 'error');
                    } finally {
                        this.reportToDelete = null;
                        this.refreshIcons();
                    }
                },

                resetFilters() {
                    this.search = '';
                    this.filterDate = '';
                    this.currentPage = 1;
                    this.refreshIcons();
                },

                /* ── Excel Export ── */
                exportToExcel() {
                    const data = this.filteredReports.map(r => ({
                        'Church':        r.church         || '',
                        'Group':         r.group          || '',
                        'Meeting Date':  this.formatDate(r.meeting_date),
                        'Attendance':    parseInt(r.attendance) || 0,
                        'Testimony':     r.testimony      || '',
                        'Report Link':   r.prayer_link    || '',
                    }));

                    if (data.length === 0) {
                        this.addToast('No records to export', 'error');
                        return;
                    }

                    const ws = XLSX.utils.json_to_sheet(data);

                    /* Column widths */
                    ws['!cols'] = [
                        { wch: 30 }, // Church
                        { wch: 22 }, // Group
                        { wch: 16 }, // Meeting Date
                        { wch: 12 }, // Attendance
                        { wch: 50 }, // Testimony
                        { wch: 40 }, // Report Link
                    ];

                    const wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, 'Prayer Reports');

                    const now    = new Date();
                    const stamp  = `${now.getFullYear()}-${String(now.getMonth()+1).padStart(2,'0')}-${String(now.getDate()).padStart(2,'0')}`;
                    const suffix = this.filterDate ? `_${this.filterDate}` : (this.search ? `_filtered` : '');

                    XLSX.writeFile(wb, `prayer-reports${suffix}_${stamp}.xlsx`);
                    this.addToast(`Exported ${data.length} record${data.length !== 1 ? 's' : ''} to Excel`);
                },

                get filteredReports() {
                    return this.reports.filter(r => {
                        const matchesSearch = r.church.toLowerCase().includes(this.search.toLowerCase()) ||
                                              r.group.toLowerCase().includes(this.search.toLowerCase());
                        let matchesDate = true;
                        if (this.filterDate) {
                            const reportDate = new Date(r.meeting_date).toDateString();
                            const filterDate  = new Date(this.filterDate).toDateString();
                            matchesDate = reportDate === filterDate;
                        }
                        return matchesSearch && matchesDate;
                    });
                },

                get paginatedReports() {
                    const start = (this.currentPage - 1) * this.pageSize;
                    return this.filteredReports.slice(start, start + this.pageSize);
                },

                get totalPages() {
                    return Math.ceil(this.filteredReports.length / this.pageSize) || 1;
                },

                nextPage() { if (this.currentPage < this.totalPages) { this.currentPage++; this.refreshIcons(); } },
                prevPage() { if (this.currentPage > 1)              { this.currentPage--; this.refreshIcons(); } },

                get stats() {
                    const totalAttendance = this.reports.reduce((sum, r) => sum + (parseInt(r.attendance) || 0), 0);
                    const groups = [...new Set(this.reports.map(r => r.group))].length;
                    return [
                        { label: 'Total Reports',    value: this.reports.length,              icon: 'file-text'   },
                        { label: 'Reporting Groups', value: groups,                           icon: 'users'       },
                        { label: 'Total Attendance', value: totalAttendance.toLocaleString(), icon: 'trending-up' }
                    ];
                },

                get currentWeekDates() {
                    const now  = new Date();
                    const dow  = now.getDay();
                    const diffToMon = (dow === 0) ? -6 : 1 - dow;
                    const mon  = new Date(now);
                    mon.setDate(now.getDate() + diffToMon);
                    mon.setHours(0, 0, 0, 0);
                    return Array.from({ length: 5 }, (_, i) => {
                        const d = new Date(mon);
                        d.setDate(mon.getDate() + i);
                        return d;
                    });
                },

                get weekLabel() {
                    const days = this.currentWeekDates;
                    const fmt  = d => d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                    return `Week of ${fmt(days[0])} \u2013 ${fmt(days[4])}`;
                },

                get weeklyLeaders() {
                    const dayNames   = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                    const shortNames = ['MON', 'TUE', 'WED', 'THU', 'FRI'];
                    const weekDates  = this.currentWeekDates;
                    const todayStr   = new Date().toDateString();
                    let globalMax    = 0;

                    const dayData = weekDates.map((date, i) => {
                        const dateStr = date.toDateString();
                        const dayReports = this.reports.filter(r => {
                            if (!r.meeting_date) return false;
                            return new Date(r.meeting_date + 'T00:00:00').toDateString() === dateStr;
                        });

                        const byGroup = {};
                        dayReports.forEach(r => {
                            const att = parseInt(r.attendance) || 0;
                            if (!byGroup[r.group]) {
                                byGroup[r.group] = { group: r.group, church: r.church, attendance: 0 };
                            }
                            byGroup[r.group].attendance += att;
                        });

                        const sorted = Object.values(byGroup).sort((a, b) => b.attendance - a.attendance);
                        const winner = sorted[0] || null;
                        if (winner && winner.attendance > globalMax) globalMax = winner.attendance;

                        return {
                            name: dayNames[i], shortName: shortNames[i],
                            isToday: dateStr === todayStr, winner,
                            _raw: winner ? winner.attendance : 0,
                        };
                    });

                    return dayData.map(d => ({
                        ...d,
                        pct: globalMax > 0 ? Math.round((d._raw / globalMax) * 100) : 0,
                    }));
                }
            }
        }
    </script>
</body>
</html>
