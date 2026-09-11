@extends('layouts.user')
@section('content')
<div class="eyebrow">HIGHCLASS SUPPORT</div>
<h1>{{ $coach ? $coach->name.'さんへの相談' : 'お問い合わせ' }}</h1>
<form class="form" method="post" action="{{ route('inquiries.store') }}">
@csrf
@if($errors->any())<div class="alert">{{ $errors->first() }}</div>@endif
@if($coach)<input type="hidden" name="coach_profile_id" value="{{ $coach->id }}">@endif
<label><span class="label">お問い合わせ種別</span><select class="field" name="category" required><option value="consultation" @selected(old('category', $coach ? 'consultation' : '') === 'consultation')>指導者への相談</option><option value="trouble" @selected(old('category') === 'trouble')>トラブル・通報</option><option value="account" @selected(old('category') === 'account')>アカウント</option><option value="service" @selected(old('category') === 'service')>サービスについて</option><option value="other" @selected(old('category') === 'other')>その他</option></select></label>
<label><span class="label">件名</span><input class="field" name="subject" value="{{ old('subject', $coach ? $coach->name.'さんへの指導相談' : '') }}" required maxlength="255"></label>
<label><span class="label">お問い合わせ内容</span><textarea class="field textarea" name="body" required maxlength="5000" placeholder="ご希望の競技、対象年代、場所、時期などをご記入ください。">{{ old('body') }}</textarea></label>
<p class="meta">連絡先は公開されません。運営が内容を確認してご案内します。</p>
<button class="btn" type="submit">送信する</button>
</form>
@endsection
