<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use App\Notifications\MatchingActivityNotification;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index()
    {
        return view('organizations.index', [
            'organizations' => Organization::publiclyVisible()->withCount([
                'jobs' => fn ($query) => $query->where('status', 'published'),
            ])->latest()->paginate(12),
        ]);
    }

    public function show(Organization $organization)
    {
        $user = auth()->user();
        if (!$organization->isApproved() && (!$user || (!$user->isAdmin() && $organization->user_id !== $user->id))) {
            abort(404);
        }

        $organization->load(['jobs' => function ($query) use ($user, $organization) {
            if (!$user || (!$user->isAdmin() && $organization->user_id !== $user->id)) {
                $query->where('status', 'published');
            }
        }]);
        return view('organizations.show', compact('organization'));
    }

    public function create()
    {
        abort_unless(auth()->user()->isOrganization() || auth()->user()->isAdmin(), 403);

        return view('organizations.create', ['prefectures' => config('matching.prefectures'), 'organization' => auth()->user()->organization]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->isOrganization() || auth()->user()->isAdmin(), 403);

        $data = $request->validate([
            'name' => ['required', 'max:255'],
            'main_prefecture' => ['required'],
            'area' => ['nullable', 'max:255'],
            'sport' => ['nullable', 'max:255'],
            'target_age' => ['nullable', 'max:255'],
            'gender' => ['nullable', 'max:50'],
            'member_count' => ['nullable', 'integer'],
            'manager_name' => ['nullable', 'max:255'],
            'manager_email' => ['nullable', 'email', 'not_regex:/[\r\n]/'],
            'manager_phone' => ['nullable', 'max:50'],
            'official_url' => ['nullable', 'url'],
            'introduction' => ['nullable', 'max:5000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if ($request->hasFile('image')) $data['image_path'] = $request->file('image')->store('organization-images', 'public');

        $data['user_id'] = auth()->id();
        $data['registered_at'] = now()->toDateString();
        $data['status'] = auth()->user()->isAdmin() ? 'approved' : 'pending';
        $organization = Organization::updateOrCreate(['user_id' => auth()->id()], $data);
        if (!auth()->user()->isAdmin()) {
            User::where('role', 'admin')->get()->each->notify(new MatchingActivityNotification('団体プロフィール審査', $organization->name.' のプロフィールが審査待ちです。', '/admin/organizations'));
        }

        return redirect('/dashboard')->with('status', '団体プロフィールを登録しました。');
    }
}
