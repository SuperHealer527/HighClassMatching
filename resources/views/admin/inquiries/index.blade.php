@extends('layouts.admin')
@section('title','Inquiries')
@section('content')
<section class="glass-card p-4">
    <div class="d-flex justify-content-between gap-3 flex-wrap mb-4"><form class="row g-2 flex-grow-1" method="get"><div class="col-md-4"><input class="form-control" name="keyword" value="{{ request('keyword') }}" placeholder="件名・本文・ユーザー名"></div><div class="col-md-3"><select class="form-select" name="status"><option value="">All status</option>@foreach(['open','in_progress','resolved','closed'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>@endforeach</select></div><div class="col-md-2"><button class="btn btn-primary w-100" type="submit">Search</button></div></form><a class="btn btn-outline-light" href="{{ route('admin.export','inquiries') }}">CSV Export</a></div>
    <div class="table-responsive"><table class="table table-darkish align-middle"><thead><tr><th>ID</th><th>受付日時</th><th>ユーザー</th><th>種別</th><th>件名</th><th>Status</th><th></th></tr></thead><tbody>
    @forelse($inquiries as $inquiry)<tr><td>{{ $inquiry->id }}</td><td>{{ $inquiry->created_at->format('Y/m/d H:i') }}</td><td>{{ optional($inquiry->user)->name }}</td><td>{{ $inquiry->category }}</td><td>{{ $inquiry->subject }}</td><td><span class="badge badge-soft">{{ $inquiry->status }}</span></td><td><a class="btn btn-primary btn-sm" href="{{ route('admin.inquiries.show',$inquiry) }}">Open</a></td></tr>@empty<tr><td colspan="7" class="text-secondary">問い合わせはありません。</td></tr>@endforelse
    </tbody></table></div>{{ $inquiries->links() }}
</section>
@endsection
