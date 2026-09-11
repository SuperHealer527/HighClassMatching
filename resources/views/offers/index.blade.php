@extends('layouts.user')
@section('content')
<div class="page-heading"><div><div class="eyebrow">DIRECT OFFERS</div><h1>オファー管理</h1></div><a class="btn secondary" href="{{ route('dashboard') }}">マイページへ</a></div>
<div class="result-grid">
@forelse($offers as $offer)
<article class="result-card"><div class="result-card-body"><span class="badge status">{{ $offer->status }}</span><h2>{{ $offer->subject }}</h2><p><strong>{{ auth()->user()->isCoach() ? $offer->organization->name : $offer->coachProfile->name }}</strong></p>@if($offer->job)<p class="meta">関連案件: {{ $offer->job->title }}</p>@endif<p class="preline">{{ $offer->message }}</p>@if($offer->proposed_schedule)<p>希望時期: {{ $offer->proposed_schedule }}</p>@endif
@if($offer->status === 'sent')<div class="page-actions">@if(auth()->user()->isCoach())<form method="post" action="{{ route('offers.update',$offer) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="accepted"><button class="btn" type="submit">受諾する</button></form><form method="post" action="{{ route('offers.update',$offer) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="declined"><button class="btn secondary" type="submit">辞退する</button></form>@else<form method="post" action="{{ route('offers.update',$offer) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="withdrawn"><button class="btn secondary" type="submit">取り下げる</button></form>@endif</div>@endif
@if($offer->status === 'accepted')<div class="contact-box"><strong>連絡先が開示されました</strong>@if(auth()->user()->isCoach())<p>{{ $offer->organization->manager_email }} / {{ $offer->organization->manager_phone }}</p>@else<p>{{ $offer->coachProfile->email }} / {{ $offer->coachProfile->phone }}</p>@endif</div>@endif
</div></article>
@empty<div class="empty-state">オファーはまだありません。</div>@endforelse
</div>
@if(method_exists($offers,'links')){{ $offers->links() }}@endif
@endsection
