@extends('layouts.user')
@section('content')
<section class="hero hero-slider" data-hero-slider>
    <div class="hero-slides" aria-hidden="true">
        <figure class="hero-slide is-active"><img src="{{ asset('images/sports-coaching-hero-color.png') }}" alt=""><figcaption><span>01</span>TEAM COACHING</figcaption></figure>
        <figure class="hero-slide"><img src="{{ asset('images/track-coaching.png') }}" alt=""><figcaption><span>02</span>PERFORMANCE</figcaption></figure>
        <figure class="hero-slide"><img src="{{ asset('images/school-team-practice-color.png') }}" alt=""><figcaption><span>03</span>COMMUNITY</figcaption></figure>
    </div>
    <div class="hero-copy"><div class="eyebrow">HIGH CLASS MATCHING</div><div class="headline">全国部活指導者マップ<strong>{{ number_format($coachTotal) }}名</strong></div><p class="lead">信頼できる専門家と、地域の学校・スポーツ団体をつなぐ。本人・資格確認を経た指導者を、地域と専門分野から探せます。</p><form class="search-row" method="get" action="{{ route('coaches.index') }}"><select class="field" name="prefecture"><option value="">都道府県</option>@foreach($prefectures as $pref)<option value="{{ $pref }}">{{ $pref }}</option>@endforeach</select><select class="field" name="field"><option value="">専門項目</option>@foreach(config('matching.fields') as $field)<option value="{{ $field }}">{{ $field }}</option>@endforeach</select><button class="btn" type="submit">指導者を検索</button></form></div>
    <div class="hero-slider-nav" aria-label="メインビジュアルを選択">@foreach(['COACH','FIELD','TEAM'] as $label)<button class="{{ $loop->first ? 'is-active' : '' }}" type="button" data-hero-slide="{{ $loop->index }}" aria-label="{{ $label }}の画像を表示" aria-pressed="{{ $loop->first ? 'true' : 'false' }}"><span>0{{ $loop->iteration }}</span><strong>{{ $label }}</strong><i></i></button>@endforeach</div>
    <div class="hero-index">TOKYO / JAPAN / 2026</div>
</section>
<script>
(function () {
    var slider = document.querySelector('[data-hero-slider]');
    if (!slider) return;
    var slides = Array.from(slider.querySelectorAll('.hero-slide'));
    var controls = Array.from(slider.querySelectorAll('[data-hero-slide]'));
    var current = 0;
    var timer;
    function show(index) {
        current = index;
        slides.forEach(function (slide, slideIndex) { slide.classList.toggle('is-active', slideIndex === index); });
        controls.forEach(function (control, controlIndex) {
            var active = controlIndex === index;
            control.classList.toggle('is-active', active);
            control.setAttribute('aria-pressed', String(active));
        });
    }
    function start() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        clearInterval(timer);
        timer = setInterval(function () { show((current + 1) % slides.length); }, 5200);
    }
    controls.forEach(function (control, index) { control.addEventListener('click', function () { show(index); start(); }); });
    slider.addEventListener('mouseenter', function () { clearInterval(timer); });
    slider.addEventListener('mouseleave', start);
    slider.addEventListener('focusin', function () { clearInterval(timer); });
    slider.addEventListener('focusout', start);
    start();
}());
</script>

<section class="trust-strip"><div><strong>{{ number_format($coachTotal) }}</strong><span>登録指導者</span></div><div><strong>{{ number_format($organizationTotal) }}</strong><span>学校・団体</span></div><div><strong>{{ number_format($jobTotal) }}</strong><span>公開案件</span></div><p>運営会社: 株式会社ハイクラス<br>特定保健指導機関 1321800128</p></section>

<div class="system-marquee" aria-hidden="true"><div>@for($repeat = 0; $repeat < 6; $repeat++)<div class="marquee-set"><span>HIGH CLASS MATCHING</span><i>SPORTS</i><span>COACH × COMMUNITY</span><i>47 PREFECTURES</i></div>@endfor</div></div>

