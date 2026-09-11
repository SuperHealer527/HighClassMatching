<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\CoachProfile;
use App\Models\Inquiry;
use App\Models\Job;
use App\Models\Organization;
use App\Models\User;
use App\Models\Offer;
use App\Models\Review;
use App\Models\Article;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminExportController extends Controller
{
    public function __invoke(string $resource): StreamedResponse
    {
        $exports = [
            'users' => [['ID', '名前', 'メール', 'ロール', '状態', '登録日'], User::latest()->get()->map(fn ($v) => [$v->id, $v->name, $v->email, $v->role, $v->status, $v->created_at])],
            'coaches' => [['ID', '名前', '都道府県', '競技', '指導分野', '状態'], CoachProfile::latest()->get()->map(fn ($v) => [$v->id, $v->name, $v->main_prefecture, implode('/', $v->sports ?? []), implode('/', $v->fields ?? []), $v->status])],
            'organizations' => [['ID', '団体名', '都道府県', '競技', '状態'], Organization::latest()->get()->map(fn ($v) => [$v->id, $v->name, $v->main_prefecture, $v->sport, $v->status])],
            'jobs' => [['ID', '案件名', '団体ID', '都道府県', '競技', '状態'], Job::latest()->get()->map(fn ($v) => [$v->id, $v->title, $v->organization_id, $v->prefecture, $v->sport, $v->status])],
            'applications' => [['ID', '案件ID', '指導者ID', '状態', '応募日'], Application::latest()->get()->map(fn ($v) => [$v->id, $v->job_id, $v->coach_profile_id, $v->status, $v->created_at])],
            'inquiries' => [['ID', 'ユーザーID', 'カテゴリ', '件名', '状態', '受付日'], Inquiry::latest()->get()->map(fn ($v) => [$v->id, $v->user_id, $v->category, $v->subject, $v->status, $v->created_at])],
            'offers' => [['ID','団体ID','指導者ID','件名','状態','送信日'], Offer::latest()->get()->map(fn($v)=>[$v->id,$v->organization_id,$v->coach_profile_id,$v->subject,$v->status,$v->created_at])],
            'reviews' => [['ID','団体ID','指導者ID','評価','タイトル','状態'], Review::latest()->get()->map(fn($v)=>[$v->id,$v->organization_id,$v->coach_profile_id,$v->rating,$v->title,$v->status])],
            'articles' => [['ID','タイトル','カテゴリ','状態','公開日'], Article::latest()->get()->map(fn($v)=>[$v->id,$v->title,$v->category,$v->status,$v->published_at])],
        ];

        abort_unless(isset($exports[$resource]), 404);
        [$headers, $rows] = $exports[$resource];

        return response()->streamDownload(function () use ($headers, $rows) {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, $headers);
            foreach ($rows as $row) {
                fputcsv($output, $row);
            }
            fclose($output);
        }, $resource.'-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
