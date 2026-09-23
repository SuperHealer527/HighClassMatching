@extends('layouts.user')
@section('content')
<section class="hero hero-slider" data-hero-slider>
    <div class="hero-slides">
        <section class="hero-slide hero-slide-coach is-active" id="hero-panel-0" data-hero-panel="0" aria-hidden="false">
            <img class="hero-slide-photo" src="{{ asset('images/sports-coaching-hero-color.png') }}" alt="スポーツ指導者によるチーム指導">
            <div class="hero-mode-copy">
                <div class="eyebrow">01 / COACH DIRECTORY</div>
                <h1 class="headline">指導者・科学者を<br>地域から探す<strong>{{ number_format($coachTotal) }}名</strong></h1>
                <p class="lead">本人・資格確認を経た専門家を、<br>日本全国の活動地域から探せます。</p>
                <a class="hero-text-link" href="{{ route('coaches.index') }}">条件から指導者を探す <span>→</span></a>
            </div>
            <div class="hero-feature hero-map-feature">
                <header><span>JAPAN COACH MAP</span><strong>都道府県を選択</strong></header>
                <div class="japan-map-visual hero-japan-map" aria-label="日本地図から指導者を探す">
                    @foreach($prefectures as $pref)
                    @php
                    $count = (int) ($counts[$pref] ?? 0);
                    @endphp
                    <a class="map-pref {{ $count >= 3 ? 'hot' : ($count > 0 ? 'cool' : '') }}" href="{{ route('coaches.index', ['prefecture' => $pref]) }}" aria-label="{{ $pref }}の指導者{{ $count }}名"><span>{{ $pref }}</span><strong>{{ $count }}名</strong></a>
                    @endforeach
                </div>
            </div>
            <div class="hero-slide-caption"><span>01</span>COACH</div>
        </section>

        <section class="hero-slide hero-slide-field" id="hero-panel-1" data-hero-panel="1" aria-hidden="true" inert>
            <img class="hero-slide-photo" src="{{ asset('images/track-coaching.png') }}" alt="スポーツ現場での専門指導">
            <div class="hero-mode-copy">
                <div class="eyebrow">02 / OPEN OPPORTUNITIES</div>
                <h2 class="headline">専門性を活かせる<br>案件を探す<strong>{{ number_format($jobTotal) }}件</strong></h2>
                <p class="lead">競技、地域、キーワードから、公開中の指導案件を絞り込めます。</p>
            </div>
            <div class="hero-feature hero-search-feature">
                <header><span>FIELD SEARCH</span><strong>案件を検索</strong>
                    <p>希望する活動条件を選択してください。</p>
                </header>
                <form method="get" action="{{ route('jobs.index') }}">
                    <label><span>AREA</span><select class="field" name="prefecture">
                            <option value="">都道府県を選択</option>@foreach($prefectures as $pref)<option value="{{ $pref }}">{{ $pref }}</option>@endforeach
                        </select></label>
                    <label><span>SPORT</span><select class="field" name="sport">
                            <option value="">競技を選択</option>@foreach($sports as $sport)<option value="{{ $sport }}">{{ $sport }}</option>@endforeach
                        </select></label>
                    <label><span>KEYWORD</span><input class="field" type="search" name="keyword" placeholder="例：トレーニング、分析"></label>
                    <button class="btn" type="submit">案件を探す <span>→</span></button>
                </form>
            </div>
            <div class="hero-slide-caption"><span>02</span>FIELD</div>
        </section>

        <section class="hero-slide hero-slide-team" id="hero-panel-2" data-hero-panel="2" aria-hidden="true" inert>
            <img class="hero-slide-photo" src="{{ asset('images/school-team-practice-color.png') }}" alt="活動中のスポーツチームと部活動">
            <div class="hero-mode-copy">
                <div class="eyebrow">03 / SPORTS COMMUNITY</div>
                <h2 class="headline">地域で活動する<br>チーム・部活を探す<strong>{{ number_format($organizationTotal) }}団体</strong></h2>
                <p class="lead">活動地域や競技から、指導者を求めているチーム・部活を探せます。</p>
            </div>
            <div class="hero-feature hero-search-feature">
                <header><span>TEAM SEARCH</span><strong>チーム・部活を検索</strong>
                    <p>応援したい地域や競技から探してください。</p>
                </header>
                <form method="get" action="{{ route('organizations.index') }}">
                    <label><span>AREA</span><select class="field" name="prefecture">
                            <option value="">都道府県を選択</option>@foreach($prefectures as $pref)<option value="{{ $pref }}">{{ $pref }}</option>@endforeach
                        </select></label>
                    <label><span>SPORT</span><select class="field" name="sport">
                            <option value="">競技を選択</option>@foreach($sports as $sport)<option value="{{ $sport }}">{{ $sport }}</option>@endforeach
                        </select></label>
                    <label><span>KEYWORD</span><input class="field" type="search" name="keyword" placeholder="チーム名、地域名など"></label>
                    <button class="btn" type="submit">チーム・部活を探す <span>→</span></button>
                </form>
            </div>
            <div class="hero-slide-caption"><span>03</span>TEAM</div>
        </section>
    </div>
    <div class="hero-slider-nav" aria-label="検索モードを選択">@foreach(['COACH','FIELD','TEAM'] as $label)<button class="{{ $loop->first ? 'is-active' : '' }}" type="button" data-hero-slide="{{ $loop->index }}" aria-controls="hero-panel-{{ $loop->index }}" aria-label="{{ $label }}検索を表示" aria-pressed="{{ $loop->first ? 'true' : 'false' }}"><span>0{{ $loop->iteration }}</span><strong>{{ $label }}</strong><i></i></button>@endforeach</div>
    <div class="hero-index">KANAGAWA / JAPAN / 2026</div>
