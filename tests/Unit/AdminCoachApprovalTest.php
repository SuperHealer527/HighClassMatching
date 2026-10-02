<?php

namespace Tests\Unit;

use App\Http\Controllers\AdminController;
use App\Models\CoachProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Tests\TestCase;

class AdminCoachApprovalTest extends TestCase
{
    public function test_unverified_coach_approval_returns_validation_error(): void
    {
        $request = $this->requestWithSession([
            'status' => 'approved',
        ]);
        $coach = new CoachProfile([
            'verification_status' => 'pending',
        ]);
        $coach->id = 43;

        $response = app(AdminController::class)->updateCoachStatus($request, $coach);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertTrue(
            $request->session()->get('errors')->has('coach_status_43')
        );
    }

    public function test_verification_without_both_documents_returns_validation_error(): void
    {
        $request = $this->requestWithSession([
            'verification_status' => 'verified',
        ]);
        $coach = new CoachProfile([
            'verification_status' => 'pending',
            'identity_document_path' => 'coach-documents/identity.pdf',
            'qualification_document_path' => null,
        ]);
        $coach->id = 43;

        $response = app(AdminController::class)->verifyCoach($request, $coach);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertTrue(
            $request->session()->get('errors')->has('coach_verification_43')
        );
    }

    private function requestWithSession(array $input): Request
    {
        $request = Request::create('/admin/coaches/43/status', 'PATCH', $input);
        $request->setLaravelSession(app('session')->driver());
        app('url')->setRequest($request);

        return $request;
    }
}
