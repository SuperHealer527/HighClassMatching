<?php

namespace App\Http\Controllers;

use App\Models\CoachProfile;
use App\Models\Job;
use App\Models\Article;
use App\Models\Organization;

class HomeController extends Controller
{
    public function __invoke()
    {
        $prefectures = config('matching.prefectures');
        $databaseCounts = CoachProfile::publiclyVisible()
            ->selectRaw('main_prefecture, COUNT(*) as coach_count')
            ->groupBy('main_prefecture')
            ->pluck('coach_count', 'main_prefecture');
        $counts = collect($prefectures)
            ->mapWithKeys(fn ($prefecture) => [$prefecture => (int) ($databaseCounts[$prefecture] ?? 0)]);

        return view('home', [
            'prefectures' => $prefectures,
            'regions' => [
                ['label' => '北海道・東北', 'prefectures' => array_slice($prefectures, 0, 7)],
                ['label' => '関東', 'prefectures' => array_slice($prefectures, 7, 7)],
                ['label' => '中部', 'prefectures' => array_slice($prefectures, 14, 10)],
                ['label' => '関西', 'prefectures' => array_slice($prefectures, 24, 6)],
                ['label' => '中国・四国', 'prefectures' => array_slice($prefectures, 30, 9)],
                ['label' => '九州・沖縄', 'prefectures' => array_slice($prefectures, 39, 8)],
            ],
            'counts' => $counts,
            'coachTotal' => CoachProfile::publiclyVisible()->count(),
            'jobTotal' => Job::publiclyVisible()->count(),
            'organizationTotal' => Organization::publiclyVisible()->count(),
            'featuredCoaches' => CoachProfile::withAvg('reviews', 'rating')->publiclyVisible()->latest('profile_updated_at')->take(4)->get(),
            'featuredJobs' => Job::with('organization')->publiclyVisible()->latest('publish_start_at')->take(4)->get(),
            'articles' => Article::published()->latest('published_at')->take(3)->get(),
        ]);
    }
}
