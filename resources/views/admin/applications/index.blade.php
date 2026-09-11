@extends('layouts.admin')
@section('title','Applications')
@section('content')
<section class="glass-card p-4">
    <div class="d-flex justify-content-between gap-3 flex-wrap mb-4"><form class="row g-2 flex-grow-1" method="get">
        <div class="col-md"><input class="form-control" name="keyword" value="{{ request('keyword') }}" placeholder="Job or coach"></div>
        <div class="col-md-4"><select class="form-select" name="status"><option value="">All status</option>@foreach(['applied','organization_review','interview','accepted','rejected','withdrawn','completed','canceled'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>@endforeach</select></div>
        <div class="col-md-2"><button class="btn btn-primary w-100" type="submit">Search</button></div>
    </form><a class="btn btn-outline-light" href="{{ route('admin.export','applications') }}">CSV Export</a></div>
    <div class="table-responsive"><table class="table table-darkish align-middle"><thead><tr><th>ID</th><th>&#26696;&#20214;</th><th>&#25351;&#23566;&#32773;</th><th>&#22243;&#20307;</th><th>Status</th><th></th></tr></thead><tbody>
        @forelse($applications as $application)
            <tr><td>{{ $application->id }}</td><td>{{ $application->job->title }}<div class="text-secondary small">{{ $application->statusHistories->count() }} history events</div></td><td>{{ $application->coachProfile->name }}</td><td>{{ $application->job->organization->name }}</td><td><span class="badge badge-soft">{{ config('matching.application_statuses.'.$application->status,$application->status) }}</span></td><td><form method="post" action="{{ route('applications.update',$application) }}" class="d-flex gap-2">@csrf @method('PATCH')<select class="form-select form-select-sm" name="status">@foreach(config('matching.application_statuses') as $status => $label)<option value="{{ $status }}" @selected($application->status === $status)>{{ $label }}</option>@endforeach</select><button class="btn btn-primary btn-sm" type="submit">&#26356;&#26032;</button></form></td></tr>
        @empty
            <tr><td colspan="6" class="text-secondary">&#24540;&#21215;&#12487;&#12540;&#12479;&#12399;&#12354;&#12426;&#12414;&#12379;&#12435;&#12290;</td></tr>
        @endforelse
    </tbody></table></div>
    {{ $applications->links() }}
</section>
@endsection
