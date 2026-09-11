@extends('layouts.user')
@section('content')
<div class="page-heading support-page-heading">
    <div><div class="eyebrow">SUPPORT DESK</div><h1>{{ $inquiry->subject }}</h1><p>お問い合わせ内容と運営からの回答を確認できます。</p></div>
    <a class="text-link" href="{{ route('inquiries.index') }}">お問い合わせ一覧へ →</a>
</div>

<div class="support-detail">
    <aside class="support-status-panel">
        <span class="eyebrow">TICKET STATUS</span><strong>{{ $inquiry->status }}</strong>
        <dl><div><dt>カテゴリー</dt><dd>{{ $inquiry->category }}</dd></div><div><dt>受付日時</dt><dd>{{ $inquiry->created_at->format('Y.m.d H:i') }}</dd></div>@if($inquiry->coachProfile)<div><dt>相談先</dt><dd><a href="{{ route('coaches.show', $inquiry->coachProfile) }}">{{ $inquiry->coachProfile->name }}</a></dd></div>@endif</dl>
    </aside>
    <div class="support-thread">
        <section class="support-message is-user"><div class="support-message-meta"><span>YOUR MESSAGE</span><time>{{ $inquiry->created_at->format('Y.m.d H:i') }}</time></div><p class="preline">{{ $inquiry->body }}</p></section>
        @if($inquiry->admin_reply)
        <section class="support-message is-staff"><div class="support-message-meta"><span>HIGHCLASS SUPPORT</span><time>{{ optional($inquiry->replied_at)->format('Y.m.d H:i') }}</time></div><h2>運営からの返信</h2><p class="preline">{{ $inquiry->admin_reply }}</p></section>
        @else
        <section class="support-waiting"><span></span><div><strong>運営が内容を確認しています</strong><p>返信までしばらくお待ちください。回答が届くとステータスが更新されます。</p></div></section>
        @endif
    </div>
</div>
@endsection
