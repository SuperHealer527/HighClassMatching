<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Organization;
use App\Models\User;
use App\Notifications\MatchingActivityNotification;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = Job::with('organization')->publiclyVisible();

        foreach (['job_type', 'prefecture', 'sport', 'target_age', 'request_frequency', 'request_style'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->$filter);
            }
        }
        if ($request->filled('area')) $query->where('area', 'like', '%'.$request->area.'%');
        if ($request->filled('qualification')) $query->where('required_conditions', 'like', '%'.$request->qualification.'%');
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('detail', 'like', "%{$keyword}%")
                    ->orWhere('required_conditions', 'like', "%{$keyword}%");
            });
        }

        switch ($request->get('sort')) {
            case 'deadline':
                $query->orderBy('publish_end_at');
                break;
            case 'popular':
                $query->withCount('applications')->orderByDesc('applications_count');
                break;
            default:
                $query->latest('publish_start_at');
        }

        return view('jobs.index', [
            'jobs' => $query->paginate(in_array((int)$request->per_page, [10,20,50]) ? (int)$request->per_page : 10)->withQueryString(),
            'prefectures' => config('matching.prefectures'),
            'fields' => config('matching.fields'),
        ]);
    }

    public function show(Job $job)
    {
        $job->load('organization');
        $user = auth()->user();
        if (!$job->isPublished() && (!$user || (!$user->isAdmin() && $job->organization->user_id !== $user->id))) {
            abort(404);
        }

        $relatedJobs = Job::with('organization')->publiclyVisible()
            ->where('id', '!=', $job->id)
            ->where(fn ($query) => $query->where('sport', $job->sport)->orWhere('prefecture', $job->prefecture))
            ->latest('publish_start_at')->take(3)->get();

        return view('jobs.show', compact('job', 'relatedJobs'));
    }

    public function create()
    {
        abort_unless(auth()->user()->isOrganization() || auth()->user()->isAdmin(), 403);

        return view('jobs.create', [
            'organizations' => $this->availableOrganizations(),
            'prefectures' => config('matching.prefectures'),
            'fields' => config('matching.fields'),
        ]);
    }

    public function edit(Job $job)
    {
        abort_unless($this->canUseOrganization($job->organization), 403);
        return view('jobs.create', [
            'job' => $job,
            'organizations' => collect([$job->organization]),
            'prefectures' => config('matching.prefectures'),
            'fields' => config('matching.fields'),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->isOrganization() || auth()->user()->isAdmin(), 403);

        $data = $request->validate([
            'organization_id' => ['required', 'exists:organizations,id'],
            'title' => ['required', 'max:255'],
            'job_type' => ['required', 'max:255'],
            'prefecture' => ['required'],
            'area' => ['nullable', 'max:255'],
            'required_conditions' => ['nullable'],
            'target_age' => ['nullable', 'max:255'],
            'gender' => ['nullable', 'max:50'],
            'sport' => ['nullable', 'max:255'],
            'request_frequency' => ['nullable', 'max:255'],
            'request_style' => ['nullable', 'max:255'],
            'role_description' => ['nullable'],
            'detail' => ['nullable'],
            'notes' => ['nullable'],
            'publish_end_at' => ['nullable', 'date'],
            'budget_note' => ['nullable', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $organization = Organization::findOrFail($data['organization_id']);
        abort_unless($this->canUseOrganization($organization), 403);

        if ($request->hasFile('image')) $data['image_path'] = $request->file('image')->store('job-images', 'public');
        $data['status'] = auth()->user()->isAdmin() ? 'published' : 'pending_review';
        $data['publish_start_at'] = auth()->user()->isAdmin() ? now()->toDateString() : null;
        $job = Job::create($data);
        if (!auth()->user()->isAdmin()) {
            User::where('role', 'admin')->get()->each->notify(new MatchingActivityNotification('案件公開審査', '「'.$job->title.'」が審査待ちです。', '/admin/jobs'));
        }

        return redirect('/jobs')->with('status', '募集案件を登録しました。');
    }

    public function update(Request $request, Job $job)
    {
        abort_unless($this->canUseOrganization($job->organization), 403);
        $data = $request->validate([
            'title' => ['required', 'max:255'], 'job_type' => ['required', 'max:255'], 'prefecture' => ['required'],
            'area' => ['nullable', 'max:255'], 'required_conditions' => ['nullable'], 'target_age' => ['nullable', 'max:255'],
            'gender' => ['nullable', 'max:50'], 'sport' => ['nullable', 'max:255'], 'request_frequency' => ['nullable', 'max:255'],
            'request_style' => ['nullable', 'max:255'], 'role_description' => ['nullable'], 'detail' => ['nullable'], 'notes' => ['nullable'],
            'publish_end_at' => ['nullable', 'date'], 'budget_note' => ['nullable', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
        if ($request->hasFile('image')) $data['image_path'] = $request->file('image')->store('job-images', 'public');
        $data['status'] = auth()->user()->isAdmin() ? $job->status : 'pending_review';
        $job->update($data);
        if (!auth()->user()->isAdmin()) User::where('role', 'admin')->get()->each->notify(new MatchingActivityNotification('案件再審査', '「'.$job->title.'」が更新され、再審査待ちです。', '/admin/jobs'));
        return redirect()->route('dashboard')->with('status', '案件を更新しました。');
    }

    private function availableOrganizations()
    {
        if (auth()->user()->isAdmin()) {
            return Organization::publiclyVisible()->get();
        }

        return Organization::where('user_id', auth()->id())
            ->where('status', 'approved')
            ->get();
    }

    private function canUseOrganization(Organization $organization): bool
    {
        if (!$organization->isApproved()) {
            return false;
        }

        return auth()->user()->isAdmin() || $organization->user_id === auth()->id();
    }
}
