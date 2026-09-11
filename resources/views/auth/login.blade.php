@extends('layouts.user')
@section('content')
<h1>ログイン</h1>
<form class="form" method="post" action="{{ route('login') }}">
    @csrf
    @if($errors->any())<div class="alert">{{ $errors->first() }}</div>@endif
    <label><span class="label">メールアドレス</span><input class="field" name="email" type="email" value="{{ old('email') }}" required></label>
    <label><span class="label">パスワード</span><input class="field" name="password" type="password" required></label>
<button class="btn" type="submit">ログイン</button>
<p class="meta"><a href="{{ route('password.request') }}">パスワードをお忘れの方</a></p>
</form>
@endsection
