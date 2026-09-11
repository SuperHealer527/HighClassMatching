@extends('layouts.admin')
@section('title','Jobs')
@section('content')
<section class="glass-card p-4">
    <div class="d-flex justify-content-between gap-3 flex-wrap mb-4"><form class="row g-2 flex-grow-1" method="get">
        <div class="col-md"><input class="form-control" name="keyword" value="{{ request('keyword') }}" placeholder="Title, type, prefecture, sport"></div>
        <div class="col-md-3"><select class="form-select" name="status"><option value="">All status</option>@foreach(['draft','pending_review','published','closed','suspended'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>@endforeach</select></div>
        <div class="col-md-2"><button class="btn btn-primary w-100" type="submit">Search</button></div>
    </form><a class="btn btn-outline-light" href="{{ route('admin.export','jobs') }}">CSV Export</a></div>
    <div class="table-responsive"><table class="table table-darkish align-middle"><thead><tr><th>ID</th><th>&#26696;&#20214;</th><th>&#22243;&#20307;</th><th>&#37117;&#36947;&#24220;&#30476;</th><th>Apps</th><th>Status</th><th></th></tr></thead><tbody>
        @forelse($jobs as $job)
            <tr><td>{{ $job->id }}</td><td><strong>{{ $job->title }}</strong><div class="text-secondary small">{{ $job->job_type }} / {{ $job->sport }}</div></td><td>{{ optional($job->organization)->name }}</td><td>{{ $job->prefecture }} {{ $job->area }}</td><td>{{ $job->applications_count }}</td><td><span class="badge badge-soft">{{ $job->status }}</span></td><td><form method="post" action="{{ route('admin.jobs.status',$job) }}" class="d-flex gap-2">@csrf @method('PATCH')<select class="form-select form-select-sm" name="status">@foreach(['draft','pending_review','published','closed','suspended'] as $status)<option value="{{ $status }}" @selected($job->status===$status)>{{ $status }}</option>@endforeach</select><button class="btn btn-primary btn-sm" type="submit">Update</button></form></td></tr>
        @empty
            <tr><td colspan="7" class="text-secondary">&#26696;&#20214;&#12487;&#12540;&#12479;&#12399;&#12354;&#12426;&#12414;&#12379;&#12435;&#12290;</td></tr>
        @endforelse
    </tbody></table></div>
    {{ $jobs->links() }}
</section>
@endsection
