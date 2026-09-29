@extends('layouts.user')
@section('content')
@php($mediated = isset($mode) && $mode === 'mediated')
<div class="eyebrow">HIGHCLASS SUPPORT</div>
<h1>{{ $coach ? ($mediated ? $coach->name.'さんについて事務局へ相談' : $coach->name.'さんへの相談') : 'お問い合わせ' }}</h1>
@if($mediated)<p class="inquiry-intro">事務局へメールとサイト内通知が届き、ご依頼内容の整理から指導者との調整までサポートします。</p>@endif
<form class="form" method="post" action="{{ route('inquiries.store') }}">
@csrf
@if($errors->any())<div class="alert">{{ $errors->first() }}</div>@endif
@if($coach)<input type="hidden" name="coach_profile_id" value="{{ $coach->id }}">@endif
<label><span class="label">お問い合わせ種別</span><select class="field" name="category" required>
    @if($coach)<option value="mediated_offer" {{ old('category', $mediated ? 'mediated_offer' : '') === 'mediated_offer' ? 'selected' : '' }}>事務局へ相談</option>@endif
    <option value="consultation" {{ old('category', (!$mediated && $coach) ? 'consultation' : '') === 'consultation' ? 'selected' : '' }}>指導者への相談</option>
    <option value="trouble" {{ old('category') === 'trouble' ? 'selected' : '' }}>トラブル・通報</option>
    <option value="account" {{ old('category') === 'account' ? 'selected' : '' }}>アカウント</option>
    <option value="service" {{ old('category') === 'service' ? 'selected' : '' }}>サービスについて</option>
    <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>その他</option>
</select></label>
<label><span class="label">件名</span><input class="field" name="subject" value="{{ old('subject', $coach ? ($mediated ? $coach->name.'さんへの仲介オファー依頼' : $coach->name.'さんへの指導相談') : '') }}" required maxlength="255"></label>
<label><span class="label">お問い合わせ内容</span><textarea class="field textarea" name="body" required maxlength="5000" placeholder="ご希望の競技、対象年代、場所、時期、予算などをご記入ください。">{{ old('body') }}</textarea></label>
<p class="meta">連絡先は公開されません。運営が内容を確認してご案内します。</p>
<button class="btn" type="submit">{{ $mediated ? '事務局へ相談を送信する' : '送信する' }}</button>
</form>
@endsection
