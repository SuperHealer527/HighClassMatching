@extends('layouts.user')
@section('content')
<div class="auth-shell"><form class="form" method="post" action="{{ route('password.update') }}">@csrf<input type="hidden" name="token" value="{{ $token }}"><div class="eyebrow">NEW PASSWORD</div><h1>新しいパスワード</h1>@if($errors->any())<div class="alert">{{ $errors->first() }}</div>@endif<label><span class="label">メールアドレス</span><input class="field" name="email" type="email" value="{{ $email }}" required></label><label><span class="label">新しいパスワード</span><input class="field" name="password" type="password" required></label><label><span class="label">パスワード確認</span><input class="field" name="password_confirmation" type="password" required></label><button class="btn">パスワードを更新</button></form></div>
@endsection
