<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Job;
use App\Notifications\MatchingActivityNotification;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function store(Request $request, Job $job)
    {
        abort_unless(auth()->user()->isCoach() && auth()->user()->isApproved() && $job->isPublished(), 403);

        $coach = auth()->user()->coachProfile;
        if (!$coach || !$coach->isApproved()) {
            return redirect('/coaches/create')->with('status', '応募前に指導者プロフィールを登録してください。');
        }

        if (Application::where('job_id', $job->id)->where('coach_profile_id', $coach->id)->exists()) {
            return back()->with('status', 'この案件には応募済みです。');
        }

        $data = $request->validate([
            'message' => ['nullable'],
            'available_schedule' => ['nullable'],
            'condition_note' => ['nullable'],
            'attachment_url' => ['nullable', 'url'],
        ]);

        $data['job_id'] = $job->id;
        $data['coach_profile_id'] = $coach->id;
        $data['status'] = 'applied';
        $application = Application::create($data);
        $application->statusHistories()->create([
            'changed_by' => auth()->id(),
            'to_status' => 'applied',
            'note' => '指導者が案件に応募しました。',
        ]);

        if ($job->organization->user) {
            $job->organization->user->notify(new MatchingActivityNotification(
                '案件に新しい応募がありました',
                $coach->name.' 様が「'.$job->title.'」に応募しました。',
                '/dashboard#application-'.$application->id
            ));
        }

        return redirect('/dashboard')->with('status', '案件へ応募しました。');
    }

    public function update(Request $request, Application $application)
    {
        $job = $application->job()->with('organization')->firstOrFail();
        $user = auth()->user();

        $ownsJob = $user->isOrganization() && $job->organization->user_id === $user->id;
        $ownsApplication = $user->isCoach() && $application->coachProfile->user_id === $user->id;
        abort_unless($user->isAdmin() || $ownsJob || $ownsApplication, 403);

        $data = $request->validate([
            'status' => ['required', 'in:applied,organization_review,interview,accepted,rejected,withdrawn,completed,canceled'],
            'note' => ['nullable', 'max:1000'],
        ]);

        if ($ownsApplication) {
            abort_unless($data['status'] === 'withdrawn' && !in_array($application->status, ['rejected', 'withdrawn', 'completed', 'canceled'], true), 403);
        }

        $oldStatus = $application->status;
        if ($oldStatus === $data['status']) {
            return back()->with('status', '応募ステータスに変更はありません。');
        }

        $application->update(['status' => $data['status']]);
        $application->statusHistories()->create([
            'changed_by' => $user->id,
            'from_status' => $oldStatus,
            'to_status' => $data['status'],
            'note' => $data['note'] ?? null,
        ]);

        $recipient = $ownsApplication ? $job->organization->user : $application->coachProfile->user;
        if ($recipient) {
            $recipient->notify(new MatchingActivityNotification(
                '応募ステータスが更新されました',
                '「'.$job->title.'」が'.config('matching.application_statuses.'.$data['status'], $data['status']).'になりました。',
                '/dashboard#application-'.$application->id
            ));
        }

        return back()->with('status', '応募ステータスを更新しました。');
    }
}
