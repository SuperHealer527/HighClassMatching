<?php

namespace App\Services;

use App\Models\CoachProfile;
use App\Models\Job;
use Illuminate\Support\Collection;

class MatchingService
{
    public function jobsForCoach(CoachProfile $coach, int $limit = 6): Collection
    {
        return Job::with('organization')->withCount('applications')->publiclyVisible()->get()
            ->map(fn (Job $job) => $this->withScore($job, $this->score($coach, $job)))
            ->sortByDesc('match_score')->take($limit)->values();
    }

    public function coachesForJob(Job $job, int $limit = 6): Collection
    {
        return CoachProfile::with('user')->withAvg('reviews', 'rating')->publiclyVisible()->get()
            ->map(fn (CoachProfile $coach) => $this->withScore($coach, $this->score($coach, $job)))
            ->sortByDesc('match_score')->take($limit)->values();
    }

    public function completeness(array $data): int
    {
        $fields = ['name', 'kana', 'birth_year', 'affiliation', 'main_prefecture', 'sports', 'fields', 'degree', 'qualifications', 'keywords', 'achievements', 'desired_fee_range', 'message', 'photo_path'];
        $filled = collect($fields)->filter(fn ($field) => filled($data[$field] ?? null))->count();
        return (int) round(($filled / count($fields)) * 100);
    }

    private function score(CoachProfile $coach, Job $job): int
    {
        $score = 0;
        $sports = array_map('mb_strtolower', $coach->sports ?? []);
        $fields = array_map('mb_strtolower', $coach->fields ?? []);
        $jobSport = mb_strtolower((string) $job->sport);
        $jobType = mb_strtolower((string) $job->job_type);
        $prefectures = array_unique(array_filter(array_merge([$coach->main_prefecture], $coach->available_prefectures ?? [])));

        if (collect($sports)->contains(fn ($sport) => $sport && (str_contains($jobSport, $sport) || str_contains($sport, $jobSport)))) $score += 35;
        if (collect($fields)->contains(fn ($field) => $field && (str_contains($jobType, $field) || str_contains($field, $jobType)))) $score += 25;
        if (in_array($job->prefecture, $prefectures, true)) $score += 25;
        if ($coach->target_ages && $job->target_age && str_contains($coach->target_ages, $job->target_age)) $score += 10;
        if ($coach->verification_status === 'verified') $score += 5;

        return min(100, $score);
    }

    private function withScore($model, int $score)
    {
        $model->setAttribute('match_score', $score);
        return $model;
    }
}
