@extends('layouts.admin')
@section('title','Coaches')
@section('content')
<section class="glass-card p-4">
    <div class="d-flex justify-content-between gap-3 flex-wrap mb-4"><form class="row g-2 flex-grow-1" method="get">
        <div class="col-md"><input class="form-control" name="keyword" value="{{ request('keyword') }}" placeholder="Name, prefecture, field"></div>
        <div class="col-md-3"><select class="form-select" name="status"><option value="">All status</option>@foreach(['pending','approved','rejected','suspended'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>@endforeach</select></div>
        <div class="col-md-2"><button class="btn btn-primary w-100" type="submit">Search</button></div>
    </form><a class="btn btn-outline-light" href="{{ route('admin.export','coaches') }}">CSV Export</a></div>
    <div class="table-responsive">
        <table class="table table-darkish align-middle">
            <thead><tr><th>ID</th><th>&#25351;&#23566;&#32773;</th><th>&#37117;&#36947;&#24220;&#30476;</th><th>Fields</th><th>Verification</th><th>Status</th><th>User</th><th></th></tr></thead>
            <tbody>
            @forelse($coaches as $coach)
                <tr><td>{{ $coach->id }}</td><td><strong>{{ $coach->name }}</strong><div class="text-secondary small">{{ $coach->kana }} / {{ $coach->roman_name }}</div><a class="small" href="{{ route('coaches.show',$coach) }}">Public profile</a></td><td>{{ $coach->main_prefecture }}</td><td>{{ implode(' / ', (array)$coach->fields) }}</td><td><span class="badge badge-soft">{{ $coach->verification_status }}</span><div class="d-flex gap-1 my-2">@if($coach->identity_document_path)<a class="btn btn-outline-light btn-sm" href="{{ route('coaches.documents.download',[$coach,'identity']) }}">ID</a>@endif @if($coach->qualification_document_path)<a class="btn btn-outline-light btn-sm" href="{{ route('coaches.documents.download',[$coach,'qualification']) }}">Cert</a>@endif</div><form method="post" action="{{ route('admin.coaches.verify',$coach) }}" class="d-flex gap-1">@csrf @method('PATCH')<select class="form-select form-select-sm" name="verification_status"><option value="verified">verified</option><option value="rejected">rejected</option></select><button class="btn btn-primary btn-sm">Set</button></form></td><td><span class="badge badge-soft">{{ $coach->status }}</span></td><td>{{ optional($coach->user)->email }}</td><td><form method="post" action="{{ route('admin.coaches.status',$coach) }}" class="d-flex gap-2">@csrf @method('PATCH')<select class="form-select form-select-sm" name="status">@foreach(['pending','approved','rejected','suspended'] as $status)<option value="{{ $status }}" @selected($coach->status===$status)>{{ $status }}</option>@endforeach</select><button class="btn btn-primary btn-sm" type="submit">Update</button></form></td></tr>
            @empty
                <tr><td colspan="8" class="text-secondary">&#25351;&#23566;&#32773;&#12487;&#12540;&#12479;&#12399;&#12354;&#12426;&#12414;&#12379;&#12435;&#12290;</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $coaches->links() }}
</section>
@endsection
