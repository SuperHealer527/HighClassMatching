<?php

namespace App\Http\Controllers;

use App\Models\CoachProfile;
use App\Models\Job;
use App\Models\Organization;
use App\Models\User;
use App\Models\Review;
use App\Notifications\MatchingActivityNotification;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function updateUserStatus(Request $request, User $user)
    {
        $data = $request->validate(['status' => ['required', 'in:pending,approved,rejected,suspended']]);
        abort_if($user->id === auth()->id() && $data['status'] !== 'approved', 422, '自分の管理者アカウントは停止できません。');
        $user->update($data);
        if (in_array($data['status'], ['rejected', 'suspended'], true)) {
            optional($user->coachProfile)->update(['status' => $data['status']]);
            optional($user->organization)->update(['status' => $data['status']]);
        }
        $user->notify(new MatchingActivityNotification('アカウント状態の更新', 'アカウント状態が「'.$data['status'].'」に更新されました。'));
        return back()->with('status', 'ユーザー状態を更新しました。');
    }

    public function updateCoachStatus(Request $request, CoachProfile $coach)
    {
        $data = $request->validate(['status' => ['required', 'in:pending,approved,rejected,suspended']]);
        if ($data['status'] === 'approved') abort_unless($coach->verification_status === 'verified', 422, '本人確認と資格確認を先に完了してください。');
        $coach->update($data);
        optional($coach->user)->update(['status' => $data['status']]);
        optional($coach->user)->notify(new MatchingActivityNotification('指導者プロフィール状態の更新', 'プロフィール状態が「'.$data['status'].'」に更新されました。', '/dashboard'));
        return back()->with('status', '指導者状態を更新しました。');
    }

    public function verifyCoach(Request $request, CoachProfile $coach)
    {
        $data = $request->validate(['verification_status' => ['required', 'in:verified,rejected']]);
        if ($data['verification_status'] === 'verified') {
            abort_unless(($coach->identity_document_path && $coach->qualification_document_path) || $coach->verification_status === 'verified', 422, '確認書類が2点必要です。');
        }
        $coach->update($data);
        optional($coach->user)->notify(new MatchingActivityNotification('本人・資格確認の更新', '確認状態が「'.$data['verification_status'].'」になりました。', '/dashboard'));
        return back()->with('status', '確認状態を更新しました。');
    }

    public function updateOrganizationStatus(Request $request, Organization $organization)
    {
        $data = $request->validate(['status' => ['required', 'in:pending,approved,rejected,suspended']]);
        $organization->update($data);
        optional($organization->user)->update(['status' => $data['status']]);
        optional($organization->user)->notify(new MatchingActivityNotification('団体プロフィール状態の更新', '団体状態が「'.$data['status'].'」に更新されました。', '/dashboard'));
        return back()->with('status', '団体状態を更新しました。');
    }

    public function updateJobStatus(Request $request, Job $job)
    {
        $data = $request->validate(['status' => ['required', 'in:draft,pending_review,published,closed,suspended']]);
        $job->update([
            'status' => $data['status'],
            'publish_start_at' => $data['status'] === 'published' ? ($job->publish_start_at ?: now()->toDateString()) : $job->publish_start_at,
        ]);
        optional($job->organization->user)->notify(new MatchingActivityNotification('案件状態の更新', '「'.$job->title.'」が「'.$data['status'].'」に更新されました。', '/jobs/'.$job->id));
        return back()->with('status', '案件状態を更新しました。');
    }

    public function updateReviewStatus(Request $request, Review $review)
    {
        $data=$request->validate(['status'=>['required','in:published,hidden']]);
        $review->update($data);
        return back()->with('status','評価の公開状態を更新しました。');
    }

    public function approveUser(User $user)
    {
        $user->update(['status' => 'approved']);
        $user->notify(new MatchingActivityNotification('アカウント承認', 'アカウントが承認されました。プロフィールや案件機能をご利用いただけます。'));
        return back()->with('status', 'ユーザーを承認しました。');
    }

    public function approveCoach(CoachProfile $coach)
    {
        abort_unless($coach->verification_status === 'verified', 422, '本人確認と資格確認を先に完了してください。');
        $coach->update(['status' => 'approved']);
        optional($coach->user)->update(['status' => 'approved']);
        optional($coach->user)->notify(new MatchingActivityNotification('指導者プロフィール承認', '指導者プロフィールが承認され、公開されました。', '/coaches/'.$coach->id));
        return back()->with('status', '指導者を承認しました。');
    }

    public function approveOrganization(Organization $organization)
    {
        $organization->update(['status' => 'approved']);
        optional($organization->user)->update(['status' => 'approved']);
        optional($organization->user)->notify(new MatchingActivityNotification('団体プロフィール承認', '団体プロフィールが承認されました。案件を登録できます。', '/organizations/'.$organization->id));
        return back()->with('status', '団体を承認しました。');
    }
    public function approveJob(Job $job)
    {
        abort_unless($job->organization && $job->organization->isApproved(), 403);

        $job->update([
            'status' => 'published',
            'publish_start_at' => $job->publish_start_at ?: now()->toDateString(),
        ]);

        optional($job->organization->user)->notify(new MatchingActivityNotification('案件公開', '「'.$job->title.'」が承認され、公開されました。', '/jobs/'.$job->id));

        return back()->with('status', '案件を公開しました。');
    }
}
