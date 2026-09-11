@extends('layouts.user')
@section('content')
<div class="auth-shell"><form class="form" method="post" action="{{ route('password.email') }}">@csrf<div class="eyebrow">ACCOUNT RECOVERY</div><h1>パスワードをお忘れの方</h1><p>登録メールアドレスへ再設定リンクを送ります。</p>@if($errors->any())<div class="alert">{{ $errors->first() }}</div>@endif<label><span class="label">メールアドレス</span><input class="field" name="email" type="email" required></label><button class="btn">再設定リンクを送る</button></form></div>
@endsection
