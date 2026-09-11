<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Notifications\MatchingActivityNotification;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Application $application)
    {
        $organization = auth()->user()->organization;
        abort_unless(auth()->user()->isOrganization() && $organization && $application->job->organization_id === $organization->id && $application->status === 'completed', 403);
        $data = $request->validate(['rating' => ['required', 'integer', 'between:1,5'], 'title' => ['required', 'max:255'], 'body' => ['required', 'max:3000']]);
        $review = $application->review()->updateOrCreate([], array_merge($data, ['organization_id' => $organization->id, 'coach_profile_id' => $application->coach_profile_id, 'status' => 'published']));
        optional($application->coachProfile->user)->notify(new MatchingActivityNotification('新しい評価', $organization->name.'から「'.$review->title.'」が届きました。', '/coaches/'.$application->coach_profile_id));
        return back()->with('status', '評価を公開しました。');
    }
}