</section>
<script>
    (function() {
        var slider = document.querySelector('[data-hero-slider]');
        if (!slider) return;
        var slides = Array.from(slider.querySelectorAll('.hero-slide'));
        var controls = Array.from(slider.querySelectorAll('[data-hero-slide]'));
        var requestedMode = new URLSearchParams(window.location.search).get('hero');
        var modeIndexes = {
            coach: 0,
            field: 1,
            team: 2
        };
        var current = Object.prototype.hasOwnProperty.call(modeIndexes, requestedMode) ? modeIndexes[requestedMode] : 0;
        var timer;

        function show(index) {
            current = index;
            slides.forEach(function(slide, slideIndex) {
                var active = slideIndex === index;
                slide.classList.toggle('is-active', active);
                slide.setAttribute('aria-hidden', String(!active));
                slide.inert = !active;
            });
            controls.forEach(function(control, controlIndex) {
                var active = controlIndex === index;
                control.classList.toggle('is-active', active);
                control.setAttribute('aria-pressed', String(active));
            });
        }

        function start() {
            clearInterval(timer);
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            if (slider.matches(':hover') || slider.matches(':focus-within')) return;
            timer = setInterval(function() {
                show((current + 1) % slides.length);
            }, 5200);
        }
        controls.forEach(function(control, index) {
            control.addEventListener('click', function() {
                show(index);
                start();
            });
        });
        slider.addEventListener('mouseenter', function() {
            clearInterval(timer);
        });
        slider.addEventListener('mouseleave', start);
        slider.addEventListener('focusin', function() {
            clearInterval(timer);
        });
        slider.addEventListener('focusout', function() {
            window.setTimeout(start, 0);
        });
        show(current);
        start();
    }());
</script>

<section class="trust-strip">
    <div><strong>{{ number_format($coachTotal) }}</strong><span>登録指導者</span></div>
    <div><strong>{{ number_format($organizationTotal) }}</strong><span>チーム・部活</span></div>
    <div><strong>{{ number_format($jobTotal) }}</strong><span>公開案件</span></div>
    <p>運営：東海大学　上水研究室<br>協力：一般社団法人Back Athlete機構</p>
