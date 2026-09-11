<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <style>
        :root{--ink:#132014;--muted:#667085;--line:#d7ded3;--brand:#f26d55;--brand-dark:#d7523d;--blue:#cfe1ea;--peach:#f6c89b;--pink:#ff918d;--paper:#f7f8f5;--white:#fff}
        *{box-sizing:border-box} body{margin:0;font-family:Arial,'Yu Gothic','Meiryo',sans-serif;color:var(--ink);background:var(--paper);line-height:1.65} a{color:#007f8c;text-decoration:none} a:hover{text-decoration:underline}
        .topbar{height:76px;background:#fff;border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;padding:0 48px;gap:24px}.brand{font-weight:800;font-size:22px;color:#14220f}.brand small{display:block;font-size:11px;font-weight:700}.nav{display:flex;gap:24px;align-items:center}.btn{display:inline-flex;align-items:center;justify-content:center;border:1px solid var(--brand);background:var(--brand);color:#fff;border-radius:8px;padding:10px 18px;font-weight:700;cursor:pointer}.btn.secondary{background:#fff;color:var(--brand)}.btn.dark{background:#153d56;border-color:#153d56}.container{max-width:1180px;margin:0 auto;padding:38px 24px}.hero{display:grid;grid-template-columns:380px 1fr;gap:42px;align-items:center;min-height:560px}.headline{font-size:32px;font-weight:800}.headline strong{display:block;font-size:48px;color:#f25b4a;background:linear-gradient(transparent 58%,#d8f26d 58%)}.search-row{display:flex;gap:14px;margin-top:20px}.field{width:100%;border:1px solid #9da89b;border-radius:3px;background:#fff;padding:13px 14px;font-size:16px}.map-title{display:flex;gap:12px;align-items:center;margin-bottom:20px}.pin{border:3px solid #243312;border-radius:10px;width:48px;height:48px;display:grid;place-items:center;font-weight:900}.japan-map{display:grid;grid-template-columns:repeat(16,1fr);gap:5px;align-items:end}.pref{display:flex;min-height:54px;align-items:center;justify-content:center;text-align:center;border:1px solid #746d5e;border-radius:5px;color:#182313;font-size:13px;font-weight:700;background:var(--peach);padding:6px}.pref.hot{background:var(--pink)}.pref.cool{background:var(--blue)}.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:18px}.card{background:#fff;border:1px solid var(--line);border-radius:8px;padding:20px}.list{background:#fff;border:1px solid var(--line);border-radius:8px;overflow:hidden}.list-item{padding:18px;border-bottom:1px solid var(--line)}.list-item:last-child{border-bottom:0}.meta{color:var(--muted);font-size:14px}.badge{display:inline-block;background:#eef5f7;border-radius:999px;padding:2px 9px;font-size:12px;margin-right:5px}.badge.status{background:#dff7df;color:#216b21}.form{background:#fff;border:1px solid var(--line);border-radius:8px;padding:24px;display:grid;gap:14px}.grid2{display:grid;grid-template-columns:1fr 1fr;gap:14px}.label{font-weight:700}.textarea{min-height:120px}.profile-head{background:#173f78;color:#fff;padding:28px;display:flex;gap:24px;align-items:center}.avatar{width:120px;height:140px;background:#e7ecef;border:3px solid #fff;display:grid;place-items:center;color:#64748b}.tabs{display:flex;gap:1px;flex-wrap:wrap;background:#dbe2ea}.tabs span{background:#fff;padding:10px 18px;border-bottom:3px solid #2f80bd}.alert{background:#eef9ee;border:1px solid #b8d9b8;padding:12px 16px;border-radius:8px;margin-bottom:18px}.footer{padding:30px;text-align:center;color:var(--muted)}
        @media(max-width:860px){.topbar{padding:16px;height:auto;align-items:flex-start}.nav{flex-wrap:wrap;gap:10px}.hero,.grid2{grid-template-columns:1fr}.search-row{flex-direction:column}.japan-map{grid-template-columns:repeat(4,1fr)}.container{padding:24px 16px}.headline strong{font-size:38px}}
    </style>
</head>
<body>
<header class="topbar">
    <a class="brand" href="{{ route('home') }}"><small>一般社団法人 日本部活動指導研究協会</small>全国部活指導者マップ</a>
    <nav class="nav">
        <a href="{{ route('coaches.index') }}">指導者を探す</a>
        <a href="{{ route('jobs.index') }}">案件を探す</a>
        <a href="{{ route('organizations.index') }}">団体一覧</a>
        @auth
            <a href="{{ route('dashboard') }}">マイページ</a>
            <form method="post" action="{{ route('logout') }}">@csrf <button class="btn secondary" type="submit">ログアウト</button></form>
        @else
            <a class="btn secondary" href="{{ route('login') }}">ログイン</a>
            <a class="btn" href="{{ route('register') }}">新規会員登録</a>
        @endauth
    </nav>
</header>
<main>
    <div class="container">
        @if(session('status'))<div class="alert">{{ session('status') }}</div>@endif
        @yield('content')
    </div>
</main>
<footer class="footer">BA Matching MVP / Laravel + MySQL</footer>
</body>
</html>