<section class="map-stage" id="area-map">
    <header class="map-title">
        <div class="map-title-copy"><div class="pin" aria-hidden="true">◎</div><div><div class="eyebrow">SEARCH BY AREA</div><h2>日本の地域から探す</h2><p class="meta">都道府県を選択して、その地域を主な活動拠点とする指導者を検索できます。</p></div></div>
        <div class="map-overview"><div><strong>47</strong><span>PREFECTURES</span></div><div><strong>{{ number_format($coachTotal) }}</strong><span>COACHES</span></div></div>
    </header>
    <div class="map-interface">
        <aside class="map-region-nav" aria-label="地域を選択">
            <div><span>REGION INDEX</span><h3>地域を絞り込む</h3></div>
            <button class="map-region-button is-active" type="button" data-map-region="all" aria-pressed="true"><span>00</span><strong>全国</strong></button>
            @foreach($regions as $region)
                <button class="map-region-button" type="button" data-map-region="{{ $loop->iteration }}" aria-pressed="false"><span>0{{ $loop->iteration }}</span><strong>{{ $region['label'] }}</strong></button>
            @endforeach
            <p>各都道府県の数字は、主な活動拠点として登録されている公開中の指導者数です。</p>
        </aside>
        <div class="map-canvas">
            <div class="map-canvas-head"><span>JAPAN / INTERACTIVE DIRECTORY</span><strong>都道府県を選択</strong></div>
            <div class="japan-map-visual" aria-label="都道府県から指導者を探す">
                @foreach($prefectures as $pref)
                    @php
                        $count = (int) ($counts[$pref] ?? 0);
                        $regionNumber = $loop->index < 7 ? 1 : ($loop->index < 14 ? 2 : ($loop->index < 24 ? 3 : ($loop->index < 30 ? 4 : ($loop->index < 39 ? 5 : 6))));
                    @endphp
                    <a class="map-pref {{ $count >= 3 ? 'hot' : ($count > 0 ? 'cool' : '') }}" data-region="{{ $regionNumber }}" href="{{ route('coaches.index',['prefecture'=>$pref]) }}" aria-label="{{ $pref }}、対応指導者{{ $count }}名"><span>{{ $pref }}</span><strong>{{ $count }}名</strong></a>
                @endforeach
            </div>
        </div>
    </div>
</section>
<script>
document.querySelectorAll('[data-map-region]').forEach(function (button) {
    button.addEventListener('click', function () {
        var region = button.getAttribute('data-map-region');
        var stage = document.getElementById('area-map');
        stage.setAttribute('data-active-region', region);
        document.querySelectorAll('[data-map-region]').forEach(function (item) {
            var active = item === button;
            item.classList.toggle('is-active', active);
            item.setAttribute('aria-pressed', String(active));
        });
    });
});
</script>

<section class="service-intro"><div class="section-copy"><div class="eyebrow">WHY HIGH CLASS</div><h2>専門性を見える化し、<br>安心して出会える仕組みへ。</h2><p>競技経験だけでなく、トレーニング、メンタル、リハビリ、栄養、映像分析まで。学校や団体の課題に合う専門家を、確認済みプロフィールと実績から比較できます。</p><a href="{{ route('coaches.index') }}">指導者一覧を見る →</a></div><div class="benefit-list"><article><span>01</span><div><h3>本人・資格確認</h3><p>運営が本人確認書類と資格証明を審査。公開状態も継続的に管理します。</p></div></article><article><span>02</span><div><h3>条件一致スコア</h3><p>競技、専門分野、対応地域、対象年代を照合し、相性の良い候補を提案します。</p></div></article><article><span>03</span><div><h3>直接オファー</h3><p>学校・団体は気になる指導者を保存し、具体的な依頼内容を直接送れます。</p></div></article></div></section>

<section class="image-story"><img src="{{ asset('images/school-team-practice.png') }}" alt="部活動の指導風景"><div><div class="eyebrow">FOR ORGANIZATIONS</div><h2>指導者不足を、<br>地域の専門性で支える。</h2><p>募集案件への応募を待つだけでなく、条件に合う指導者へ直接オファーできます。候補保存、選考履歴、評価まで一つの画面で管理できます。</p><a class="btn" href="{{ route('register') }}">学校・団体として無料登録</a></div></section>

