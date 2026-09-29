@extends('layouts.user')
@section('content')
<h1>新規会員登録</h1>
<form class="form" method="post" action="{{ route('register') }}">
    @csrf
    @if($errors->any())<div class="alert">{{ $errors->first() }}</div>@endif
    <div class="grid2">
        <label><span class="label">氏名/団体担当者名</span><input class="field" name="name" value="{{ old('name') }}" required></label>
        <label><span class="label">メールアドレス</span><input class="field" name="email" type="email" value="{{ old('email') }}" required></label>
    </div>
    <label><span class="label">会員種別</span><select class="field" name="role"><option value="organization" {{ old('role', 'organization') === 'organization' ? 'selected' : '' }}>チーム・部活</option><option value="coach" {{ old('role') === 'coach' ? 'selected' : '' }}>指導者会員</option></select></label>
    <label><span class="label">紹介者 <small>任意</small></span><input class="field" name="referrer" value="{{ old('referrer') }}" maxlength="255" placeholder="紹介者のお名前・団体名"></label>
    <div class="grid2">
        <label><span class="label">パスワード</span><input class="field" name="password" type="password" required></label>
        <label><span class="label">パスワード確認</span><input class="field" name="password_confirmation" type="password" required></label>
    </div>
    <button class="btn" type="submit">登録する</button>
</form>
@endsection