</section>

<div class="system-marquee" aria-hidden="true">
    <div>@for($repeat = 0; $repeat < 6; $repeat++)<div class="marquee-set"><span>Back Athlete Matching</span><i>SPORTS</i><span>COACH × COMMUNITY</span><i>47 PREFECTURES</i></div>@endfor
</div>
</div>

<!-- <section class="map-stage" id="area-map">
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
</section> -->
<script>
    document.querySelectorAll('[data-map-region]').forEach(function(button) {
        button.addEventListener('click', function() {
            var region = button.getAttribute('data-map-region');
            var stage = document.getElementById('area-map');
            stage.setAttribute('data-active-region', region);
            document.querySelectorAll('[data-map-region]').forEach(function(item) {
                var active = item === button;
                item.classList.toggle('is-active', active);
                item.setAttribute('aria-pressed', String(active));
            });
        });
    });
</script>

<section class="service-intro">
    <div class="section-copy">
        <div class="eyebrow">WHY HIGH CLASS</div>
        <h2>専門性を見える化し、<br>安心して出会える仕組みへ。</h2>
        <p>自称専門家を排除し、トレーニング、メンタル、リハビリ、栄養、映像分析まで、幅広い指導者のプロフィールと実績を見て、相談が可能です。</p><a href="{{ route('coaches.index') }}">指導者一覧を見る →</a>
    </div>
    <div class="benefit-list">
        <article><span>01</span>
            <div>
                <h3>リファーラル（紹介制）で実施</h3>
                <p>運営者や監修者、登録者の紹介で登録が可能なため、安心してご依頼が可能です。</p>
            </div>
        </article>
        <article><span>02</span>
            <div>
                <h3>本人や資格の確認まで対応</h3>
                <p>本人確認書類と資格書類も確認して、掲載しています。</p>
            </div>
        </article>
        <article><span>03</span>
            <div>
                <h3>直接オファーも可能</h3>
                <p>事務局に、高額な手数料を取られず依頼が可能です。</p>
            </div>
        </article>
    </div>
</section>

<section class="image-story"><img src="{{ asset('images/school-team-practice.png') }}" alt="部活動の指導風景">
    <div>
        <div class="eyebrow">FOR ORGANIZATIONS</div>
        <h2>困っている人材を探すには、チーム登録が必須！<br>さらに、募集をかけるには、案件の登録が可能</h2>
        <p>募集案件への応募を待つだけでなく、条件に合う指導者へ直接オファーできます。候補保存、選考履歴、評価まで一つの画面で管理できます。</p><a class="btn" href="{{ route('register') }}">チーム・部活として無料登録</a>
    </div>
</section>

<!-- <section class="home-section">
    <div class="section-heading">
        <div>
            <div class="eyebrow">FEATURED COACHES</div>
            <h2>注目の指導者</h2>
        </div><a href="{{ route('coaches.index') }}">すべて見る →</a>
    </div>
    <div class="result-grid coach-results">@foreach($featuredCoaches as $coach)@php($photo=$coach->photo_path?(str_starts_with($coach->photo_path,'images/')?asset($coach->photo_path):asset('storage/'.$coach->photo_path)):asset($loop->even?'images/coach-female-editorial.png':'images/sample-coach-profile.png'))<article class="result-card coach-card"><a class="result-image" href="{{ route('coaches.show',$coach) }}"><img src="{{ $photo }}" alt="{{ $coach->name }}"><span class="entity-image-label">COACH PROFILE</span></a>
            <div class="result-card-body"><span><span class="badge status">確認済み</span><span class="badge">{{ $coach->main_prefecture }}</span></span>
                <h3><a href="{{ route('coaches.show',$coach) }}">{{ $coach->name }}</a></h3>
                <p>{{ implode(' / ',(array)$coach->fields) }}</p>
                <p class="rating">★ {{ $coach->reviews_avg_rating ? number_format($coach->reviews_avg_rating,1) : 'NEW' }}</p><a class="entity-card-link" href="{{ route('coaches.show',$coach) }}">プロフィールを見る <span>→</span></a>
            </div>
        </article>@endforeach</div>
