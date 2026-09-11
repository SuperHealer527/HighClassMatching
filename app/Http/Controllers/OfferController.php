<?php

namespace App\Http\Controllers;

use App\Models\CoachProfile;
use App\Models\Offer;
use App\Notifications\MatchingActivityNotification;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        abort_unless($user->isCoach() || $user->isOrganization(), 403);
        $profile = $user->isCoach() ? $user->coachProfile : $user->organization;
        $offers = $profile
            ? $profile->offers()->with($user->isCoach() ? ['organization', 'job'] : ['coachProfile', 'job'])->latest()->paginate(15)
            : null;

        return view('offers.index', ['offers' => $offers ?: collect()]);
    }

    public function create(CoachProfile $coach)
    {
        $organization = auth()->user()->organization;
        abort_unless(auth()->user()->isOrganization() && auth()->user()->isApproved() && $organization && $organization->isApproved() && $coach->isApproved(), 403);

        return view('offers.create', [
            'coach' => $coach,
            'organization' => $organization,
            'jobs' => $organization->jobs()->where('status', 'published')->latest()->get(),
        ]);
    }

    public function store(Request $request, CoachProfile $coach)
    {
        $organization = auth()->user()->organization;
        abort_unless(auth()->user()->isOrganization() && auth()->user()->isApproved() && $organization && $organization->isApproved() && $coach->isApproved(), 403);
        $data = $request->validate([
            'job_id' => ['nullable', 'exists:jobs,id'],
            'subject' => ['required', 'max:255'],
            'message' => ['required', 'max:5000'],
            'proposed_schedule' => ['nullable', 'max:255'],
        ]);
        if (!empty($data['job_id'])) abort_unless($organization->jobs()->whereKey($data['job_id'])->exists(), 403);
        $offer = $organization->offers()->create(array_merge($data, ['coach_profile_id' => $coach->id, 'status' => 'sent']));
        optional($coach->user)->notify(new MatchingActivityNotification('新しいオファー', $organization->name.'から「'.$offer->subject.'」が届きました。', '/offers'));
        return redirect()->route('offers.index')->with('status', '指導者へオファーを送信しました。');
    }

    public function update(Request $request, Offer $offer)
    {
        $user = auth()->user();
        $isCoach = $user->isCoach() && $offer->coachProfile->user_id === $user->id;
        $isOrganization = $user->isOrganization() && $offer->organization->user_id === $user->id;
        abort_unless($isCoach || $isOrganization, 403);
        $allowed = $isCoach ? 'accepted,declined' : 'withdrawn';
        $data = $request->validate(['status' => ['required', 'in:'.$allowed]]);
        abort_unless($offer->status === 'sent', 422);
        $offer->update(['status' => $data['status'], 'responded_at' => now()]);
        $recipient = $isCoach ? $offer->organization->user : $offer->coachProfile->user;
        optional($recipient)->notify(new MatchingActivityNotification('オファー状態の更新', '「'.$offer->subject.'」が「'.$data['status'].'」になりました。', '/offers'));
        return back()->with('status', 'オファーを更新しました。');
    }
}
