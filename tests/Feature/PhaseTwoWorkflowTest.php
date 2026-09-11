<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\CoachProfile;
use App\Models\Inquiry;
use App\Models\Job;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PhaseTwoWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_member_can_send_inquiry_and_admin_can_reply(): void
    {
        [$coachUser, $coach, $organizationUser] = $this->matchingUsers();
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'approved']);

        $this->actingAs($organizationUser)->post(route('inquiries.store'), [
            'coach_profile_id' => $coach->id,
            'category' => 'consultation',
            'subject' => '指導相談テスト',
            'body' => '来月からの指導を相談したいです。',
        ])->assertRedirect();

        $inquiry = Inquiry::where('subject', '指導相談テスト')->firstOrFail();
        $this->assertDatabaseHas('notifications', ['notifiable_id' => $admin->id]);

        $this->actingAs($admin)->patch(route('admin.inquiries.reply', $inquiry), [
            'status' => 'resolved',
            'admin_reply' => '担当者からご連絡します。',
        ])->assertRedirect();

        $this->assertDatabaseHas('inquiries', ['id' => $inquiry->id, 'status' => 'resolved']);
        $this->assertDatabaseHas('notifications', ['notifiable_id' => $organizationUser->id]);
    }

    public function test_application_changes_create_history_and_coach_can_withdraw(): void
    {
        [$coachUser, $coach, $organizationUser, $organization] = $this->matchingUsers();
        $job = Job::create([
            'organization_id' => $organization->id,
            'title' => '選考フローテスト',
            'job_type' => '競技コーチ',
            'prefecture' => '東京',
            'status' => 'published',
        ]);
        $application = Application::create(['job_id' => $job->id, 'coach_profile_id' => $coach->id, 'status' => 'applied']);

        $this->actingAs($organizationUser)->patch(route('applications.update', $application), [
            'status' => 'interview',
            'note' => '9月20日にオンライン面談',
        ])->assertRedirect();
        $this->assertDatabaseHas('application_status_histories', ['application_id' => $application->id, 'to_status' => 'interview']);

        $this->actingAs($coachUser)->patch(route('applications.update', $application), ['status' => 'withdrawn'])->assertRedirect();
        $this->assertDatabaseHas('applications', ['id' => $application->id, 'status' => 'withdrawn']);
    }

    public function test_private_coach_fields_are_not_rendered_publicly(): void
    {
        [, $coach] = $this->matchingUsers();
        $coach->update([
            'birth_year' => 1985,
            'request_history' => '非公開の依頼実績',
            'show_birth_year' => false,
            'show_request_history' => false,
        ]);

        $this->get(route('coaches.show', $coach))
            ->assertOk()
            ->assertDontSee('1985年')
            ->assertDontSee('非公開の依頼実績');
    }

    public function test_admin_can_export_csv_and_member_cannot(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'approved']);
        $member = User::factory()->create(['role' => 'coach', 'status' => 'approved']);

        $this->actingAs($admin)->get(route('admin.export', 'users'))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->actingAs($member)->get(route('admin.export', 'users'))->assertForbidden();
    }

    public function test_suspended_account_disappears_from_public_search(): void
    {
        [$coachUser, $coach] = $this->matchingUsers();
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'approved']);

        $this->actingAs($admin)->patch(route('admin.users.status', $coachUser), ['status' => 'suspended'])->assertRedirect();
        $this->assertDatabaseHas('coach_profiles', ['id' => $coach->id, 'status' => 'suspended']);
        $this->get(route('coaches.index'))->assertDontSee('テスト指導者');
    }

    public function test_suspended_account_cannot_log_in(): void
    {
        $user = User::factory()->create(['role' => 'coach', 'status' => 'suspended']);

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    private function matchingUsers(): array
    {
        $coachUser = User::factory()->create(['role' => 'coach', 'status' => 'approved']);
        $coach = CoachProfile::create([
            'user_id' => $coachUser->id,
            'name' => 'テスト指導者',
            'main_prefecture' => '東京',
            'sports' => ['バスケットボール'],
            'fields' => ['競技指導'],
            'status' => 'approved',
        ]);
        $organizationUser = User::factory()->create(['role' => 'organization', 'status' => 'approved']);
        $organization = Organization::create([
            'user_id' => $organizationUser->id,
            'name' => 'テストスポーツ団体',
            'main_prefecture' => '東京',
            'sport' => 'バスケットボール',
            'status' => 'approved',
        ]);

        return [$coachUser, $coach, $organizationUser, $organization];
    }
}
