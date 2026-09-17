@extends('layouts.user')
@section('content')
@php
    $education = old('education_history', $coach && $coach->education_history ? $coach->education_history : array_filter([$coach ? $coach->degree : null]));
    $qualifications = old('qualification_items', $coach && $coach->qualification_items ? $coach->qualification_items : array_slice(preg_split('/[、,\r\n]+/u', (string) ($coach ? $coach->qualifications : ''), -1, PREG_SPLIT_NO_EMPTY), 0, 2));
    $teachingAchievements = old('teaching_achievements', $coach && $coach->teaching_achievements ? $coach->teaching_achievements : array_filter(preg_split('/\r\n|\r|\n/u', (string) ($coach ? $coach->achievements : ''), -1, PREG_SPLIT_NO_EMPTY)));
    $requestAchievements = old('request_achievements', $coach && $coach->request_achievements ? $coach->request_achievements : array_filter(preg_split('/\r\n|\r|\n/u', (string) ($coach ? $coach->request_history : ''), -1, PREG_SPLIT_NO_EMPTY)));
    $recommendations = old('recommendations', $coach && $coach->recommendations ? $coach->recommendations : (($coach && $coach->recommended_athlete) ? [['name' => $coach->recommended_athlete, 'introduction' => '']] : []));
    $selectedFields = old('fields', $coach ? ($coach->fields ?: []) : []);
    $selectedPrefectures = old('available_prefectures', $coach ? ($coach->available_prefectures ?: []) : []);
    $selectedSport = old('sports', ($coach && !empty($coach->sports)) ? $coach->sports[0] : '');
    $directOfferEnabled = (int) old('direct_offer_enabled', $coach ? (int) $coach->direct_offer_enabled : 1);
@endphp

<header class="profile-form-heading">
    <div class="eyebrow">COACH PROFILE</div>
    <h1>指導者プロフィール{{ $coach ? '編集' : '登録' }}</h1>
    <p>公開プロフィールとオファー受付条件を設定します。</p>
</header>

<form class="form coach-profile-form" method="post" action="{{ route('coaches.store') }}" enctype="multipart/form-data">
@csrf
@if($errors->any())<div class="alert">{{ $errors->first() }}</div>@endif

<section class="profile-form-section">
    <div class="form-section-title"><span>01</span><div><h2>基本情報</h2><p class="meta">名前、所属、活動地域は公開プロフィールに表示されます。</p></div></div>
    <div class="grid2">
        <label><span class="label">名前</span><input class="field" name="name" value="{{ old('name', optional($coach)->name) }}" required></label>
        <label><span class="label">ふりがな</span><input class="field" name="kana" value="{{ old('kana', optional($coach)->kana) }}"></label>
        <label><span class="label">ローマ字</span><input class="field" name="roman_name" value="{{ old('roman_name', optional($coach)->roman_name) }}"></label>
        <label><span class="label">所属</span><input class="field" name="affiliation" value="{{ old('affiliation', optional($coach)->affiliation) }}"></label>
        <label><span class="label">現在の拠点（都道府県）</span><select class="field" name="main_prefecture" required><option value="">選択してください</option>@foreach($prefectures as $pref)<option value="{{ $pref }}" {{ old('main_prefecture', optional($coach)->main_prefecture) === $pref ? 'selected' : '' }}>{{ $pref }}</option>@endforeach</select></label>
        <label><span class="label">市区町村・活動エリア</span><input class="field" name="area" value="{{ old('area', optional($coach)->area) }}" placeholder="横浜市 / 関東圏など"></label>
    </div>
    <fieldset class="form-fieldset"><legend>対応可能地域 <small>必ず公開されます</small></legend><div class="prefecture-check-grid">@foreach($prefectures as $pref)<label class="compact-check"><input type="checkbox" name="available_prefectures[]" value="{{ $pref }}" {{ in_array($pref, $selectedPrefectures, true) ? 'checked' : '' }}><span>{{ $pref }}</span></label>@endforeach</div></fieldset>
    <label class="file-field"><span class="label">プロフィール写真</span><input class="field" name="photo" type="file" accept="image/jpeg,image/png,image/webp"><small class="photo-ratio-note"><strong>推奨比率 4:5</strong>（例 800 × 1000px） / JPG・PNG・WebP、5MBまで</small></label>
</section>

