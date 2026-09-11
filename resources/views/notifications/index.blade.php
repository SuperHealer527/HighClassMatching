@extends('layouts.user')
@section('content')
<div class="page-heading"><div><div class="eyebrow">ACTIVITY</div><h1>通知</h1></div><form method="post" action="{{ route('notifications.read-all') }}">@csrf<button class="btn secondary" type="submit">すべて既読にする</button></form></div>
<div class="list">
@forelse($notifications as $notification)
    <a class="list-item notification-row {{ $notification->read_at ? '' : 'is-unread' }}" href="{{ route('notifications.read', $notification->id) }}">
        <div class="notification-dot"></div><div><h2>{{ $notification->data['title'] ?? 'お知らせ' }}</h2><p>{{ $notification->data['message'] ?? '' }}</p><p class="meta">{{ $notification->created_at->diffForHumans() }}</p></div>
    </a>
@empty
    <div class="list-item">新しい通知はありません。</div>
@endforelse
</div>
{{ $notifications->links() }}
@endsection
