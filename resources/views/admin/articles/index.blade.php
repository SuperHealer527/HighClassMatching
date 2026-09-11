@extends('layouts.admin')
@section('title','Articles')
@section('content')
<section class="glass-card p-4"><div class="d-flex justify-content-between align-items-center mb-4"><h2 class="h5 m-0">Editorial content</h2><a class="btn btn-primary" href="{{ route('admin.articles.create') }}">New Article</a></div><div class="table-responsive"><table class="table table-darkish"><thead><tr><th>Title</th><th>Category</th><th>Status</th><th>Published</th><th></th></tr></thead><tbody>@foreach($articles as $article)<tr><td>{{ $article->title }}</td><td>{{ $article->category }}</td><td><span class="badge badge-soft">{{ $article->status }}</span></td><td>{{ optional($article->published_at)->format('Y/m/d') }}</td><td><a class="btn btn-outline-light btn-sm" href="{{ route('admin.articles.edit',$article) }}">Edit</a></td></tr>@endforeach</tbody></table></div>{{ $articles->links() }}</section>
@endsection
