<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin | {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root{--line:rgba(255,255,255,.1);--text:#eef4ff;--muted:#9ca3af}body{min-height:100vh;background:radial-gradient(circle at top right,rgba(74,144,226,.22),transparent 32%),linear-gradient(180deg,#080d18,#0b1020);color:var(--text);font-family:'Yu Gothic','Meiryo',system-ui,sans-serif}.admin-shell{display:grid;grid-template-columns:280px 1fr;min-height:100vh}.sidebar{border-right:1px solid var(--line);background:rgba(8,13,24,.78);backdrop-filter:blur(18px);padding:26px;position:sticky;top:0;height:100vh}.sidebar img{width:210px;max-width:100%;background:#fff;border-radius:8px;padding:8px}.nav-pills .nav-link{color:#cbd5e1;border-radius:8px;font-weight:700}.nav-pills .nav-link.active,.nav-pills .nav-link:hover{background:rgba(110,168,254,.16);color:#fff}.admin-main{padding:32px}.admin-top{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:28px}.glass-card{background:linear-gradient(180deg,rgba(23,32,51,.92),rgba(17,24,39,.92));border:1px solid var(--line);border-radius:8px;box-shadow:0 22px 60px rgba(0,0,0,.28)}.metric{padding:22px}.metric-label{color:var(--muted);font-size:13px;font-weight:800}.metric-value{font-size:34px;font-weight:900;letter-spacing:0}.table-darkish{--bs-table-bg:transparent;--bs-table-color:#eef4ff;--bs-table-border-color:rgba(255,255,255,.1)}.list-group-item{background:transparent;color:var(--text);border-color:var(--line)}.btn-primary{background:#3b82f6;border-color:#3b82f6}.btn-outline-light{border-color:rgba(255,255,255,.22)}.badge-soft{background:rgba(110,168,254,.16);color:#bcd7ff;border:1px solid rgba(110,168,254,.22)}.form-control,.form-select,.btn{border-radius:8px}.alert{border-radius:8px}.pagination{--bs-pagination-bg:#111827;--bs-pagination-border-color:rgba(255,255,255,.12);--bs-pagination-color:#dbeafe;--bs-pagination-active-bg:#3b82f6;--bs-pagination-active-border-color:#3b82f6}@media(max-width:900px){.admin-shell{grid-template-columns:1fr}.sidebar{position:relative;height:auto}.admin-main{padding:22px}}
    </style>
    <style>
        a, a:hover, a:focus, a:active { text-decoration: none; }
    </style>
</head>
<body>
<div class="admin-shell">
    <aside class="sidebar">
        <img src="{{ asset('images/logo.png') }}" alt="Highclass Inc.">
        <div class="mt-4 small text-secondary">Matching System Admin</div>
        <nav class="nav nav-pills flex-column gap-2 mt-4">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">Users</a>
            <a class="nav-link {{ request()->routeIs('admin.coaches.*') ? 'active' : '' }}" href="{{ route('admin.coaches.index') }}">Coaches</a>
            <a class="nav-link {{ request()->routeIs('admin.organizations.*') ? 'active' : '' }}" href="{{ route('admin.organizations.index') }}">Organizations</a>
            <a class="nav-link {{ request()->routeIs('admin.jobs.*') ? 'active' : '' }}" href="{{ route('admin.jobs.index') }}">Jobs</a>
            <a class="nav-link {{ request()->routeIs('admin.applications.*') ? 'active' : '' }}" href="{{ route('admin.applications.index') }}">Applications</a>
            <a class="nav-link {{ request()->routeIs('admin.inquiries.*') ? 'active' : '' }}" href="{{ route('admin.inquiries.index') }}">Inquiries</a>
            <a class="nav-link {{ request()->routeIs('admin.offers.*') ? 'active' : '' }}" href="{{ route('admin.offers.index') }}">Offers</a>
            <a class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}" href="{{ route('admin.reviews.index') }}">Reviews</a>
            <a class="nav-link {{ request()->routeIs('admin.articles.*') ? 'active' : '' }}" href="{{ route('admin.articles.index') }}">Articles</a>
            <a class="nav-link {{ request()->routeIs('admin.masters.*') ? 'active' : '' }}" href="{{ route('admin.masters.index') }}">Master Data</a>
            <a class="nav-link" href="{{ route('home') }}">User Site</a>
        </nav>
    </aside>
    <main class="admin-main">
        <div class="admin-top">
            <div><div class="text-secondary fw-bold">&#26666;&#24335;&#20250;&#31038;&#12495;&#12452;&#12463;&#12521;&#12473;</div><h1 class="h3 m-0">@yield('title','&#31649;&#29702;&#32773;&#12480;&#12483;&#12471;&#12517;&#12508;&#12540;&#12489;')</h1></div>
            <form method="post" action="{{ route('logout') }}">@csrf <button class="btn btn-outline-light" type="submit">&#12525;&#12464;&#12450;&#12454;&#12488;</button></form>
        </div>
        @if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
        @yield('content')
    </main>
</div>
</body>
</html>
