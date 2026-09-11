<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\CoachProfile;
use App\Models\Job;
use App\Models\Organization;
use App\Models\User;
use App\Models\Inquiry;
use App\Services\MatchingService;
use App\Models\Offer;
use App\Models\Review;
use App\Models\Article;

class DashboardController extends Controller
{
    public function __invoke(MatchingService $matching)
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return request()->routeIs('admin.dashboard')
                ? view('dashboard.admin', [
                    'pendingUsers' => User::where('status', 'pending')->count(),
                    'pendingCoaches' => CoachProfile::where('status', 'pending')->count(),
                    'pendingOrganizations' => Organization::where('status', 'pending')->count(),
                    'pendingJobs' => Job::where('status', 'pending_review')->count(),
                    'publishedJobs' => Job::where('status', 'published')->count(),
                    'openInquiries' => Inquiry::whereIn('status', ['open', 'in_progress'])->count(),
                    'offerCount' => Offer::count(),
                    'reviewCount' => Review::where('status','published')->count(),
                    'articleCount' => Article::where('status','published')->count(),
                    'pendingJobList' => Job::with('organization')->where('status', 'pending_review')->latest()->get(),
                    'applications' => Application::with('job.organization', 'coachProfile')->latest()->take(20)->get(),
                ])
                : redirect()->route('admin.dashboard');
        }

        $coach = $user->coachProfile;
        $organization = $user->organization;
        $latestJob = $organization ? $organization->jobs()->latest()->first() : null;

        return view('dashboard.member', [
            'user' => $user,
            'coach' => $coach,
            'organization' => $organization,
            'applications' => $coach ? $coach->applications()->with('job.organization', 'statusHistories.changedBy')->latest()->get() : collect(),
            'jobs' => $organization ? $organization->jobs()->with('applications.coachProfile', 'applications.statusHistories.changedBy', 'applications.review')->latest()->get() : collect(),
            'offers' => $coach ? $coach->offers()->with('organization', 'job')->latest()->take(5)->get() : ($organization ? $organization->offers()->with('coachProfile', 'job')->latest()->take(5)->get() : collect()),
            'favoriteCoaches' => $organization ? $organization->favoriteCoaches()->publiclyVisible()->take(6)->get() : collect(),
            'recommendations' => $coach ? $matching->jobsForCoach($coach) : ($latestJob ? $matching->coachesForJob($latestJob) : collect()),
            'recommendationContext' => $latestJob,
            'inquiryCount' => $user->inquiries()->whereIn('status', ['open', 'in_progress'])->count(),
            'unreadNotificationCount' => $user->unreadNotifications()->count(),
        ]);
    }
}
