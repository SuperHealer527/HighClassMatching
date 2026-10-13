@extends('layouts.admin')
@section('title','指導者審査')
@section('content')
@php
    $photo = $coach->photo_path
        ? (\Illuminate\Support\Str::startsWith($coach->photo_path, 'images/') ? asset($coach->photo_path) : asset('storage/'.$coach->photo_path))
        : asset('images/sample-coach-profile.png');
    $recommendations = (array) ($coach->recommendations ?: []);
    $education = (array) ($coach->education_history ?: []);
    $qualifications = (array) ($coach->qualification_items ?: []);
    $hasIdentityDocument = filled($coach->identity_document_path);
    $hasQualificationDocument = filled($coach->qualification_document_path);
    $hasRequiredDocuments = $hasIdentityDocument && $hasQualificationDocument;
    $isVerified = $coach->verification_status === 'verified';
@endphp

<div class="admin-page-actions">
    <a class="btn btn-outline-light" href="{{ route('admin.coaches.index') }}">指導者一覧へ戻る</a>
    <a class="btn btn-outline-light" href="{{ route('coaches.show', $coach) }}" target="_blank" rel="noopener">公開プロフィールを見る</a>
</div>

<section class="review-hero admin-panel">
    <img class="review-hero-photo" src="{{ $photo }}" alt="{{ $coach->name }}" onerror="this.onerror=null;this.src='{{ asset('images/sample-coach-profile.png') }}'">
    <div class="review-hero-copy">
        <div class="admin-eyebrow">COACH REVIEW / #{{ $coach->id }}</div>
        <h2>{{ $coach->name }}</h2>
        <p>{{ $coach->kana }} @if($coach->roman_name)/ {{ $coach->roman_name }}@endif</p>
        <div class="admin-badge-row"><span class="status-chip is-{{ $coach->status }}">{{ $coach->status }}</span><span class="status-chip is-{{ $coach->verification_status }}">{{ $coach->verification_status }}</span>@if($coach->is_student)<span class="status-chip">学生</span>@endif</div>
    </div>
    <dl class="review-hero-meta">
        <div><dt>登録日</dt><dd>{{ $coach->created_at->format('Y.m.d') }}</dd></div>
        <div><dt>最終更新</dt><dd>{{ optional($coach->profile_updated_at ?: $coach->updated_at)->format('Y.m.d H:i') }}</dd></div>
        <div><dt>プロフィール充実度</dt><dd>{{ $coach->completeness_score }}%</dd></div>
    </dl>
</section>

<section class="review-progress" aria-label="審査進捗">
    <article class="review-step {{ optional($coach->user)->status === 'approved' ? 'is-complete' : '' }}"><span>01</span><div><small>ACCOUNT</small><strong>アカウント</strong><p>{{ optional($coach->user)->status ?: '未登録' }}</p></div></article>
    <article class="review-step {{ $hasRequiredDocuments ? 'is-complete' : '' }}"><span>02</span><div><small>DOCUMENTS</small><strong>必要書類</strong><p>{{ (int) $hasIdentityDocument + (int) $hasQualificationDocument }} / 2 点</p></div></article>
    <article class="review-step {{ $isVerified ? 'is-complete' : '' }}"><span>03</span><div><small>VERIFICATION</small><strong>本人・資格確認</strong><p>{{ $coach->verification_status }}</p></div></article>
    <article class="review-step {{ $coach->status === 'approved' ? 'is-complete' : '' }}"><span>04</span><div><small>PUBLICATION</small><strong>公開審査</strong><p>{{ $coach->status }}</p></div></article>
</section>