</section> -->

<section class="home-section contrast-band">
    <div class="section-heading">
        <div>
            <div class="eyebrow">OPEN OPPORTUNITIES</div>
            <h2>新着の指導案件</h2>
        </div><a href="{{ route('jobs.index') }}">案件を探す →</a>
    </div>
    <div class="job-feature-grid">@foreach($featuredJobs as $job)@php($image=$job->image_path?(str_starts_with($job->image_path,'images/')?asset($job->image_path):asset('storage/'.$job->image_path)):asset($loop->even?'images/track-coaching.png':'images/school-team-practice.png'))<article><a href="{{ route('jobs.show',$job) }}"><img src="{{ $image }}" alt="">
                <div><span>{{ $job->prefecture }} / {{ $job->sport }}</span>
                    <h3>{{ $job->title }}</h3>
                    <p>{{ $job->organization->name }}</p>
                </div>
            </a></article>@endforeach</div>
</section>

<!-- <section class="process-section">
    <div class="section-copy">
        <div class="eyebrow">HOW IT WORKS</div>
        <h2>登録から出会いまで、迷わない。</h2>
    </div>
    <ol>
        <li><span>01</span>
            <h3>プロフィール登録</h3>
            <p>役割に合わせて専門性や団体情報を登録します。</p>
        </li>
        <li><span>02</span>
            <h3>運営による確認</h3>
            <p>本人・資格・公開情報をハイクラスが確認します。</p>
        </li>
        <li><span>03</span>
            <h3>検索・推薦</h3>
            <p>地域と条件から探し、自動推薦も活用できます。</p>
        </li>
        <li><span>04</span>
            <h3>応募・オファー</h3>
            <p>合意後に連絡先を開示し、外部メールで調整します。</p>
        </li>
    </ol>
</section> -->

<section class="home-section">
    <div class="section-heading">
        <div>
            <div class="eyebrow">JOURNAL</div>
            <h2>指導現場の知見</h2>
        </div><a href="{{ route('articles.index') }}">記事一覧 →</a>
    </div>
    <div class="article-grid">@foreach($articles as $article)<article class="article-card"><a href="{{ route('articles.show',$article) }}"><img src="{{ asset($article->cover_image_path ?: 'images/sports-analysis-interview.png') }}" alt="">
                <div><span class="eyebrow">{{ strtoupper($article->category) }}</span>
                    <h3>{{ $article->title }}</h3>
                    <p>{{ $article->excerpt }}</p>
                </div>
            </a></article>@endforeach</div>
</section>

<section class="home-section home-news-section" aria-labelledby="home-news-title">
    <header class="news-heading">
        <div>
            <div class="eyebrow">NEWS / INFORMATION</div>
            <h2 id="home-news-title">お知らせ</h2>
        </div>
        <p>Back Athlete Matchingからの<br>最新情報をご案内します。</p>
    </header>
    <div class="news-list">
        <article class="news-item">
            <time datetime="2026-09-20">2026.09.20</time>
            <span class="news-category">SERVICE</span>
            <h3>神奈川県を中心とした試験導入を開始しました</h3>
            <span class="news-mark" aria-hidden="true">01</span>
        </article>
        <article class="news-item">
            <time datetime="2026-09-12">2026.09.12</time>
            <span class="news-category">INFORMATION</span>
            <h3>スポーツ指導者・科学者の事前登録受付を開始しました</h3>
            <span class="news-mark" aria-hidden="true">02</span>
        </article>
        <article class="news-item">
            <time datetime="2026-09-01">2026.09.01</time>
            <span class="news-category">PROJECT</span>
            <h3>Back Athlete Matchingプロジェクトサイトを公開しました</h3>
            <span class="news-mark" aria-hidden="true">03</span>
        </article>
    </div>
