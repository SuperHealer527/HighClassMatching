@extends('layouts.user')
@section('content')
@php($editing = isset($job) && $job->exists)
<div class="eyebrow">JOB POSTING</div><h1>案件{{ $editing ? '編集' : '登録' }}</h1>
@if($organizations->isEmpty())<div class="alert">承認済みの団体プロフィールが必要です。</div>@else
<form class="form" method="post" enctype="multipart/form-data" action="{{ $editing ? route('jobs.update',$job) : route('jobs.store') }}">@csrf @if($editing)@method('PATCH')@endif
@if($errors->any())<div class="alert">{{ $errors->first() }}</div>@endif
@unless($editing)<label><span class="label">団体</span><select class="field" name="organization_id" required>@foreach($organizations as $org)<option value="{{ $org->id }}">{{ $org->name }}</option>@endforeach</select></label>@endunless
<label><span class="label">案件タイトル</span><input class="field" name="title" value="{{ old('title',$job->title ?? '') }}" required></label>
<div class="grid2"><label><span class="label">募集職種</span><input class="field" name="job_type" value="{{ old('job_type',$job->job_type ?? '') }}" required></label><label><span class="label">都道府県</span><select class="field" name="prefecture" required>@foreach($prefectures as $pref)<option value="{{ $pref }}" @selected(old('prefecture',$job->prefecture ?? '')===$pref)>{{ $pref }}</option>@endforeach</select></label></div>
<div class="grid2"><label><span class="label">エリア</span><input class="field" name="area" value="{{ old('area',$job->area ?? '') }}"></label><label><span class="label">競技</span><input class="field" name="sport" value="{{ old('sport',$job->sport ?? '') }}"></label></div>
<div class="grid2"><label><span class="label">対象年代</span><input class="field" name="target_age" value="{{ old('target_age',$job->target_age ?? '') }}"></label><label><span class="label">性別</span><input class="field" name="gender" value="{{ old('gender',$job->gender ?? '') }}"></label></div>
<div class="grid2"><label><span class="label">依頼頻度</span><input class="field" name="request_frequency" value="{{ old('request_frequency',$job->request_frequency ?? '') }}"></label><label><span class="label">依頼形態</span><input class="field" name="request_style" value="{{ old('request_style',$job->request_style ?? '') }}"></label></div>
<label><span class="label">費用</span><input class="field" name="budget_note" value="{{ old('budget_note',$job->budget_note ?? '応相談') }}" readonly></label>
<label><span class="label">必要資格・条件</span><textarea class="field textarea" name="required_conditions">{{ old('required_conditions',$job->required_conditions ?? '') }}</textarea></label><label><span class="label">業務の役割</span><textarea class="field textarea" name="role_description">{{ old('role_description',$job->role_description ?? '') }}</textarea></label><label><span class="label">希望詳細</span><textarea class="field textarea" name="detail">{{ old('detail',$job->detail ?? '') }}</textarea></label><label><span class="label">特記事項</span><textarea class="field textarea" name="notes">{{ old('notes',$job->notes ?? '') }}</textarea></label>
<div class="grid2"><label><span class="label">募集終了日</span><input class="field" name="publish_end_at" type="date" value="{{ old('publish_end_at',isset($job) && $job->publish_end_at ? $job->publish_end_at->format('Y-m-d') : '') }}"></label><label><span class="label">募集イメージ</span><input class="field" name="image" type="file" accept="image/jpeg,image/png,image/webp"></label></div>
<button class="btn" type="submit">{{ $editing ? '変更を保存する' : '審査へ提出する' }}</button>
</form>@endif
@endsection
