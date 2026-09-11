<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Article;
use App\Models\CoachProfile;
use App\Models\Job;
use App\Models\Offer;
use App\Models\Organization;
use App\Models\User;
use App\Services\MatchingService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductionWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_organization_can_save_and_offer_coach_then_coach_accepts(): void
    {
        [$coachUser,$coach,$organizationUser,$organization,$job]=$this->records();
        $this->actingAs($organizationUser)->post(route('favorites.toggle',$coach))->assertRedirect();
        $this->assertDatabaseHas('coach_favorites',['organization_id'=>$organization->id,'coach_profile_id'=>$coach->id]);

        $this->actingAs($organizationUser)->get(route('offers.create',$coach))
            ->assertOk()
            ->assertSee('本番テスト指導者')
            ->assertSee('東京 / バスケットボール');

        $this->actingAs($organizationUser)->post(route('offers.store',$coach),[
            'job_id'=>$job->id,'subject'=>'秋季指導のご相談','message'=>'週1回の指導をお願いします。','proposed_schedule'=>'10月から',
        ])->assertRedirect(route('offers.index'));
        $offer=Offer::where('subject','秋季指導のご相談')->firstOrFail();
        $this->actingAs($coachUser)->patch(route('offers.update',$offer),['status'=>'accepted'])->assertRedirect();
        $this->assertDatabaseHas('offers',['id'=>$offer->id,'status'=>'accepted']);
        $this->actingAs($coachUser)->get(route('offers.index'))->assertSee($organization->manager_email);
    }

    public function test_completed_application_can_receive_review(): void
    {
        [, $coach,$organizationUser,$organization,$job]=$this->records();
        $application=Application::create(['job_id'=>$job->id,'coach_profile_id'=>$coach->id,'status'=>'completed']);
        $this->actingAs($organizationUser)->post(route('reviews.store',$application),[
            'rating'=>5,'title'=>'素晴らしい指導','body'=>'選手の理解に合わせた丁寧な指導でした。',
        ])->assertRedirect();
        $this->assertDatabaseHas('reviews',['application_id'=>$application->id,'rating'=>5]);
    }

    public function test_guest_sees_limited_profile_but_member_sees_full_profile(): void
    {
        [$coachUser,$coach]=$this->records();
        $coach->update(['achievements'=>'会員限定の指導実績']);
        $this->get(route('coaches.show',$coach))->assertSee('MEMBERS ONLY')->assertDontSee('会員限定の指導実績');
        $this->actingAs($coachUser)->get(route('coaches.show',$coach))->assertSee('会員限定の指導実績');
    }

    public function test_matching_score_uses_sport_field_area_and_verification(): void
    {
        [, $coach,,,$job]=$this->records();
        $coach->update(['available_prefectures'=>['東京'],'target_ages'=>'中学生','verification_status'=>'verified']);
        $job->update(['sport'=>'バスケットボール','job_type'=>'競技指導','prefecture'=>'東京','target_age'=>'中学生']);
        $result=app(MatchingService::class)->jobsForCoach($coach,1)->first();
        $this->assertSame(100,$result->match_score);
    }

    public function test_new_coach_requires_and_stores_verification_documents(): void
    {
        Storage::fake('local'); Storage::fake('public');
        $user=User::factory()->create(['role'=>'coach','status'=>'pending']);
        $this->actingAs($user)->post(route('coaches.store'),[
            'name'=>'書類確認テスト','main_prefecture'=>'東京','sports'=>'陸上競技','fields'=>'競技指導',
        ])->assertSessionHasErrors('identity_document');
        $this->actingAs($user)->post(route('coaches.store'),[
            'name'=>'書類確認テスト','main_prefecture'=>'東京','sports'=>'陸上競技','fields'=>'競技指導',
            'identity_document'=>UploadedFile::fake()->create('identity.pdf',100,'application/pdf'),
            'qualification_document'=>UploadedFile::fake()->create('certificate.pdf',100,'application/pdf'),
            'photo'=>UploadedFile::fake()->image('profile.jpg'),
        ])->assertRedirect('/dashboard');
        $profile=$user->fresh()->coachProfile;
        Storage::disk('local')->assertExists($profile->identity_document_path);
        Storage::disk('public')->assertExists($profile->photo_path);
    }

    public function test_articles_and_password_reset_flow_are_available(): void
    {
        $article=Article::create(['title'=>'公開記事','slug'=>'production-test-article','category'=>'knowledge','excerpt'=>'記事概要','body'=>'記事本文','status'=>'published','published_at'=>now()]);
        $this->get(route('articles.show',$article))->assertOk()->assertSee('記事本文');
        $user=User::factory()->create(['status'=>'approved']);
        $this->post(route('password.email'),['email'=>$user->email])->assertSessionHas('status');
        $notification=$user->fresh()->notifications()->first();
        $this->assertStringContainsString('/reset-password/',$notification->data['url']);
    }

    public function test_home_map_uses_live_public_coach_counts_by_main_prefecture(): void
    {
        $prefecture = '山形';
        $before = CoachProfile::publiclyVisible()->where('main_prefecture', $prefecture)->count();

        $approvedUser = User::factory()->create(['role' => 'coach', 'status' => 'approved']);
        CoachProfile::create([
            'user_id' => $approvedUser->id,
            'name' => '地図集計テスト指導者',
            'main_prefecture' => $prefecture,
            'available_prefectures' => ['東京'],
            'sports' => ['陸上競技'],
            'fields' => ['競技指導'],
            'status' => 'approved',
        ]);

        $pendingUser = User::factory()->create(['role' => 'coach', 'status' => 'pending']);
        CoachProfile::create([
            'user_id' => $pendingUser->id,
            'name' => '非公開地図集計テスト指導者',
            'main_prefecture' => $prefecture,
            'sports' => ['陸上競技'],
            'fields' => ['競技指導'],
            'status' => 'approved',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('aria-label="'.$prefecture.'、対応指導者'.($before + 1).'名"', false);
    }

    public function test_registration_rejects_email_header_injection(): void
    {
        $this->post(route('register'), [
            'name' => 'メール検証テスト',
            'email' => "member@example.com\r\nBcc:attacker@example.com",
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
            'role' => 'coach',
        ])->assertSessionHasErrors('email');
    }

    private function records(): array
    {
        $coachUser=User::factory()->create(['role'=>'coach','status'=>'approved']);
        $coach=CoachProfile::create(['user_id'=>$coachUser->id,'name'=>'本番テスト指導者','main_prefecture'=>'東京','available_prefectures'=>['東京'],'sports'=>['バスケットボール'],'fields'=>['競技指導'],'status'=>'approved','verification_status'=>'verified','email'=>'coach-flow@example.com','phone'=>'090-0000-0000']);
        $organizationUser=User::factory()->create(['role'=>'organization','status'=>'approved']);
        $organization=Organization::create(['user_id'=>$organizationUser->id,'name'=>'本番テスト団体','main_prefecture'=>'東京','sport'=>'バスケットボール','target_age'=>'中学生','manager_email'=>'org-flow@example.com','manager_phone'=>'03-0000-0000','status'=>'approved']);
        $job=Job::create(['organization_id'=>$organization->id,'title'=>'本番フローテスト案件','job_type'=>'競技指導','prefecture'=>'東京','sport'=>'バスケットボール','target_age'=>'中学生','status'=>'published','publish_start_at'=>now()]);
        return [$coachUser,$coach,$organizationUser,$organization,$job];
    }
}
