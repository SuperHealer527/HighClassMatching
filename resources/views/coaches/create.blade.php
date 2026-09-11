@extends('layouts.user')
@section('content')
<div class="eyebrow">COACH PROFILE</div><h1>指導者プロフィール{{ $coach ? '編集' : '登録' }}</h1>
<form class="form" method="post" action="{{ route('coaches.store') }}" enctype="multipart/form-data">@csrf
@if($errors->any())<div class="alert">{{ $errors->first() }}</div>@endif
<div class="form-section-title"><span>01</span><div><h2>基本情報</h2><p class="meta">名前と活動地域は公開プロフィールに表示されます。</p></div></div>
<div class="grid2"><label><span class="label">名前</span><input class="field" name="name" value="{{ old('name',optional($coach)->name) }}" required></label><label><span class="label">ふりがな</span><input class="field" name="kana" value="{{ old('kana',optional($coach)->kana) }}"></label></div>
<div class="grid2"><label><span class="label">ローマ字</span><input class="field" name="roman_name" value="{{ old('roman_name',optional($coach)->roman_name) }}"></label><label><span class="label">生まれ年</span><input class="field" name="birth_year" value="{{ old('birth_year',optional($coach)->birth_year) }}" placeholder="1990"></label></div>
<label class="check-row"><input type="checkbox" name="show_birth_year" value="1" @checked(old('show_birth_year',optional($coach)->show_birth_year))><span><strong>生まれ年を公開する</strong><small>未選択の場合は本人と運営だけが確認できます。</small></span></label>
<div class="grid2"><label><span class="label">主要都道府県</span><select class="field" name="main_prefecture" required>@foreach($prefectures as $pref)<option value="{{ $pref }}" @selected(old('main_prefecture',optional($coach)->main_prefecture)===$pref)>{{ $pref }}</option>@endforeach</select></label><label><span class="label">エリア</span><input class="field" name="area" value="{{ old('area',optional($coach)->area) }}"></label></div>
<label><span class="label">対応可能都道府県</span><select class="field multi-field" name="available_prefectures[]" multiple size="6">@foreach($prefectures as $pref)<option value="{{ $pref }}" @selected(in_array($pref,old('available_prefectures',optional($coach)->available_prefectures ?? [])))>{{ $pref }}</option>@endforeach</select></label>
<label class="check-row"><input type="checkbox" name="show_available_prefectures" value="1" @checked(old('show_available_prefectures',optional($coach)->show_available_prefectures ?? true))><span><strong>対応可能都道府県を公開する</strong><small>主要都道府県は常に公開されます。</small></span></label>
<label><span class="label">プロフィール写真</span><input class="field" name="photo" type="file" accept="image/jpeg,image/png,image/webp"><small class="meta">JPG / PNG / WebP、5MBまで</small></label>

<div class="form-section-title"><span>02</span><div><h2>専門性と実績</h2><p class="meta">学校・団体が比較する重要な情報です。</p></div></div>
<div class="grid2"><label><span class="label">得意競技（カンマ区切り）</span><input class="field" name="sports" value="{{ old('sports',implode(',',optional($coach)->sports ?? [])) }}"></label><label><span class="label">指導分野（カンマ区切り）</span><input class="field" name="fields" value="{{ old('fields',implode(',',optional($coach)->fields ?? [])) }}" placeholder="競技指導,メンタル"></label></div>
<label><span class="label">所属</span><input class="field" name="affiliation" value="{{ old('affiliation',optional($coach)->affiliation) }}"></label>
<label><span class="label">学位</span><input class="field" name="degree" value="{{ old('degree',optional($coach)->degree) }}"></label>
<label><span class="label">資格</span><textarea class="field textarea" name="qualifications">{{ old('qualifications',optional($coach)->qualifications) }}</textarea></label>
<label><span class="label">キーワード</span><input class="field" name="keywords" value="{{ old('keywords',optional($coach)->keywords) }}"></label>
<label><span class="label">指導実績</span><textarea class="field textarea" name="achievements">{{ old('achievements',optional($coach)->achievements) }}</textarea></label>
<label><span class="label">依頼実績</span><textarea class="field textarea" name="request_history">{{ old('request_history',optional($coach)->request_history) }}</textarea></label>
<label class="check-row"><input type="checkbox" name="show_request_history" value="1" @checked(old('show_request_history',optional($coach)->show_request_history))><span><strong>依頼実績を公開する</strong><small>実績に個人情報を含めないでください。</small></span></label>
<label><span class="label">推薦アスリート</span><input class="field" name="recommended_athlete" value="{{ old('recommended_athlete',optional($coach)->recommended_athlete) }}"></label>
<label class="check-row"><input type="checkbox" name="show_recommended_athlete" value="1" @checked(old('show_recommended_athlete',optional($coach)->show_recommended_athlete))><span><strong>推薦アスリートを公開する</strong><small>掲載許可を得た情報だけを入力してください。</small></span></label>

<div class="form-section-title"><span>03</span><div><h2>条件と連絡先</h2><p class="meta">メールと電話番号は公開されません。</p></div></div>
<label><span class="label">希望額</span><input class="field" name="desired_fee_range" value="{{ old('desired_fee_range',optional($coach)->desired_fee_range) }}" placeholder="30,000円〜 / 60分"></label>
<label><span class="label">メッセージ</span><textarea class="field textarea" name="message">{{ old('message',optional($coach)->message) }}</textarea></label>
<div class="grid2"><label><span class="label">メール（非公開）</span><input class="field" name="email" type="email" value="{{ old('email',optional($coach)->email) }}"></label><label><span class="label">電話（非公開）</span><input class="field" name="phone" value="{{ old('phone',optional($coach)->phone) }}"></label></div>
<div class="form-section-title"><span>04</span><div><h2>本人・資格確認</h2><p class="meta">安全なマッチングのため、新規登録時は2点とも必須です。書類は公開されません。</p></div></div>
<div class="grid2"><label><span class="label">本人確認書類</span><input class="field" name="identity_document" type="file" accept="application/pdf,image/jpeg,image/png"><small class="meta">運転免許証など / PDF・画像、5MBまで</small></label><label><span class="label">資格証明書</span><input class="field" name="qualification_document" type="file" accept="application/pdf,image/jpeg,image/png"><small class="meta">指導資格・専門資格 / PDF・画像、5MBまで</small></label></div>
@if($coach)<p><span class="badge status">確認状態: {{ $coach->verification_status }}</span>　プロフィール充実度: {{ $coach->completeness_score }}%</p>@endif
<button class="btn" type="submit">{{ $coach ? '変更を保存する' : '登録する' }}</button>
</form>
@endsection
