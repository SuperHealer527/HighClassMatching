<?php

namespace App\Http\Controllers;

use App\Models\CoachProfile;
use Illuminate\Support\Facades\Storage;

class VerificationDocumentController extends Controller
{
    public function download(CoachProfile $coach, string $type)
    {
        abort_unless(auth()->user()->isAdmin() || $coach->user_id === auth()->id(), 403);
        abort_unless(in_array($type, ['identity', 'qualification'], true), 404);
        $path = $type === 'identity' ? $coach->identity_document_path : $coach->qualification_document_path;
        abort_unless($path && Storage::disk('local')->exists($path), 404);
        return Storage::disk('local')->download($path);
    }
}