</section>

<section class="project-team-section" aria-labelledby="project-team-title">
    <header class="project-team-heading">
        <div>
            <div class="eyebrow">DEVELOPMENT / OPERATIONS</div>
            <h2 id="project-team-title">開発者・運営者</h2>
        </div>
        <p>競技現場と専門知をつなぐため、<br>多様なメンバーが運営しています。</p>
    </header>
    <div class="project-team-grid">
        <article class="project-member"><img class="member-portrait" src="{{ asset('images/team-member-01.png') }}" alt="上水 研一朗のプロフィール写真" width="444" height="444" loading="lazy"><span class="member-role">PROJECT LEAD</span><p class="member-affiliation">東海大学 上水研究室</p><h3>上水 研一朗</h3></article>
        <article class="project-member"><img class="member-portrait" src="{{ asset('images/team-member-02.png') }}" alt="高橋 美咲のプロフィール写真" width="443" height="444" loading="lazy"><span class="member-role">OPERATIONS</span><p class="member-affiliation">Back Athlete機構</p><h3>高橋 美咲</h3></article>
        <article class="project-member"><img class="member-portrait" src="{{ asset('images/team-member-03.png') }}" alt="佐藤 健一のプロフィール写真" width="444" height="444" loading="lazy"><span class="member-role">SUPERVISOR</span><p class="member-affiliation">スポーツ科学研究センター</p><h3>佐藤 健一</h3></article>
        <article class="project-member"><img class="member-portrait" src="{{ asset('images/team-member-04.png') }}" alt="中村 由佳のプロフィール写真" width="443" height="444" loading="lazy"><span class="member-role">COORDINATOR</span><p class="member-affiliation">地域スポーツ推進室</p><h3>中村 由佳</h3></article>
        <article class="project-member"><img class="member-portrait" src="{{ asset('images/team-member-05.png') }}" alt="伊藤 真理のプロフィール写真" width="444" height="443" loading="lazy"><span class="member-role">ADVISOR</span><p class="member-affiliation">アスリート支援事業部</p><h3>伊藤 真理</h3></article>
        <article class="project-member"><img class="member-portrait" src="{{ asset('images/team-member-06.png') }}" alt="山本 大輔のプロフィール写真" width="443" height="443" loading="lazy"><span class="member-role">DEVELOPER</span><p class="member-affiliation">株式会社ハイクラス</p><h3>山本 大輔</h3></article>
        <article class="project-member"><img class="member-portrait" src="{{ asset('images/team-member-07.png') }}" alt="小林 彩香のプロフィール写真" width="444" height="443" loading="lazy"><span class="member-role">DESIGNER</span><p class="member-affiliation">株式会社ハイクラス</p><h3>小林 彩香</h3></article>
        <article class="project-member"><img class="member-portrait" src="{{ asset('images/team-member-08.png') }}" alt="松本 拓也のプロフィール写真" width="443" height="443" loading="lazy"><span class="member-role">ENGINEER</span><p class="member-affiliation">株式会社ハイクラス</p><h3>松本 拓也</h3></article>
    </div>
</section>

<section class="dual-cta">
    <article>
        <div class="eyebrow">FOR COACHES</div>
        <h2>指導の経験を、次の世代へ。</h2>
        <p>プロフィールを公開し、全国のチーム・部活とつながる。</p><a class="btn" href="{{ route('register') }}">指導者会員に登録</a>
    </article>
    <article>
        <div class="eyebrow">FOR ORGANIZATIONS</div>
        <h2>チームに必要な専門家を。</h2>
        <p>募集掲載と直接オファーで、最適な指導者を探す。</p><a class="btn" href="{{ route('register') }}">チーム・部活会員に登録</a>
    </article>
</section>
@endsection
