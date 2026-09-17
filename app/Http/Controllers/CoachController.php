<?php

namespace App\Http\Controllers;

use App\Models\CoachProfile;
use App\Models\User;
use App\Notifications\MatchingActivityNotification;
use App\Services\MatchingService;
use Illuminate\Http\Request;

class CoachController extends Controller
{
    public function index(Request $request)
    {
        $query = CoachProfile::query()->publiclyVisible()->withAvg('reviews', 'rating')->withCount('reviews');

        if ($request->filled('prefecture')) {
            $prefecture = $request->prefecture;
            $query->where(fn ($q) => $q->where('main_prefecture', $prefecture)->orWhere('available_prefectures', 'like', "%{$prefecture}%"));
        }
        if ($request->filled('field')) {
            $field = $request->field;
            $query->where(function ($q) use ($field) {
                $q->where('fields', 'like', "%{$field}%")->orWhere('keywords', 'like', "%{$field}%");
            });
        }
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('sports', 'like', "%{$keyword}%")
                    ->orWhere('fields', 'like', "%{$keyword}%")
                    ->orWhere('achievements', 'like', "%{$keyword}%")
                    ->orWhere('message', 'like', "%{$keyword}%");
            });
        }

        foreach (['area', 'target_ages', 'target_levels', 'teaching_styles'] as $filter) {
            if ($request->filled($filter)) $query->where($filter, 'like', '%'.$request->$filter.'%');
        }
        if ($request->filled('sport')) $query->where('sports', 'like', '%'.$request->sport.'%');
        if ($request->boolean('verified')) $query->where('verification_status', 'verified');
        switch ($request->get('sort')) {
            case 'rating':
                $query->orderByDesc('reviews_avg_rating');
                break;
            case 'complete':
                $query->orderByDesc('completeness_score');
                break;
            default:
                $query->latest('profile_updated_at');
        }

        return view('coaches.index', [
            'coaches' => $query->paginate(in_array((int)$request->per_page, [10,20,50]) ? (int)$request->per_page : 10)->withQueryString(),
            'prefectures' => config('matching.prefectures'),
            'fields' => config('matching.fields'),
        ]);
    }

    public function show(CoachProfile $coach)
    {
        $user = auth()->user();
        if (!$coach->isApproved() && (!$user || (!$user->isAdmin() && $coach->user_id !== $user->id))) {
            abort(404);
        }

        return view('coaches.show', compact('coach'));
    }

    public function create()
    {
        abort_unless(auth()->user()->isCoach() || auth()->user()->isAdmin(), 403);

        return view('coaches.create', [
            'prefectures' => config('matching.prefectures'),
            'fields' => config('matching.fields'),
            'coach' => auth()->user()->coachProfile,
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->isCoach() || auth()->user()->isAdmin(), 403);

        if (is_string($request->input('fields'))) {
            $request->merge(['fields' => array_values(array_filter(array_map('trim', explode(',', $request->input('fields')))))]);
        }

        $data = $request->validate([
            'name' => ['required', 'max:255'],
            'kana' => ['nullable', 'max:255'],
            'roman_name' => ['nullable', 'max:255'],
            'affiliation' => ['nullable', 'max:255'],
            'main_prefecture' => ['required'],
            'area' => ['nullable', 'max:255'],
            'sports' => ['nullable', 'max:255'],
            'fields' => ['nullable', 'array', 'max:7'],
            'fields.*' => ['in:'.implode(',', config('matching.fields'))],
            'education_history' => ['nullable', 'array', 'max:3'],
            'education_history.*' => ['nullable', 'max:255'],
            'qualification_items' => ['nullable', 'array', 'max:2'],
            'qualification_items.*' => ['nullable', 'max:255'],
            'recommendations' => ['nullable', 'array', 'max:3'],
            'recommendations.*.name' => ['nullable', 'max:255'],
            'recommendations.*.introduction' => ['nullable', 'max:1000'],
            'teaching_achievements' => ['nullable', 'array', 'max:3'],
            'teaching_achievements.*' => ['nullable', 'max:255'],
            'request_achievements' => ['nullable', 'array', 'max:3'],
            'request_achievements.*' => ['nullable', 'max:255'],
            'direct_offer_enabled' => ['nullable', 'boolean'],
            'keywords' => ['nullable'],
            'desired_fee_range' => ['nullable', 'max:255'],
            'message' => ['nullable'],
            'email' => ['nullable', 'email', 'not_regex:/[\r\n]/'],
            'phone' => ['nullable', 'max:50'],
            'available_prefectures' => ['nullable', 'array'],
            'available_prefectures.*' => ['in:'.implode(',', config('matching.prefectures'))],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'identity_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'qualification_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $existing = auth()->user()->coachProfile;
        if (!$existing && (!$request->hasFile('identity_document') || !$request->hasFile('qualification_document'))) {
            return back()->withInput()->withErrors(['identity_document' => '新規登録には本人確認書類と資格証明書が必要です。']);
        }

        if ($request->hasFile('photo')) $data['photo_path'] = $request->file('photo')->store('coach-photos', 'public');
        if ($request->hasFile('identity_document')) $data['identity_document_path'] = $request->file('identity_document')->store('coach-documents');
        if ($request->hasFile('qualification_document')) $data['qualification_document_path'] = $request->file('qualification_document')->store('coach-documents');
        if ($request->hasFile('identity_document') || $request->hasFile('qualification_document')) $data['verification_status'] = 'pending';

        $data['user_id'] = auth()->id();
        $data['sports'] = filled($data['sports'] ?? null) ? [trim($data['sports'])] : [];
        $data['fields'] = array_values(array_filter($data['fields'] ?? []));
        foreach (['education_history', 'qualification_items', 'teaching_achievements', 'request_achievements'] as $field) {
            $data[$field] = array_values(array_filter(array_map('trim', $data[$field] ?? [])));
        }
        $data['recommendations'] = collect($data['recommendations'] ?? [])->map(function ($recommendation) {
            return [
                'name' => trim($recommendation['name'] ?? ''),
                'introduction' => trim($recommendation['introduction'] ?? ''),
            ];
        })->filter(fn ($recommendation) => filled($recommendation['name']) || filled($recommendation['introduction']))->values()->all();
        $data['direct_offer_enabled'] = $request->has('direct_offer_enabled')
            ? $request->boolean('direct_offer_enabled')
            : ($existing ? $existing->direct_offer_enabled : true);
        $data['degree'] = $data['education_history'][0] ?? null;
        $data['qualifications'] = implode('、', $data['qualification_items']);
        $data['achievements'] = implode("\n", $data['teaching_achievements']);
        $data['request_history'] = implode("\n", $data['request_achievements']);
        $data['recommended_athlete'] = $data['recommendations'][0]['name'] ?? null;
        $data['status'] = auth()->user()->isAdmin() ? 'approved' : 'pending';
        $data['profile_updated_at'] = now();
        $data['show_birth_year'] = false;
        $data['show_available_prefectures'] = true;
        $data['show_request_history'] = true;
        $data['show_recommended_athlete'] = true;
        $data['completeness_score'] = app(MatchingService::class)->completeness(array_merge(optional($existing)->toArray() ?? [], $data));

        $coach = CoachProfile::updateOrCreate(['user_id' => auth()->id()], $data);
        if (!auth()->user()->isAdmin()) {
            User::where('role', 'admin')->get()->each->notify(new MatchingActivityNotification('指導者プロフィール審査', $coach->name.' 様のプロフィールが審査待ちです。', '/admin/coaches'));
        }
        return redirect('/dashboard')->with('status', '指導者プロフィールを登録しました。');
    }
}
