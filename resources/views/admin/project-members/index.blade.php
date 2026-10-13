@extends('layouts.admin')
@section('title','Project Team')
@section('content')
<section class="glass-card p-4">
    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
        <div>
            <h2 class="h5 mb-1">開発者・運営者</h2>
            <p class="text-secondary small mb-0">トップページに表示するメンバー、順番、公開状態を管理します。</p>
        </div>
        <a class="btn btn-primary" href="{{ route('admin.project-members.create') }}">メンバーを追加</a>
    </div>

    <div class="table-responsive">
        <table class="table table-darkish align-middle">
            <thead><tr><th>順番</th><th>写真</th><th>名前</th><th>役割</th><th>所属</th><th>公開</th><th></th></tr></thead>
            <tbody>
            @forelse($members as $member)
                @php($memberImage = \Illuminate\Support\Str::startsWith($member->image_path, 'images/') ? asset($member->image_path) : asset('storage/'.$member->image_path))
                <tr>
                    <td>{{ $member->sort_order }}</td>
                    <td><img src="{{ $memberImage }}" alt="" width="58" height="58" class="rounded-circle object-fit-cover border border-secondary"></td>
                    <td><strong>{{ $member->name }}</strong></td>
                    <td><span class="badge badge-soft">{{ $member->role }}</span></td>
                    <td>{{ $member->affiliation }}</td>
                    <td><span class="badge {{ $member->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $member->is_active ? '公開' : '非公開' }}</span></td>
                    <td class="text-end"><a class="btn btn-outline-light btn-sm" href="{{ route('admin.project-members.edit', $member) }}">編集</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-secondary">登録されているメンバーはいません。</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
