@extends('layouts.user')
@section('content')
@php($photo = $coach->photo_path ? (str_starts_with($coach->photo_path, 'images/') ? asset($coach->photo_path) : asset('storage/'.$coach->photo_path)) : asset('images/sample-coach-profile.png'))
<div class="page-heading offer-page-heading">
    <div><div class="eyebrow">DIRECT OFFER</div><h1>{{ $coach->name }}さんへ<br>指導を相談する</h1><p>依頼内容を具体的に伝えると、よりスムーズに条件を確認できます。</p></div>
    <a class="text-link" href="{{ route('coaches.show', $coach) }}">プロフィールに戻る →</a>
</div>

<div class="offer-compose">
    <aside class="offer-recipient">
        <img src="{{ $photo }}" alt="{{ $coach->name }}">
        <div><span class="eyebrow">TO COACH</span><h2>{{ $coach->name }}</h2><p>{{ $coach->main_prefecture }} / {{ implode('・', (array) $coach->sports) }}</p></div>
        <dl><div><dt>本人確認</dt><dd>確認済み</dd></div><div><dt>相談方法</dt><dd>サイト内オファー</dd></div><div><dt>連絡先</dt><dd>受諾後に相互開示</dd></div></dl>
    </aside>

    <section class="offer-form-panel">
        <div class="offer-form-intro"><span>01</span><div><div class="eyebrow">REQUEST DETAILS</div><h2>依頼内容</h2></div></div>
        <form class="form" method="post" action="{{ route('offers.store',$coach) }}">@csrf
            @if($errors->any())<div class="alert">{{ $errors->first() }}</div>@endif
            <label><span class="label">関連案件</span><select class="field" name="job_id"><option value="">案件を指定しない</option>@foreach($jobs as $job)<option value="{{ $job->id }}" @selected(old('job_id') == $job->id)>{{ $job->title }}</option>@endforeach</select></label>
            <label><span class="label">件名</span><input class="field" name="subject" value="{{ old('subject','指導のご相談') }}" required></label>
            <label><span class="label">依頼内容</span><textarea class="field textarea" name="message" required placeholder="対象、競技、場所、頻度、依頼したい内容をご記入ください。">{{ old('message') }}</textarea></label>
            <label><span class="label">希望時期・日程</span><input class="field" name="proposed_schedule" value="{{ old('proposed_schedule') }}" placeholder="2026年10月から、毎週土曜日など"></label>
            <div class="offer-assurance"><strong>安心してご相談ください</strong><p>送信時点で契約は成立しません。オファー受諾後に双方の登録連絡先が開示されます。</p></div>
            <button class="btn offer-submit" type="submit">オファーを送信する <span>→</span></button>
        </form>
    </section>
</div>

@include('partials.matching-promo',['title'=>'次の指導機会を、もっと確かな出会いから。','text'=>'競技・地域・活動条件をもとに、団体と指導者の新しい接点をつくります。'])
@endsection