<section class="profile-form-section">
    <div class="form-section-title"><span>02</span><div><h2>専門性・評価・実績</h2><p class="meta">比較しやすいよう、項目ごとに簡潔に入力してください。</p></div></div>
    <fieldset class="form-fieldset"><legend>指導分野 <small>複数選択可</small></legend><div class="choice-grid">@foreach($fields as $field)<label class="choice-tile"><input type="checkbox" name="fields[]" value="{{ $field }}" {{ in_array($field, $selectedFields, true) ? 'checked' : '' }}><span>{{ $field }}</span></label>@endforeach</div></fieldset>
    <label><span class="label">専門競技 <small>1つまで</small></span><input class="field" name="sports" value="{{ $selectedSport }}" placeholder="バスケットボール"></label>
    <div class="repeat-fields"><div class="repeat-heading"><h3>学歴</h3><span>最大3件</span></div>@for($i=0;$i<3;$i++)<label class="repeat-field"><span>{{ sprintf('%02d', $i + 1) }}</span><input class="field" name="education_history[]" value="{{ $education[$i] ?? '' }}" placeholder="大学・大学院・専攻など"></label>@endfor</div>
    <div class="repeat-fields"><div class="repeat-heading"><h3>資格</h3><span>最大2件</span></div>@for($i=0;$i<2;$i++)<label class="repeat-field"><span>{{ $i === 0 ? '①' : '②' }}</span><input class="field" name="qualification_items[]" value="{{ $qualifications[$i] ?? '' }}" placeholder="資格名"></label>@endfor</div>
    <div class="repeat-fields"><div class="repeat-heading"><h3>指導実績</h3><span>1行程度・最大3件</span></div>@for($i=0;$i<3;$i++)<label class="repeat-field"><span>{{ sprintf('%02d', $i + 1) }}</span><input class="field" name="teaching_achievements[]" value="{{ $teachingAchievements[$i] ?? '' }}" placeholder="例：全国大会出場チームを3年間指導"></label>@endfor</div>
    <div class="repeat-fields"><div class="repeat-heading"><h3>依頼実績</h3><span>1行程度・最大3件</span></div>@for($i=0;$i<3;$i++)<label class="repeat-field"><span>{{ sprintf('%02d', $i + 1) }}</span><input class="field" name="request_achievements[]" value="{{ $requestAchievements[$i] ?? '' }}" placeholder="例：高校部活動の年間指導計画を担当"></label>@endfor</div>
    <div class="repeat-fields"><div class="repeat-heading"><h3>推薦者</h3><span>推薦者名と紹介文・最大3件</span></div><div class="recommendation-form-grid">@for($i=0;$i<3;$i++)<div class="recommendation-form-item"><strong>推薦 {{ sprintf('%02d', $i + 1) }}</strong><label><span class="label">推薦者名</span><input class="field" name="recommendations[{{ $i }}][name]" value="{{ $recommendations[$i]['name'] ?? '' }}" placeholder="選手名・団体名など"></label><label><span class="label">紹介文</span><textarea class="field textarea compact-textarea" name="recommendations[{{ $i }}][introduction]" placeholder="指導を受けた感想や推薦コメント">{{ $recommendations[$i]['introduction'] ?? '' }}</textarea></label></div>@endfor</div></div>
</section>

<section class="profile-form-section">
    <div class="form-section-title"><span>03</span><div><h2>オファー条件とメッセージ</h2><p class="meta">直接オファーを受けない場合も、事務局を通じた相談は受け付けられます。</p></div></div>
    <fieldset class="form-fieldset"><legend>直接オファー</legend><div class="segmented-choice"><label><input type="radio" name="direct_offer_enabled" value="1" {{ $directOfferEnabled === 1 ? 'checked' : '' }}><span>許可する</span></label><label><input type="radio" name="direct_offer_enabled" value="0" {{ $directOfferEnabled === 0 ? 'checked' : '' }}><span>許可しない</span></label></div></fieldset>
    <label><span class="label">オファー可能な依頼・希望金額</span><input class="field" name="desired_fee_range" value="{{ old('desired_fee_range', optional($coach)->desired_fee_range) }}" placeholder="例：部活動指導・講習会 / 30,000円〜"></label>
    <label><span class="label">メッセージ</span><textarea class="field textarea" name="message" placeholder="指導方針やチーム・選手へのメッセージ">{{ old('message', optional($coach)->message) }}</textarea></label>
    <div class="grid2"><label><span class="label">メール（非公開）</span><input class="field" name="email" type="email" value="{{ old('email', optional($coach)->email) }}"></label><label><span class="label">電話（非公開）</span><input class="field" name="phone" value="{{ old('phone', optional($coach)->phone) }}"></label></div>
    <label><span class="label">キーワード <small>プロフィールの最下部に表示</small></span><input class="field" name="keywords" value="{{ old('keywords', optional($coach)->keywords) }}" placeholder="育成年代, パフォーマンス向上, 全国対応"></label>
</section>

<section class="profile-form-section">
    <div class="form-section-title"><span>04</span><div><h2>本人・資格確認</h2><p class="meta">新規登録時は2点とも必須です。書類は公開されません。</p></div></div>
    <div class="grid2"><label><span class="label">本人確認書類</span><input class="field" name="identity_document" type="file" accept="application/pdf,image/jpeg,image/png"><small class="meta">運転免許証など / PDF・画像、5MBまで</small></label><label><span class="label">資格証明書</span><input class="field" name="qualification_document" type="file" accept="application/pdf,image/jpeg,image/png"><small class="meta">指導資格・専門資格 / PDF・画像、5MBまで</small></label></div>
    @if($coach)<p class="profile-form-status"><span class="badge status">確認状態: {{ $coach->verification_status }}</span><span>プロフィール充実度: {{ $coach->completeness_score }}%</span></p>@endif
</section>
<div class="profile-form-submit"><button class="btn" type="submit">{{ $coach ? '変更を保存する' : '登録する' }}</button></div>
</form>
@endsection
