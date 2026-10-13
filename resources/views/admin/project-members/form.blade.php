@extends('layouts.admin')
@section('title', $member->exists ? 'Edit Team Member' : 'New Team Member')
@section('content')
@php
    $memberImage = $member->image_path
        ? (\Illuminate\Support\Str::startsWith($member->image_path, 'images/') ? asset($member->image_path) : asset('storage/'.$member->image_path))
        : null;
@endphp
<form class="glass-card p-4" method="post" enctype="multipart/form-data" action="{{ $member->exists ? route('admin.project-members.update', $member) : route('admin.project-members.store') }}">
    @csrf
    @if($member->exists)@method('PATCH')@endif

    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
        <div><h2 class="h5 mb-1">{{ $member->exists ? 'メンバー編集' : 'メンバー追加' }}</h2><p class="text-secondary small mb-0">所属は20文字以内、写真は正方形を推奨します。</p></div>
        <a class="btn btn-outline-light" href="{{ route('admin.project-members.index') }}">一覧へ戻る</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            @if($memberImage)<img id="member-image-preview" src="{{ $memberImage }}" alt="現在の写真" class="d-block w-100 border border-secondary mb-3" style="max-width:300px;aspect-ratio:1;object-fit:cover">@else<img id="member-image-preview" alt="写真プレビュー" class="d-none w-100 border border-secondary mb-3" style="max-width:300px;aspect-ratio:1;object-fit:cover">@endif
            <label class="form-label">顔写真</label>
            <input class="form-control" id="member-image-input" name="image" type="file" accept="image/jpeg,image/png,image/webp" {{ $member->exists ? '' : 'required' }}>
            <div class="form-text text-secondary">JPG・PNG・WebP / 最大5MB</div>
        </div>
        <div class="col-lg-8">
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">名前</label><input class="form-control" name="name" maxlength="40" value="{{ old('name', $member->name) }}" required></div>
                <div class="col-md-6"><label class="form-label">役割</label><input class="form-control" name="role" maxlength="40" value="{{ old('role', $member->role) }}" placeholder="PROJECT LEAD" required></div>
                <div class="col-12"><label class="form-label">所属 <span class="text-secondary small">20文字以内</span></label><input class="form-control" name="affiliation" maxlength="20" value="{{ old('affiliation', $member->affiliation) }}" required></div>
                <div class="col-md-6"><label class="form-label">表示順</label><input class="form-control" name="sort_order" type="number" min="0" max="999" value="{{ old('sort_order', $member->sort_order) }}" required></div>
                <div class="col-md-6 d-flex align-items-end"><div class="form-check form-switch mb-2"><input type="hidden" name="is_active" value="0"><input class="form-check-input" id="is-active" name="is_active" type="checkbox" value="1" {{ old('is_active', $member->is_active) ? 'checked' : '' }}><label class="form-check-label" for="is-active">トップページに公開</label></div></div>
                <div class="col-12"><button class="btn btn-primary" type="submit">{{ $member->exists ? '変更を保存' : 'メンバーを追加' }}</button></div>
            </div>
        </div>
    </div>
</form>

@if($member->exists)
<form class="mt-3 text-end" method="post" action="{{ route('admin.project-members.destroy', $member) }}" onsubmit="return confirm('このメンバーを削除しますか？');">
    @csrf @method('DELETE')
    <button class="btn btn-outline-danger" type="submit">メンバーを削除</button>
</form>
@endif

<script>
var memberImageInput = document.getElementById('member-image-input');
var memberImagePreview = document.getElementById('member-image-preview');
if (memberImageInput && memberImagePreview) {
    memberImageInput.addEventListener('change', function () {
        var file = this.files && this.files[0];
        if (!file) return;
        memberImagePreview.src = URL.createObjectURL(file);
        memberImagePreview.classList.remove('d-none');
    });
}
</script>
@endsection
