@extends('layouts.user')
@section('content')
<div class="page-heading"><div><div class="eyebrow">SUPPORT DESK</div><h1>問い合わせ履歴</h1></div><a class="btn" href="{{ route('inquiries.create') }}">新しい問い合わせ</a></div>
<div class="list">
@forelse($inquiries as $inquiry)
    <a class="list-item inquiry-row" href="{{ route('inquiries.show', $inquiry) }}">
        <div><span class="badge status">{{ $inquiry->status }}</span><span class="badge">{{ $inquiry->category }}</span></div>
        <h2>{{ $inquiry->subject }}</h2>
        <p class="meta">{{ $inquiry->created_at->format('Y/m/d H:i') }} @if($inquiry->coachProfile) / 相談先: {{ $inquiry->coachProfile->name }} @endif</p>
    </a>
@empty
    <div class="list-item">問い合わせ履歴はありません。</div>
@endforelse
</div>
{{ $inquiries->links() }}
@endsection
