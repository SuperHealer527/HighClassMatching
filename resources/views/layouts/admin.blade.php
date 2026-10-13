<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin | {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/admin-controls.css') }}" rel="stylesheet">
    <style>
        :root{--line:rgba(255,255,255,.11);--text:#f3f3f0;--muted:#929292;--accent:#fb5601}*{box-sizing:border-box}body{min-height:100vh;margin:0;background-color:#0b0b0c;background-image:linear-gradient(rgba(255,255,255,.018) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.018) 1px,transparent 1px);background-size:44px 44px;color:var(--text);font-family:'Yu Gothic','Meiryo',system-ui,sans-serif}.admin-shell{display:grid;grid-template-columns:260px minmax(0,1fr);min-height:100vh}.sidebar{position:sticky;top:0;height:100vh;overflow-y:auto;padding:26px 20px;border-right:1px solid var(--line);background:#090909}.nav-pills .nav-link{padding:10px 12px;border-left:2px solid transparent;border-radius:2px;color:#a8a8a8;font-size:13px;font-weight:700;transition:color .2s ease,background-color .2s ease,border-color .2s ease}.nav-pills .nav-link.active,.nav-pills .nav-link:hover{border-left-color:var(--accent);background:#171717;color:#fff}.admin-main{min-width:0;padding:clamp(22px,3vw,42px)}.admin-top{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:26px;padding-bottom:20px;border-bottom:1px solid var(--line)}.glass-card{border:1px solid var(--line);border-radius:4px;background:#151516;box-shadow:0 18px 48px rgba(0,0,0,.22)}.metric{padding:22px}.metric-label{color:var(--muted);font-size:12px;font-weight:800}.metric-value{margin-top:6px;color:#fff;font-size:34px;font-weight:900;letter-spacing:0}.table-darkish{--bs-table-bg:transparent;--bs-table-color:#eee;--bs-table-border-color:rgba(255,255,255,.1)}.list-group-item{background:transparent;color:var(--text);border-color:var(--line)}.btn{border-radius:3px}.btn-primary{border-color:var(--accent);background:var(--accent);color:#fff}.btn-primary:hover,.btn-primary:focus{border-color:#fff;background:#fff;color:#111}.btn-outline-light{border-color:rgba(255,255,255,.25)}.badge-soft{border:1px solid rgba(251,86,1,.28);background:rgba(251,86,1,.1);color:#ff9862}.form-control,.form-select{border-radius:3px}.alert{border-radius:3px}.pagination{--bs-pagination-bg:#151516;--bs-pagination-border-color:rgba(255,255,255,.12);--bs-pagination-color:#ddd;--bs-pagination-active-bg:#fb5601;--bs-pagination-active-border-color:#fb5601}@media(max-width:900px){.admin-shell{grid-template-columns:1fr}.sidebar{position:relative;height:auto;overflow-y:visible}.admin-main{padding:20px}.admin-top{align-items:flex-start}}
    </style>
    <style>
        a, a:hover, a:focus, a:active { text-decoration: none; }
    </style>
</head>
<body>
<div class="admin-shell">
    <button class="admin-nav-backdrop" type="button" aria-label="メニューを閉じる"></button>
    <aside class="sidebar">
        <a class="admin-brand" href="{{ route('admin.dashboard') }}"><strong>Back Athlete</strong><span>OPERATIONS CONSOLE</span></a>
        <nav class="nav nav-pills flex-column mt-3">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">ダッシュボード</a>
            <div class="admin-nav-label">REVIEW</div>
            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">会員・紹介者</a>
            <a class="nav-link {{ request()->routeIs('admin.coaches.*') ? 'active' : '' }}" href="{{ route('admin.coaches.index') }}">指導者審査</a>
            <a class="nav-link {{ request()->routeIs('admin.organizations.*') ? 'active' : '' }}" href="{{ route('admin.organizations.index') }}">チーム・部活審査</a>
            <a class="nav-link {{ request()->routeIs('admin.jobs.*') ? 'active' : '' }}" href="{{ route('admin.jobs.index') }}">案件審査</a>
            <div class="admin-nav-label">OPERATIONS</div>
            <a class="nav-link {{ request()->routeIs('admin.applications.*') ? 'active' : '' }}" href="{{ route('admin.applications.index') }}">応募管理</a>
            <a class="nav-link {{ request()->routeIs('admin.inquiries.*') ? 'active' : '' }}" href="{{ route('admin.inquiries.index') }}">問い合わせ</a>
            <a class="nav-link {{ request()->routeIs('admin.offers.*') ? 'active' : '' }}" href="{{ route('admin.offers.index') }}">オファー</a>
            <a class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}" href="{{ route('admin.reviews.index') }}">評価</a>
            <div class="admin-nav-label">CONTENT</div>
            <a class="nav-link {{ request()->routeIs('admin.articles.*') ? 'active' : '' }}" href="{{ route('admin.articles.index') }}">記事</a>
            <a class="nav-link {{ request()->routeIs('admin.project-members.*') ? 'active' : '' }}" href="{{ route('admin.project-members.index') }}">開発者・運営者</a>
            <a class="nav-link {{ request()->routeIs('admin.masters.*') ? 'active' : '' }}" href="{{ route('admin.masters.index') }}">マスターデータ</a>
            <div class="admin-nav-label">PREVIEW</div>
            <a class="nav-link" href="{{ route('home') }}">ユーザーサイトを見る</a>
        </nav>
    </aside>
    <main class="admin-main">
        <div class="admin-top">
            <div class="d-flex align-items-center gap-3"><button class="admin-menu-toggle" type="button" aria-label="管理メニューを開く" aria-expanded="false">☰</button><div><div class="admin-eyebrow">BACK ATHLETE / ADMIN</div><h1 class="h3 m-0 mt-1">@yield('title','管理者ダッシュボード')</h1></div></div>
            <form method="post" action="{{ route('logout') }}">@csrf <button class="btn btn-outline-light" type="submit">&#12525;&#12464;&#12450;&#12454;&#12488;</button></form>
        </div>
        @if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
        @if($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong>更新できませんでした。</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </main>
</div>
<script>
var adminMenuToggle = document.querySelector('.admin-menu-toggle');
var adminNavBackdrop = document.querySelector('.admin-nav-backdrop');
function closeAdminMenu() {
    document.body.classList.remove('admin-nav-open');
    if (adminMenuToggle) adminMenuToggle.setAttribute('aria-expanded', 'false');
}
if (adminMenuToggle) {
    adminMenuToggle.addEventListener('click', function () {
        var isOpen = document.body.classList.toggle('admin-nav-open');
        adminMenuToggle.setAttribute('aria-expanded', String(isOpen));
    });
}
if (adminNavBackdrop) adminNavBackdrop.addEventListener('click', closeAdminMenu);
document.addEventListener('keydown', function (event) { if (event.key === 'Escape') closeAdminMenu(); });
</script>
</body>
</html>
