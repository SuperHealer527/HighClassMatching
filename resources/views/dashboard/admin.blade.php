@extends('layouts.admin')
@section('title','管理者ダッシュボード')
@section('content')
<div class="row g-3 mb-4">
    <div class="col-md"><div class="glass-card metric"><div class="metric-label">申請中ユーザー</div><div class="metric-value">{{ $pendingUsers }}</div></div></div>
    <div class="col-md"><div class="glass-card metric"><div class="metric-label">指導者審査</div><div class="metric-value">{{ $pendingCoaches }}</div></div></div>
    <div class="col-md"><div class="glass-card metric"><div class="metric-label">団体審査</div><div class="metric-value">{{ $pendingOrganizations }}</div></div></div>
    <div class="col-md"><div class="glass-card metric"><div class="metric-label">案件審査</div><div class="metric-value">{{ $pendingJobs }}</div></div></div>
    <div class="col-md"><div class="glass-card metric"><div class="metric-label">公開案件</div><div class="metric-value">{{ $publishedJobs }}</div></div></div>
    <div class="col-md"><div class="glass-card metric"><div class="metric-label">未解決問い合わせ</div><div class="metric-value">{{ $openInquiries }}</div></div></div>
</div>
<div class="row g-3 mb-4"><div class="col-md"><div class="glass-card metric"><div class="metric-label">オファー</div><div class="metric-value">{{ $offerCount }}</div></div></div><div class="col-md"><div class="glass-card metric"><div class="metric-label">公開評価</div><div class="metric-value">{{ $reviewCount }}</div></div></div><div class="col-md"><div class="glass-card metric"><div class="metric-label">公開記事</div><div class="metric-value">{{ $articleCount }}</div></div></div></div>
<div class="row g-4">
    <div class="col-lg-6">
        <section class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h5 m-0">審査待ち案件</h2><span class="badge badge-soft">{{ $pendingJobs }} pending</span></div>
            <div class="list-group list-group-flush">
                @forelse($pendingJobList as $job)
                    <div class="list-group-item px-0">
                        <div class="fw-bold">{{ $job->title }}</div>
                        <div class="text-secondary small mb-3">{{ $job->organization->name }} / {{ $job->prefecture }} / {{ $job->job_type }}</div>
                        <form method="post" action="{{ route('admin.jobs.approve',$job) }}">@csrf<button class="btn btn-primary btn-sm" type="submit">公開する</button></form>
                    </div>
                @empty
                    <div class="list-group-item px-0 text-secondary">審査待ち案件はありません。</div>
                @endforelse
            </div>
        </section>
    </div>
    <div class="col-lg-6">
        <section class="glass-card p-4">
            <h2 class="h5 mb-3">最近の応募</h2>
            <div class="list-group list-group-flush">
                @forelse($applications as $app)
                    <div class="list-group-item px-0"><div class="fw-bold">{{ $app->job->title }}</div><div class="text-secondary small">{{ $app->coachProfile->name }} -> {{ $app->job->organization->name }} / {{ $app->status }}</div></div>
                @empty
                    <div class="list-group-item px-0 text-secondary">応募はまだありません。</div>
                @endforelse
            </div>
        </section>
    </div>
</div>
<div class="mt-4 d-flex gap-2 flex-wrap">
    <a class="btn btn-primary" href="{{ route('admin.coaches.index') }}">Coaches</a>
    <a class="btn btn-outline-light" href="{{ route('admin.organizations.index') }}">Organizations</a>
    <a class="btn btn-outline-light" href="{{ route('admin.jobs.index') }}">Jobs</a>
    <a class="btn btn-outline-light" href="{{ route('admin.applications.index') }}">Applications</a>
    <a class="btn btn-outline-light" href="{{ route('admin.inquiries.index') }}">Inquiries</a>
</div>
@endsection
