@extends('layouts.user')
@section('content')
<div class="eyebrow">ORGANIZATION PROFILE</div><h1>団体プロフィール{{ $organization ? '編集' : '登録' }}</h1>
<form class="form" method="post" action="{{ route('organizations.store') }}" enctype="multipart/form-data">@csrf
@if($errors->any())<div class="alert">{{ $errors->first() }}</div>@endif
<label><span class="label">団体名</span><input class="field" name="name" value="{{ old('name',optional($organization)->name) }}" required></label>
<div class="grid2"><label><span class="label">主要都道府県</span><select class="field" name="main_prefecture" required>@foreach($prefectures as $pref)<option value="{{ $pref }}" @selected(old('main_prefecture',optional($organization)->main_prefecture)===$pref)>{{ $pref }}</option>@endforeach</select></label><label><span class="label">エリア</span><input class="field" name="area" value="{{ old('area',optional($organization)->area) }}"></label></div>
<div class="grid2"><label><span class="label">競技</span><input class="field" name="sport" value="{{ old('sport',optional($organization)->sport) }}"></label><label><span class="label">対象年代</span><input class="field" name="target_age" value="{{ old('target_age',optional($organization)->target_age) }}"></label></div>
<div class="grid2"><label><span class="label">性別</span><input class="field" name="gender" value="{{ old('gender',optional($organization)->gender) }}"></label><label><span class="label">人数</span><input class="field" name="member_count" type="number" value="{{ old('member_count',optional($organization)->member_count) }}"></label></div>
<label><span class="label">団体紹介</span><textarea class="field textarea" name="introduction">{{ old('introduction',optional($organization)->introduction) }}</textarea></label>
<label><span class="label">団体イメージ</span><input class="field" name="image" type="file" accept="image/jpeg,image/png,image/webp"></label>
<div class="grid2"><label><span class="label">責任者名（非公開）</span><input class="field" name="manager_name" value="{{ old('manager_name',optional($organization)->manager_name) }}"></label><label><span class="label">責任者メール（非公開）</span><input class="field" name="manager_email" type="email" value="{{ old('manager_email',optional($organization)->manager_email) }}"></label></div>
<label><span class="label">責任者電話（非公開）</span><input class="field" name="manager_phone" value="{{ old('manager_phone',optional($organization)->manager_phone) }}"></label>
<label><span class="label">公式HP/SNS URL</span><input class="field" name="official_url" type="url" value="{{ old('official_url',optional($organization)->official_url) }}"></label>
<button class="btn" type="submit">{{ $organization ? '変更を保存する' : '登録する' }}</button>
</form>
@endsection
