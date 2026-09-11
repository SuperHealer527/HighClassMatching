@php
    $promoTitle = $title ?? '信頼できる出会いを、地域スポーツの力に。';
    $promoText = $text ?? '確認済みの専門家と学校・スポーツ団体を、競技・専門分野・活動地域からつなぎます。';
@endphp
<section class="matching-promo">
    <div class="matching-promo-copy">
        <div class="eyebrow">HIGH CLASS MATCHING</div>
        <h2>{{ $promoTitle }}</h2>
        <p>{{ $promoText }}</p>
        <div class="promo-actions">
            @guest<a class="btn promo-primary" href="{{ route('register') }}">無料会員登録 <span>→</span></a>@endguest
            <a class="promo-link" href="{{ route('coaches.index') }}">指導者を探す</a>
            <a class="promo-link" href="{{ route('jobs.index') }}">案件を探す</a>
        </div>
    </div>
    <div class="promo-proof">
        <article><strong>01</strong><div><h3>本人・資格確認</h3><p>提出書類を運営が確認した指導者を掲載。</p></div></article>
        <article><strong>02</strong><div><h3>条件マッチング</h3><p>競技、専門分野、活動地域から候補を提案。</p></div></article>
        <article><strong>03</strong><div><h3>直接オファー</h3><p>気になる指導者へ団体から相談できます。</p></div></article>
    </div>
</section>
