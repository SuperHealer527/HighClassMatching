<?php

namespace App\Http\Controllers;

use App\Models\ProfileRevisionRequest;
use App\Notifications\MatchingActivityNotification;

class ProfileRevisionRequestController extends Controller
{
    public function complete(ProfileRevisionRequest $profileRevisionRequest)
    {
        abort_unless(
            $profileRevisionRequest->coachProfile
                && $profileRevisionRequest->coachProfile->user_id === auth()->id(),
            403
        );

        $profileRevisionRequest->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        optional($profileRevisionRequest->requestedBy)->notify(new MatchingActivityNotification(
            'プロフィール修正完了の連絡',
            $profileRevisionRequest->coachProfile->name.'さんが修正依頼を完了しました。',
            '/admin/coaches/'.$profileRevisionRequest->coach_profile_id
        ));

        return back()->with('status', '修正完了を運営へ連絡しました。');
    }
}
