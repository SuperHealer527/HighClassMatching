<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\CoachProfile;
use App\Models\Job;
use App\Models\Organization;
use App\Models\User;
use App\Models\Offer;
use App\Models\Review;
use Illuminate\Http\Request;

class AdminManagementController extends Controller
{
    public function coaches(Request $request)
    {
        $query = CoachProfile::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('main_prefecture', 'like', "%{$keyword}%")
                    ->orWhere('fields', 'like', "%{$keyword}%")
                    ->orWhere('sports', 'like', "%{$keyword}%");
            });
        }

        return view('admin.coaches.index', [
            'coaches' => $query->latest()->paginate(15)->withQueryString(),
        ]);
    }

    public function organizations(Request $request)
    {
        $query = Organization::with('user')->withCount('jobs');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('main_prefecture', 'like', "%{$keyword}%")
                    ->orWhere('sport', 'like', "%{$keyword}%");
            });
        }

        return view('admin.organizations.index', [
            'organizations' => $query->latest()->paginate(15)->withQueryString(),
        ]);
    }

    public function jobs(Request $request)
    {
        $query = Job::with('organization')->withCount('applications');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('job_type', 'like', "%{$keyword}%")
                    ->orWhere('prefecture', 'like', "%{$keyword}%")
                    ->orWhere('sport', 'like', "%{$keyword}%");
            });
        }

        return view('admin.jobs.index', [
            'jobs' => $query->latest()->paginate(15)->withQueryString(),
        ]);
    }

    public function applications(Request $request)
    {
        $query = Application::with('job.organization', 'coachProfile', 'statusHistories.changedBy');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->whereHas('job', fn ($job) => $job->where('title', 'like', "%{$keyword}%"))
                    ->orWhereHas('coachProfile', fn ($coach) => $coach->where('name', 'like', "%{$keyword}%"));
            });
        }

        return view('admin.applications.index', [
            'applications' => $query->latest()->paginate(15)->withQueryString(),
        ]);
    }

    public function users(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        return view('admin.users.index', [
            'users' => $query->latest()->paginate(20)->withQueryString(),
        ]);
    }

    public function offers(Request $request)
    {
        $query=Offer::with('organization','coachProfile','job');
        if($request->filled('status')) $query->where('status',$request->status);
        return view('admin.offers.index',['offers'=>$query->latest()->paginate(20)->withQueryString()]);
    }

    public function reviews(Request $request)
    {
        $query=Review::with('organization','coachProfile','application.job');
        if($request->filled('status')) $query->where('status',$request->status);
        return view('admin.reviews.index',['reviews'=>$query->latest()->paginate(20)->withQueryString()]);
    }
}
