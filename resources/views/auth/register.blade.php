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
    <label><span class="label">会員種別</span><select class="field" name="role"><option value="organization">閲覧会員・学校/団体</option><option value="coach">指導者会員</option></select></label>
    <div class="grid2">
        <label><span class="label">パスワード</span><input class="field" name="password" type="password" required></label>
        <label><span class="label">パスワード確認</span><input class="field" name="password_confirmation" type="password" required></label>
    </div>
    <button class="btn" type="submit">登録する</button>
</form>
@endsection
