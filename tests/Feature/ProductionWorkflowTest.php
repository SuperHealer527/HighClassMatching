<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Article;
use App\Models\CoachProfile;
use App\Models\Job;
use App\Models\MatchingMaster;
use App\Models\Offer;
use App\Models\Organization;
use App\Models\User;
use App\Notifications\MatchingActivityNotification;
use App\Services\MatchingService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductionWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_organization_can_save_and_offer_coach_then_coach_accepts(): void
    {
        Notification::fake();
        [$coachUser,$coach,$organizationUser,$organization,$job]=$this->records();
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'approved']);
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
        Notification::assertSentTo($coachUser, MatchingActivityNotification::class, function ($notification) use ($coachUser) {
            return $notification->title === '新しい直接オファー'
                && $notification->forceMail
                && in_array('mail', $notification->via($coachUser), true);
        });
        Notification::assertSentTo($admin, MatchingActivityNotification::class, function ($notification) use ($admin) {
            return $notification->title === '新しい直接オファー'
                && $notification->forceMail
                && in_array('mail', $notification->via($admin), true);
        });
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

    public function test_disabled_direct_offer_uses_office_mediated_flow(): void
    {
        Notification::fake();
        [$coachUser, $coach, $organizationUser] = $this->records();
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'approved']);
        $coach->update(['direct_offer_enabled' => false]);

        $this->actingAs($organizationUser)->get(route('offers.create', $coach))->assertForbidden();
        $this->actingAs($organizationUser)->get(route('coaches.show', $coach))
            ->assertOk()
            ->assertDontSee('直接オファーする')
            ->assertSee('事務局へ相談');
        $this->actingAs($organizationUser)->get(route('inquiries.create', ['coach' => $coach->id, 'mode' => 'mediated']))
            ->assertOk()
            ->assertSee('事務局へ相談');
        $this->actingAs($organizationUser)->post(route('inquiries.store'), [
            'coach_profile_id' => $coach->id,
            'category' => 'mediated_offer',
            'subject' => '事務局への指導相談',
            'body' => '条件整理と指導者への確認をお願いします。',
        ])->assertRedirect();
        Notification::assertSentTo($admin, MatchingActivityNotification::class, function ($notification) use ($admin) {
            return $notification->title === '指導者についての事務局相談'
                && $notification->forceMail
                && in_array('mail', $notification->via($admin), true);
        });
        Notification::assertNotSentTo($coachUser, MatchingActivityNotification::class);
    }

    public function test_coach_can_store_structured_profile_fields(): void
    {
        Storage::fake('public');
        [$coachUser, $coach] = $this->records();

        $this->actingAs($coachUser)->post(route('coaches.store'), [
            'name' => $coach->name,
            'main_prefecture' => '東京',
            'available_prefectures' => ['東京', '神奈川'],
            'sports' => ['バスケットボール', '陸上競技', '柔道'],
            'fields' => ['メンタル'],
            'other_affiliations' => ['地域スポーツ研究会', 'ジュニア育成委員会', '競技連盟'],
            'education_history' => ['体育大学卒業', 'スポーツ科学研究科修了'],
            'degree' => '修士（スポーツ科学）',
            'qualification_items' => ['公認コーチ', 'CSCS'],
            'recommendations' => [[
                'name' => '山田選手',
                'introduction' => '丁寧で実践的な指導です。',
                'image' => UploadedFile::fake()->image('recommendation.jpg', 800, 500),
            ]],
            'teaching_achievements' => ['全国大会出場チームを指導'],
            'request_achievements' => ['部活動の年間指導を担当'],
            'direct_offer_enabled' => '0',
            'is_student' => '1',
            'message' => '競技経験と専門知識を生かし、選手に寄り添う指導を行います。',
        ])->assertRedirect('/dashboard');

        $coach->refresh();
        $this->assertSame(['体育大学卒業', 'スポーツ科学研究科修了'], $coach->education_history);
        $this->assertSame(['地域スポーツ研究会', 'ジュニア育成委員会', '競技連盟'], $coach->other_affiliations);
        $this->assertSame('修士（スポーツ科学）', $coach->degree);
        $this->assertSame(['バスケットボール', '陸上競技', '柔道'], $coach->sports);
        $this->assertSame(['メンタル'], $coach->fields);
        $this->assertSame(['公認コーチ', 'CSCS'], $coach->qualification_items);
        $this->assertSame('山田選手', $coach->recommendations[0]['name']);
        $this->assertNotEmpty($coach->recommendations[0]['image_path']);
        Storage::disk('public')->assertExists($coach->recommendations[0]['image_path']);
        $this->assertFalse($coach->direct_offer_enabled);
        $this->assertTrue($coach->is_student);
        $this->assertTrue($coach->show_available_prefectures);

        $coachUser->unsetRelation('coachProfile');
        $this->actingAs($coachUser)->get(route('coaches.create'))
            ->assertOk()
            ->assertSee('name="direct_offer_enabled" value="0" checked', false)
            ->assertSee('name="is_student" value="1" checked', false)
            ->assertSee('maxlength="150"', false)
            ->assertSee('maxlength="200"', false)
            ->assertSee('name="recommendations[0][image]"', false)
            ->assertSee('name="available_prefectures[]"', false)
            ->assertSee('type="radio" name="fields[]" value="メンタル" checked', false)
            ->assertSee('name="sports[]"', false)
            ->assertSee('name="other_affiliations[]"', false)
            ->assertSee('name="degree"', false);

        $this->actingAs($coachUser)->post(route('coaches.store'), [
            'name' => $coach->name,
            'main_prefecture' => '東京',
            'fields' => ['競技指導', 'メンタル'],
            'sports' => ['競技1', '競技2', '競技3', '競技4'],
            'message' => str_repeat('あ', 151),
        ])->assertSessionHasErrors(['fields', 'sports', 'message']);
    }

    public function test_student_coaches_can_be_marked_and_filtered(): void
    {
        [, $generalCoach] = $this->records();
        $studentUser = User::factory()->create(['role' => 'coach', 'status' => 'approved']);
        $studentCoach = CoachProfile::create([
            'user_id' => $studentUser->id,
            'name' => '学生指導者テスト',
            'main_prefecture' => '神奈川',
            'sports' => ['陸上競技'],
            'fields' => ['トレーニング', 'リハビリ'],
            'message' => '学生スポーツの経験を生かした指導を行います。',
            'is_student' => true,
            'status' => 'approved',
            'verification_status' => 'verified',
        ]);

        $this->get(route('coaches.index', ['student' => 1]))
            ->assertOk()
            ->assertSee($studentCoach->name)
            ->assertDontSee($generalCoach->name)
            ->assertSee('class="student-badge">学生', false)
            ->assertSee('coach-card-image', false)
            ->assertSee('トレーニング')
            ->assertSee('リハビリ');

        $this->get(route('coaches.show', $studentCoach))
            ->assertOk()
            ->assertSee('登録区分')
            ->assertSee('学生');

        $admin = User::factory()->create(['role' => 'admin', 'status' => 'approved']);
        $this->actingAs($admin)->get(route('admin.coaches.index', ['student' => '1']))
            ->assertOk()
            ->assertSee($studentCoach->name)
            ->assertDontSee($generalCoach->name);

        $this->actingAs($studentUser)->post(route('coaches.store'), [
            'name' => $studentCoach->name,
            'main_prefecture' => '神奈川',
            'message' => str_repeat('あ', 151),
        ])->assertSessionHasErrors('message');
    }

    public function test_user_and_admin_selects_restore_current_values(): void
    {
        [, $coach, , , $job] = $this->records();
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'approved']);
        $coach->update(['verification_status' => 'rejected']);
        MatchingMaster::create(['type' => 'field', 'value' => '操作確認用', 'sort_order' => 999, 'is_active' => true]);

        $this->get(route('jobs.index', ['prefecture' => '東京', 'sort' => 'deadline']))
            ->assertOk()
            ->assertSee('<option value="東京" selected>東京</option>', false)
            ->assertSee('<option value="deadline" selected>締切順</option>', false)
            ->assertSee($job->title);

        $this->actingAs($admin)->get(route('admin.coaches.index'))
            ->assertOk()
            ->assertSee('<option value="rejected" selected>rejected</option>', false)
            ->assertSee('css/admin-controls.css', false);
        $this->actingAs($admin)->get(route('admin.masters.index'))
            ->assertOk()
            ->assertSee('name="is_active" value="1" checked', false);
    }

    public function test_guest_can_review_the_public_coach_profile(): void
    {
        [$coachUser,$coach]=$this->records();
        $coach->update(['achievements'=>'会員限定の指導実績']);
        $this->get(route('coaches.show',$coach))
            ->assertSee('評価とメッセージ')
            ->assertSee('会員限定の指導実績')
            ->assertSeeInOrder(['指導実績', '評価とメッセージ', 'オファー可能なご依頼について']);
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

    public function test_registration_stores_referrer_without_browsing_member_wording(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('紹介者')
            ->assertSee('チーム・部活')
            ->assertDontSee('閲覧会員');

        $this->post(route('register'), [
            'name' => '紹介登録テスト',
            'email' => 'referred-member@example.com',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
            'role' => 'organization',
            'referrer' => '地域スポーツ協会 山田様',
        ])->assertRedirect('/dashboard');

        $this->assertDatabaseHas('users', [
            'email' => 'referred-member@example.com',
            'referrer' => '地域スポーツ協会 山田様',
        ]);
    }

    public function test_header_service_title_links_to_home(): void
    {
        $this->get(route('coaches.index'))
            ->assertOk()
            ->assertSee('class="brand-service" href="'.route('home').'"', false)
            ->assertSee('Back Athlete Matching');
    }

    public function test_organization_directory_filters_by_prefecture_and_sport(): void
    {
        $tokyo = config('matching.prefectures')[12];
        $osaka = config('matching.prefectures')[26];
        $tokyoUser = User::factory()->create(['role' => 'organization', 'status' => 'approved']);
        $osakaUser = User::factory()->create(['role' => 'organization', 'status' => 'approved']);

        Organization::create([
            'user_id' => $tokyoUser->id,
            'name' => 'Tokyo Basketball Club',
            'main_prefecture' => $tokyo,
            'sport' => 'Basketball',
            'status' => 'approved',
        ]);
        Organization::create([
            'user_id' => $osakaUser->id,
            'name' => 'Osaka Football Club',
            'main_prefecture' => $osaka,
            'sport' => 'Football',
            'status' => 'approved',
        ]);

        $this->get(route('organizations.index', [
            'prefecture' => $tokyo,
            'sport' => 'Basketball',
        ]))
            ->assertOk()
            ->assertSee('都道府県と競技から検索')
            ->assertSee('Tokyo Basketball Club')
            ->assertDontSee('Osaka Football Club')
            ->assertViewHas('sports', fn ($sports) => $sports->contains('Basketball') && $sports->contains('Football'));
    }

    public function test_hidden_coach_cannot_be_favorited_or_attached_to_an_inquiry(): void
    {
        [, $coach, $organizationUser] = $this->records();
        $coach->update(['status' => 'suspended']);

        $this->actingAs($organizationUser)
            ->post(route('favorites.toggle', $coach))
            ->assertForbidden();
        $this->assertDatabaseMissing('coach_favorites', ['coach_profile_id' => $coach->id]);

        $this->actingAs($organizationUser)->post(route('inquiries.store'), [
            'coach_profile_id' => $coach->id,
            'category' => 'consultation',
            'subject' => '非公開プロフィールへの問い合わせ',
            'body' => '送信されない問い合わせです。',
        ])->assertNotFound();
        $this->assertDatabaseMissing('inquiries', ['subject' => '非公開プロフィールへの問い合わせ']);
    }

    public function test_offer_cannot_reference_an_unpublished_job(): void
    {
        [, $coach, $organizationUser, , $job] = $this->records();
        $job->update(['status' => 'closed']);

        $this->actingAs($organizationUser)->post(route('offers.store', $coach), [
            'job_id' => $job->id,
            'subject' => '終了案件からのオファー',
            'message' => '送信されないオファーです。',
        ])->assertForbidden();
        $this->assertDatabaseMissing('offers', ['subject' => '終了案件からのオファー']);
    }

    public function test_public_jobs_respect_their_publication_window(): void
    {
        [, , , , $job] = $this->records();
        $job->update(['publish_end_at' => today()->subDay()]);

        $this->get(route('jobs.index'))->assertDontSee($job->title);
        $this->get(route('jobs.show', $job))->assertNotFound();

        $job->update(['publish_start_at' => today()->addDay(), 'publish_end_at' => today()->addWeek()]);
        $this->get(route('jobs.index'))->assertDontSee($job->title);
        $this->get(route('jobs.show', $job))->assertNotFound();
    }

    public function test_organization_cannot_reopen_a_finished_application(): void
    {
        [, $coach, $organizationUser, , $job] = $this->records();
        $application = Application::create([
            'job_id' => $job->id,
            'coach_profile_id' => $coach->id,
            'status' => 'completed',
        ]);

        $this->actingAs($organizationUser)->patch(route('applications.update', $application), [
            'status' => 'interview',
        ])->assertStatus(422);
        $this->assertDatabaseHas('applications', ['id' => $application->id, 'status' => 'completed']);
    }

    public function test_suspended_existing_session_cannot_access_member_or_admin_pages(): void
    {
        $member = User::factory()->create(['role' => 'coach', 'status' => 'suspended']);
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'suspended']);

        $this->actingAs($member)->get(route('dashboard'))->assertForbidden();
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_all_admin_management_pages_render_in_the_admin_layout(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'approved']);
        $pages = [
            'admin.dashboard' => 'dashboard.admin',
            'admin.users.index' => 'admin.users.index',
            'admin.coaches.index' => 'admin.coaches.index',
            'admin.organizations.index' => 'admin.organizations.index',
            'admin.jobs.index' => 'admin.jobs.index',
            'admin.applications.index' => 'admin.applications.index',
            'admin.inquiries.index' => 'admin.inquiries.index',
            'admin.offers.index' => 'admin.offers.index',
            'admin.reviews.index' => 'admin.reviews.index',
            'admin.articles.index' => 'admin.articles.index',
            'admin.masters.index' => 'admin.masters.index',
        ];

        foreach ($pages as $route => $view) {
            $this->actingAs($admin)->get(route($route))->assertOk()->assertViewIs($view);
        }
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