<section class="home-section"><div class="section-heading"><div><div class="eyebrow">FEATURED COACHES</div><h2>注目の指導者</h2></div><a href="{{ route('coaches.index') }}">すべて見る →</a></div><div class="result-grid coach-results">@foreach($featuredCoaches as $coach)@php($photo=$coach->photo_path?(str_starts_with($coach->photo_path,'images/')?asset($coach->photo_path):asset('storage/'.$coach->photo_path)):asset($loop->even?'images/coach-female-editorial.png':'images/sample-coach-profile.png'))<article class="result-card coach-card"><a class="result-image" href="{{ route('coaches.show',$coach) }}"><img src="{{ $photo }}" alt="{{ $coach->name }}"><span class="entity-image-label">COACH PROFILE</span></a><div class="result-card-body"><span><span class="badge status">確認済み</span><span class="badge">{{ $coach->main_prefecture }}</span></span><h3><a href="{{ route('coaches.show',$coach) }}">{{ $coach->name }}</a></h3><p>{{ implode(' / ',(array)$coach->fields) }}</p><p class="rating">★ {{ $coach->reviews_avg_rating ? number_format($coach->reviews_avg_rating,1) : 'NEW' }}</p><a class="entity-card-link" href="{{ route('coaches.show',$coach) }}">プロフィールを見る <span>→</span></a></div></article>@endforeach</div></section>

<section class="home-section contrast-band"><div class="section-heading"><div><div class="eyebrow">OPEN OPPORTUNITIES</div><h2>新着の指導案件</h2></div><a href="{{ route('jobs.index') }}">案件を探す →</a></div><div class="job-feature-grid">@foreach($featuredJobs as $job)@php($image=$job->image_path?(str_starts_with($job->image_path,'images/')?asset($job->image_path):asset('storage/'.$job->image_path)):asset($loop->even?'images/track-coaching.png':'images/school-team-practice.png'))<article><a href="{{ route('jobs.show',$job) }}"><img src="{{ $image }}" alt=""><div><span>{{ $job->prefecture }} / {{ $job->sport }}</span><h3>{{ $job->title }}</h3><p>{{ $job->organization->name }}</p></div></a></article>@endforeach</div></section>

<section class="process-section"><div class="section-copy"><div class="eyebrow">HOW IT WORKS</div><h2>登録から出会いまで、迷わない。</h2></div><ol><li><span>01</span><h3>プロフィール登録</h3><p>役割に合わせて専門性や団体情報を登録します。</p></li><li><span>02</span><h3>運営による確認</h3><p>本人・資格・公開情報をハイクラスが確認します。</p></li><li><span>03</span><h3>検索・推薦</h3><p>地域と条件から探し、自動推薦も活用できます。</p></li><li><span>04</span><h3>応募・オファー</h3><p>合意後に連絡先を開示し、外部メールで調整します。</p></li></ol></section>

<section class="home-section"><div class="section-heading"><div><div class="eyebrow">JOURNAL</div><h2>指導現場の知見</h2></div><a href="{{ route('articles.index') }}">記事一覧 →</a></div><div class="article-grid">@foreach($articles as $article)<article class="article-card"><a href="{{ route('articles.show',$article) }}"><img src="{{ asset($article->cover_image_path ?: 'images/sports-analysis-interview.png') }}" alt=""><div><span class="eyebrow">{{ strtoupper($article->category) }}</span><h3>{{ $article->title }}</h3><p>{{ $article->excerpt }}</p></div></a></article>@endforeach</div></section>

<section class="dual-cta"><article><div class="eyebrow">FOR COACHES</div><h2>指導の経験を、次の世代へ。</h2><p>プロフィールを公開し、全国の学校・団体とつながる。</p><a class="btn" href="{{ route('register') }}">指導者会員に登録</a></article><article><div class="eyebrow">FOR ORGANIZATIONS</div><h2>チームに必要な専門家を。</h2><p>募集掲載と直接オファーで、最適な指導者を探す。</p><a class="btn" href="{{ route('register') }}">学校・団体会員に登録</a></article></section>
@endsection