<div class="admin-review-grid">
    <main class="admin-review-main">
        <section class="admin-panel review-section">
            <header class="admin-section-heading"><div><span>PUBLIC PROFILE</span><h2>公開プロフィール情報</h2></div><span class="section-count">01</span></header>
            <dl class="admin-data-grid">
                <div><dt>所属</dt><dd>{{ $coach->affiliation ?: '未設定' }}</dd></div>
                <div><dt>その他の所属</dt><dd>{{ implode(' / ', (array) $coach->other_affiliations) ?: '未設定' }}</dd></div>
                <div><dt>学歴</dt><dd>{{ implode(' / ', $education) ?: '未設定' }}</dd></div>
                <div><dt>学位</dt><dd>{{ $coach->degree ?: '未設定' }}</dd></div>
                <div><dt>資格</dt><dd>{{ implode(' / ', $qualifications) ?: '未設定' }}</dd></div>
                <div><dt>指導分野</dt><dd>{{ implode(' / ', (array) $coach->fields) ?: '未設定' }}</dd></div>
                <div><dt>専門競技</dt><dd>{{ implode(' / ', (array) $coach->sports) ?: '未設定' }}</dd></div>
                <div><dt>現在の拠点</dt><dd>{{ $coach->main_prefecture }}{{ $coach->area ? ' / '.$coach->area : '' }}</dd></div>
                <div><dt>対応可能地域</dt><dd>{{ implode(' / ', (array) $coach->available_prefectures) ?: '未設定' }}</dd></div>
                <div><dt>キーワード</dt><dd>{{ $coach->keywords ?: '未設定' }}</dd></div>
            </dl>
            <div class="review-message"><span>紹介文</span><p>{{ $coach->message ?: '未設定' }}</p></div>
        </section>

        <section class="admin-panel review-section">
            <header class="admin-section-heading"><div><span>RECOMMENDATION / REFERRAL</span><h2>推薦者・紹介者</h2></div><span class="section-count">02</span></header>
            <div class="review-recommenders">
                @for($index = 0; $index < 3; $index++)
                    @php($recommendation = $recommendations[$index] ?? null)
                    <article class="review-person-card">
                        <span>推薦者 {{ sprintf('%02d', $index + 1) }}</span>
                        @if($recommendation)
                            <strong>{{ $recommendation['name'] ?? '名称未設定' }}</strong>
                            <p>{{ $recommendation['introduction'] ?? 'コメント未設定' }}</p>
                        @else
                            <strong>未登録</strong><p>推薦者情報は登録されていません。</p>
                        @endif
                    </article>
                @endfor
                <article class="review-person-card is-referrer">
                    <span>登録紹介者</span>
                    <strong>{{ optional($coach->user)->referrer ?: '未登録' }}</strong>
                    <p>紹介制登録時に入力された紹介者です。</p>
                </article>
            </div>
        </section>

        <section class="admin-panel review-section">
            <header class="admin-section-heading"><div><span>CORRECTION REQUEST</span><h2>プロフィール修正依頼</h2></div><span class="section-count">03</span></header>
            <form method="post" action="{{ route('admin.coaches.revision-requests.store', $coach) }}" class="revision-compose">
                @csrf
                <label class="form-label" for="revision-message">コーチへ伝える修正内容</label>
                <textarea class="form-control" id="revision-message" name="message" rows="6" maxlength="2000" placeholder="例：所属名の正式名称を確認し、資格証明書と一致する表記へ修正してください。" required>{{ old('message') }}</textarea>
                <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mt-3"><small class="text-secondary">送信するとコーチのマイページと通知一覧へ届きます。メール通知設定が有効な場合はメールも送信されます。</small><button class="btn btn-primary" type="submit">修正依頼を送信</button></div>
            </form>

            <div class="revision-history">
                @forelse($coach->revisionRequests as $revisionRequest)
                    <article class="revision-history-item">
                        <div><span class="status-chip is-{{ $revisionRequest->status }}">{{ $revisionRequest->status === 'open' ? '対応待ち' : '完了' }}</span><time>{{ $revisionRequest->created_at->format('Y.m.d H:i') }}</time></div>
                        <p>{{ $revisionRequest->message }}</p>
                        <footer><span>{{ optional($revisionRequest->requestedBy)->name ?: '運営事務局' }}</span><form method="post" action="{{ route('admin.profile-revision-requests.update', $revisionRequest) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="{{ $revisionRequest->status === 'open' ? 'completed' : 'open' }}"><button class="btn btn-outline-light btn-sm" type="submit">{{ $revisionRequest->status === 'open' ? '完了にする' : '再開する' }}</button></form></footer>
                    </article>
                @empty
                    <div class="admin-empty">修正依頼の履歴はありません。</div>
                @endforelse
            </div>
        </section>
    </main>

    <aside class="admin-review-side">
        <section class="admin-panel review-side-panel">
            <div class="admin-eyebrow">PRIVATE / SCREENING</div><h2>非公開・審査情報</h2>
            <dl class="private-data-list">
                <div><dt>メール</dt><dd>{{ optional($coach->user)->email ?: $coach->email ?: '未設定' }}</dd></div>
                <div><dt>電話番号</dt><dd>{{ $coach->phone ?: '未設定' }}</dd></div>
                <div><dt>会員区分</dt><dd>{{ optional($coach->user)->member_type ?: '未設定' }}</dd></div>
                <div><dt>紹介者</dt><dd>{{ optional($coach->user)->referrer ?: '未登録' }}</dd></div>
            </dl>
        </section>

        <section class="admin-panel review-side-panel">
            <div class="admin-eyebrow">DOCUMENT CHECK</div><h2>確認書類</h2>
            <div class="document-check-list">
                <div class="{{ $hasIdentityDocument ? 'is-ready' : '' }}"><span>本人確認書類</span>@if($hasIdentityDocument)<a href="{{ route('coaches.documents.download', [$coach, 'identity']) }}">確認する</a>@else<strong>未提出</strong>@endif</div>
                <div class="{{ $hasQualificationDocument ? 'is-ready' : '' }}"><span>資格確認書類</span>@if($hasQualificationDocument)<a href="{{ route('coaches.documents.download', [$coach, 'qualification']) }}">確認する</a>@else<strong>未提出</strong>@endif</div>
            </div>
            <form method="post" action="{{ route('admin.coaches.verify', $coach) }}" class="review-control-form">
                @csrf @method('PATCH')
                <label class="form-label">本人・資格確認</label>
                <select class="form-select" name="verification_status">
                    @if(!$isVerified && $coach->verification_status !== 'rejected')<option value="" selected>審査結果を選択</option>@endif
                    <option value="verified" {{ $isVerified ? 'selected' : '' }} {{ !$hasRequiredDocuments && !$isVerified ? 'disabled' : '' }}>verified</option>
                    <option value="rejected" {{ $coach->verification_status === 'rejected' ? 'selected' : '' }}>rejected</option>
                </select>
                <button class="btn btn-primary" type="submit">確認状態を更新</button>
            </form>
        </section>

        <section class="admin-panel review-side-panel">
            <div class="admin-eyebrow">PUBLICATION</div><h2>公開状態</h2>
            <form method="post" action="{{ route('admin.coaches.status', $coach) }}" class="review-control-form">
                @csrf @method('PATCH')
                <label class="form-label">プロフィール状態</label>
                <select class="form-select" name="status">
                    @foreach(['pending','approved','rejected','suspended'] as $status)
                        <option value="{{ $status }}" {{ $coach->status === $status ? 'selected' : '' }} {{ $status === 'approved' && !$isVerified ? 'disabled' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
                <button class="btn btn-primary" type="submit">公開状態を更新</button>
                @if(!$isVerified)<small class="text-warning">公開承認には本人・資格確認が必要です。</small>@endif
            </form>
        </section>
    </aside>
</div>
@endsection
