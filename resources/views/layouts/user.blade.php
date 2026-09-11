<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} | Highclass Inc.</title>
    <style>
        :root{--ink:#172033;--muted:#667085;--line:#e4e7ec;--brand:#243f8f;--brand-2:#2f71d0;--accent:#c0212d;--paper:#f7f5ef;--shadow:0 18px 48px rgba(24,36,64,.11)}
        *{box-sizing:border-box}body{margin:0;font-family:'Yu Gothic','Meiryo',system-ui,sans-serif;color:var(--ink);background:linear-gradient(180deg,#fbfaf6 0%,#f1f6fa 100%);line-height:1.7}a{color:var(--brand);text-decoration:none}a:hover{text-decoration:underline}
        .topbar{position:sticky;top:0;z-index:20;background:rgba(255,255,255,.92);backdrop-filter:blur(18px);border-bottom:1px solid rgba(36,63,143,.12)}.topbar-inner{max-width:1240px;margin:0 auto;min-height:82px;padding:12px 28px;display:flex;align-items:center;justify-content:space-between;gap:24px}.brand-logo{display:flex;align-items:center}.brand-logo img{width:260px;max-width:42vw;height:auto}.nav{display:flex;gap:8px;align-items:center;flex-wrap:wrap}.nav a:not(.btn){padding:10px 12px;border-radius:999px;color:#344054;font-weight:700}.nav a:not(.btn):hover{background:#eef4ff;text-decoration:none}.btn{display:inline-flex;align-items:center;justify-content:center;border:1px solid var(--brand);background:var(--brand);color:#fff;border-radius:999px;padding:10px 18px;font-weight:800;cursor:pointer;box-shadow:0 8px 18px rgba(36,63,143,.18)}.btn:hover{text-decoration:none}.btn.secondary{background:#fff;color:var(--brand);box-shadow:none}.btn.dark{background:#151b2d;border-color:#151b2d}.container{max-width:1240px;margin:0 auto;padding:42px 28px}.hero{display:grid;grid-template-columns:minmax(320px,430px) 1fr;gap:48px;align-items:center;min-height:640px}.eyebrow{display:inline-flex;color:var(--accent);font-weight:900;font-size:13px;letter-spacing:.08em}.headline{font-size:38px;font-weight:900;line-height:1.25;margin:18px 0}.headline strong{display:block;font-size:58px;color:var(--brand);font-family:Georgia,'Times New Roman',serif}.lead{font-size:17px;color:#475467}.search-panel,.form{background:rgba(255,255,255,.92);border:1px solid var(--line);border-radius:8px;padding:22px;box-shadow:var(--shadow)}.search-row{display:flex;gap:12px;margin-top:22px}.field{width:100%;border:1px solid #cbd5e1;border-radius:8px;background:#fff;padding:13px 14px;font-size:15px}.field:focus{outline:3px solid rgba(47,113,208,.18);border-color:var(--brand-2)}
        .map-stage{position:relative;background:linear-gradient(135deg,#fff 0%,#edf5fb 100%);border:1px solid rgba(36,63,143,.14);border-radius:8px;padding:28px;box-shadow:var(--shadow);overflow:hidden}.map-stage:before{content:"";position:absolute;inset:0;background-image:linear-gradient(90deg,rgba(36,63,143,.05) 1px,transparent 1px),linear-gradient(rgba(36,63,143,.05) 1px,transparent 1px);background-size:24px 24px;pointer-events:none}.map-title{position:relative;display:flex;gap:14px;align-items:center;margin-bottom:22px}.pin{border:2px solid var(--brand);background:#fff;border-radius:50%;width:54px;height:54px;display:grid;place-items:center;font-weight:900;color:var(--accent);box-shadow:0 10px 24px rgba(36,63,143,.15)}
        .japan-map-visual{position:relative;display:grid;grid-template-columns:repeat(18,minmax(26px,1fr));grid-template-rows:repeat(12,38px);gap:5px;min-height:500px;padding:10px 0 0}.map-pref{display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;border:1px solid rgba(36,63,143,.2);border-radius:6px;color:#172033;font-size:12px;font-weight:900;background:linear-gradient(135deg,#fff,#fff8f0);box-shadow:0 7px 18px rgba(24,36,64,.08);transition:transform .16s ease,box-shadow .16s ease,background .16s ease}.map-pref strong{font-size:10px;color:#475467;line-height:1.2}.map-pref:hover{transform:translateY(-2px);text-decoration:none;box-shadow:0 14px 28px rgba(24,36,64,.16);z-index:2}.map-pref.hot{background:linear-gradient(135deg,#fff1f1,#ff9a94);border-color:#ee8f8b}.map-pref.cool{background:linear-gradient(135deg,#f7fbff,#cde8f4);border-color:#a9ccdc}.map-pref:nth-child(1){grid-column:16/span 2;grid-row:1/span 2}.map-pref:nth-child(2){grid-column:15;grid-row:3}.map-pref:nth-child(3){grid-column:16;grid-row:4}.map-pref:nth-child(4){grid-column:16;grid-row:5}.map-pref:nth-child(5){grid-column:15;grid-row:4}.map-pref:nth-child(6){grid-column:15;grid-row:5}.map-pref:nth-child(7){grid-column:15;grid-row:6}.map-pref:nth-child(8){grid-column:17;grid-row:7}.map-pref:nth-child(9){grid-column:16;grid-row:7}.map-pref:nth-child(10){grid-column:15;grid-row:7}.map-pref:nth-child(11){grid-column:16;grid-row:8}.map-pref:nth-child(12){grid-column:17;grid-row:9}.map-pref:nth-child(13){grid-column:16;grid-row:9}.map-pref:nth-child(14){grid-column:15;grid-row:10}.map-pref:nth-child(15){grid-column:14;grid-row:6}.map-pref:nth-child(16){grid-column:12;grid-row:7}.map-pref:nth-child(17){grid-column:11;grid-row:7}.map-pref:nth-child(18){grid-column:11;grid-row:8}.map-pref:nth-child(19){grid-column:14;grid-row:9}.map-pref:nth-child(20){grid-column:14;grid-row:7/span 2}.map-pref:nth-child(21){grid-column:13;grid-row:8}.map-pref:nth-child(22){grid-column:14;grid-row:10}.map-pref:nth-child(23){grid-column:13;grid-row:10}.map-pref:nth-child(24){grid-column:13;grid-row:11}.map-pref:nth-child(25){grid-column:12;grid-row:8}.map-pref:nth-child(26){grid-column:11;grid-row:9}.map-pref:nth-child(27){grid-column:11;grid-row:10}.map-pref:nth-child(28){grid-column:10;grid-row:9}.map-pref:nth-child(29){grid-column:12;grid-row:10}.map-pref:nth-child(30){grid-column:11/span 2;grid-row:11}.map-pref:nth-child(31){grid-column:9;grid-row:8}.map-pref:nth-child(32){grid-column:8;grid-row:8}.map-pref:nth-child(33){grid-column:9;grid-row:9}.map-pref:nth-child(34){grid-column:8;grid-row:9}.map-pref:nth-child(35){grid-column:7;grid-row:9}.map-pref:nth-child(36){grid-column:10;grid-row:11}.map-pref:nth-child(37){grid-column:9;grid-row:11}.map-pref:nth-child(38){grid-column:8;grid-row:11}.map-pref:nth-child(39){grid-column:9;grid-row:12}.map-pref:nth-child(40){grid-column:5;grid-row:9}.map-pref:nth-child(41){grid-column:4;grid-row:9}.map-pref:nth-child(42){grid-column:4;grid-row:10}.map-pref:nth-child(43){grid-column:5;grid-row:10}.map-pref:nth-child(44){grid-column:6;grid-row:10}.map-pref:nth-child(45){grid-column:6;grid-row:11}.map-pref:nth-child(46){grid-column:5/span 2;grid-row:12}.map-pref:nth-child(47){grid-column:2;grid-row:12}
        .cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:18px}.card{background:#fff;border:1px solid var(--line);border-radius:8px;padding:22px;box-shadow:0 10px 28px rgba(24,36,64,.07)}.list{background:#fff;border:1px solid var(--line);border-radius:8px;overflow:hidden;box-shadow:0 10px 28px rgba(24,36,64,.06)}.list-item{padding:18px;border-bottom:1px solid var(--line)}.list-item:last-child{border-bottom:0}.meta{color:var(--muted);font-size:14px}.badge{display:inline-block;background:#eef4ff;border-radius:999px;padding:3px 10px;font-size:12px;margin-right:5px;color:var(--brand);font-weight:800}.badge.status{background:#ecfdf3;color:#067647}.grid2{display:grid;grid-template-columns:1fr 1fr;gap:14px}.label{font-weight:800}.textarea{min-height:120px}.profile-head{background:linear-gradient(135deg,#172554,#2f71d0);color:#fff;padding:30px;border-radius:8px;display:flex;gap:24px;align-items:center;box-shadow:var(--shadow)}.avatar{width:120px;height:140px;background:#e7ecef;border:3px solid #fff;display:grid;place-items:center;color:#64748b}.tabs{display:flex;gap:1px;flex-wrap:wrap;margin-top:18px}.tabs span{background:#fff;padding:10px 18px;border-bottom:3px solid var(--brand-2)}.alert{background:#eef9ee;border:1px solid #b8d9b8;padding:12px 16px;border-radius:8px;margin-bottom:18px}.footer{border-top:1px solid var(--line);background:#fff}.footer-inner{max-width:1240px;margin:0 auto;padding:34px 28px;display:grid;grid-template-columns:1.2fr 1fr;gap:28px;color:#475467}.company-name{font-weight:900;color:var(--brand);font-size:18px}
        @media(max-width:980px){.topbar-inner{align-items:flex-start;flex-direction:column}.hero,.grid2,.footer-inner{grid-template-columns:1fr}.headline strong{font-size:44px}.brand-logo img{max-width:78vw}.search-row{flex-direction:column}.japan-map-visual{grid-template-columns:repeat(10,minmax(44px,1fr));grid-template-rows:repeat(16,40px);min-height:650px}.map-pref:nth-child(n){grid-column:auto;grid-row:auto}}@media(max-width:640px){.container{padding:26px 16px}.topbar-inner{padding:12px 16px}.japan-map-visual{grid-template-columns:repeat(4,1fr);grid-template-rows:auto;min-height:auto}.map-pref{min-height:56px}.headline{font-size:30px}.headline strong{font-size:38px}.nav{width:100%}.nav a,.nav form,.nav button{width:100%}.btn{width:100%}}
    </style>
    <link rel="stylesheet" href="{{ asset('css/user-luxury.css') }}">
</head>
<body>
<header class="topbar">
    <div class="header-line"></div>
    <div class="topbar-inner">
        <div class="brand-cluster">
            <a class="brand-logo" href="{{ route('home') }}"><img src="{{ asset('images/logo.png') }}" alt="Highclass Inc."></a>
            <div class="brand-service"><strong>HIGH CLASS MATCHING</strong><span>全国部活指導者マップ</span></div>
        </div>
        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-navigation" aria-label="メニューを開く"><span></span><span></span><span></span><small>MENU</small></button>
        <nav class="nav" id="primary-navigation">
            <a @class(['is-current' => request()->routeIs('coaches.*')]) href="{{ route('coaches.index') }}"><small>COACH</small><strong>指導者を探す</strong></a>
            <a @class(['is-current' => request()->routeIs('jobs.*')]) href="{{ route('jobs.index') }}"><small>OPPORTUNITY</small><strong>案件を探す</strong></a>
            <a @class(['is-current' => request()->routeIs('organizations.*')]) href="{{ route('organizations.index') }}"><small>COMMUNITY</small><strong>学校・団体</strong></a>
            <a @class(['is-current' => request()->routeIs('articles.*')]) href="{{ route('articles.index') }}"><small>JOURNAL</small><strong>記事を読む</strong></a>
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}"><small>ADMIN</small><strong>管理画面</strong></a>
                @else
                    <a href="{{ route('offers.index') }}"><small>OFFER</small><strong>オファー</strong></a>
                    <a href="{{ route('notifications.index') }}"><small>ACTIVITY</small><strong>通知 @if(auth()->user()->unreadNotifications()->count())<sup>{{ auth()->user()->unreadNotifications()->count() }}</sup>@endif</strong></a>
                @endif
                <a class="nav-account" href="{{ route('dashboard') }}"><small>MY PAGE</small><strong>マイページ</strong></a>
                <form method="post" action="{{ route('logout') }}">@csrf <button class="nav-logout" type="submit">ログアウト</button></form>
            @else
                <a class="nav-login" href="{{ route('login') }}"><small>MEMBER</small><strong>ログイン</strong></a>
                <a class="nav-register" href="{{ route('register') }}"><small>JOIN US</small><strong>無料会員登録</strong></a>
            @endauth
        </nav>
    </div>
</header>
<main><div class="container">@if(session('status'))<div class="alert">{{ session('status') }}</div>@endif @yield('content')</div></main>
<footer class="footer">
    <section class="footer-cta">
        <div><span>START MATCHING</span><h2>スポーツの未来を、<br>地域から変えていく。</h2></div>
        <div class="footer-cta-actions"><a class="btn footer-join" href="{{ route('register') }}">無料会員登録 <span>→</span></a><a href="{{ route('coaches.index') }}">指導者を探す</a><a href="{{ route('jobs.index') }}">案件を探す</a></div>
    </section>
    <div class="footer-main">
        <div class="footer-brand"><strong>HIGH CLASS<br>MATCHING</strong><p>全国部活指導者マップ</p><img src="{{ asset('images/logo.png') }}" alt="株式会社ハイクラス"></div>
        <div class="footer-nav"><div><span>DISCOVER</span><a href="{{ route('coaches.index') }}">指導者を探す</a><a href="{{ route('jobs.index') }}">案件を探す</a><a href="{{ route('organizations.index') }}">学校・団体を探す</a></div><div><span>SUPPORT</span><a href="{{ route('articles.index') }}">記事・インタビュー</a>@auth<a href="{{ route('inquiries.index') }}">お問い合わせ</a><a href="{{ route('dashboard') }}">マイページ</a>@else<a href="{{ route('login') }}">ログイン</a><a href="{{ route('register') }}">新規会員登録</a>@endauth</div></div>
        <div class="footer-company">
            <span>COMPANY</span>
            <strong>株式会社ハイクラス</strong>
            <p>代表取締役 位髙 駿夫<br>〒150-0002 東京都渋谷区渋谷1-20-26</p>
            <p><a href="tel:0368224541">03-6822-4541</a><br>mail [@] highclass-inc.com</p>
            <p>特定保健指導機関 1321800128</p>
            <nav class="footer-social" aria-label="公式サイト・SNS">
                <span>FOLLOW / CONNECT</span>
                @foreach(config('matching.social_links', []) as $social)
                    @if($social['url'])
                        <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer">
                            <small>{{ $social['code'] }}</small>
                            <strong>{{ $social['label'] }}</strong>
                            <i aria-hidden="true">↗</i>
                        </a>
                    @endif
                @endforeach
            </nav>
        </div>
    </div>
    <div class="footer-bottom"><span>© {{ date('Y') }} Highclass Inc.</span><span>HEALTH × SPORTS × COMMUNITY</span><a href="#">PAGE TOP ↑</a></div>
</footer>
<script>
var navToggle = document.querySelector('.nav-toggle');
var primaryNavigation = document.getElementById('primary-navigation');
if (navToggle && primaryNavigation) {
    navToggle.addEventListener('click', function () {
        var isOpen = primaryNavigation.classList.toggle('is-open');
        document.body.classList.toggle('nav-open', isOpen);
        navToggle.setAttribute('aria-expanded', String(isOpen));
        navToggle.setAttribute('aria-label', isOpen ? 'メニューを閉じる' : 'メニューを開く');
    });
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && primaryNavigation.classList.contains('is-open')) navToggle.click();
    });
    primaryNavigation.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () {
            if (primaryNavigation.classList.contains('is-open')) navToggle.click();
        });
    });
}
document.querySelectorAll('.cards,.profile-head').forEach(function (element) {
    element.classList.add('reveal');
});
if ('IntersectionObserver' in window) {
    var revealObserver = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                revealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: .08 });
    document.querySelectorAll('.reveal').forEach(function (element) { revealObserver.observe(element); });
} else {
    document.querySelectorAll('.reveal').forEach(function (element) { element.classList.add('is-visible'); });
}
</script>
</body>
</html>
