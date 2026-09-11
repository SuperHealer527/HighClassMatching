<?php

namespace App\Http\Controllers;

use App\Models\CoachProfile;

class FavoriteController extends Controller
{
    public function toggle(CoachProfile $coach)
    {
        $organization = auth()->user()->organization;
        abort_unless(auth()->user()->isOrganization() && $organization && $organization->isApproved(), 403);
        $result = $organization->favoriteCoaches()->toggle($coach->id);
        $saved = count($result['attached']) > 0;
        return back()->with('status', $saved ? '関心指導者に保存しました。' : '保存を解除しました。');
    }
}
