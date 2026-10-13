<?php

namespace App\Http\Controllers;

use App\Models\CoachProfile;
use App\Models\ProfileRevisionRequest;
use App\Notifications\MatchingActivityNotification;
use Illuminate\Http\Request;

class AdminProfileRevisionRequestController extends Controller
{
    public function store(Request $request, CoachProfile $coach)
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $revisionRequest = $coach->revisionRequests()->create([
            'requested_by' => auth()->id(),
            'message' => $data['message'],
            'status' => 'open',
        ]);

        optional($coach->user)->notify(new MatchingActivityNotification(
            'プロフィール修正のお願い',
            $revisionRequest->message,
            '/dashboard'
        ));

        return back()->with('status', 'プロフィール修正依頼をコーチへ送信しました。');
    }

    public function update(Request $request, ProfileRevisionRequest $profileRevisionRequest)
    {
        $data = $request->validate([
            'status' => ['required', 'in:open,completed'],
        ]);

        $profileRevisionRequest->update([
            'status' => $data['status'],
            'completed_at' => $data['status'] === 'completed' ? now() : null,
        ]);

        return back()->with('status', '修正依頼の状態を更新しました。');
    }
}
