@extends('layouts.admin')
@section('title','Users')
@section('content')
<section class="glass-card p-4">
    <div class="d-flex justify-content-between gap-3 flex-wrap mb-4"><form class="row g-2 flex-grow-1" method="get">
        <div class="col-md"><input class="form-control" name="keyword" value="{{ request('keyword') }}" placeholder="Name or email"></div>
        <div class="col-md-3"><select class="form-select" name="role"><option value="">All roles</option>@foreach(['admin','coach','organization'] as $role)<option value="{{ $role }}" @selected(request('role') === $role)>{{ $role }}</option>@endforeach</select></div>
        <div class="col-md-3"><select class="form-select" name="status"><option value="">All status</option>@foreach(['pending','approved','rejected','suspended'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>@endforeach</select></div>
        <div class="col-md-2"><button class="btn btn-primary w-100" type="submit">Search</button></div>
    </form><a class="btn btn-outline-light" href="{{ route('admin.export','users') }}">CSV Export</a></div>
    <div class="table-responsive"><table class="table table-darkish align-middle"><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th></th></tr></thead><tbody>
        @forelse($users as $user)
            <tr><td>{{ $user->id }}</td><td>{{ $user->name }}</td><td>{{ $user->email }}</td><td>{{ $user->role }}</td><td><span class="badge badge-soft">{{ $user->status }}</span></td><td><form method="post" action="{{ route('admin.users.status',$user) }}" class="d-flex gap-2">@csrf @method('PATCH')<select class="form-select form-select-sm" name="status">@foreach(['pending','approved','rejected','suspended'] as $status)<option value="{{ $status }}" @selected($user->status===$status)>{{ $status }}</option>@endforeach</select><button class="btn btn-primary btn-sm" type="submit">Update</button></form></td></tr>
        @empty
            <tr><td colspan="6" class="text-secondary">No users.</td></tr>
        @endforelse
    </tbody></table></div>
    {{ $users->links() }}
</section>
@endsection
