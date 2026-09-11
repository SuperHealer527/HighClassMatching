<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = ['organization_id', 'coach_profile_id', 'job_id', 'subject', 'message', 'proposed_schedule', 'status', 'responded_at'];
    protected $casts = ['responded_at' => 'datetime'];

    public function organization() { return $this->belongsTo(Organization::class); }
    public function coachProfile() { return $this->belongsTo(CoachProfile::class); }
    public function job() { return $this->belongsTo(Job::class); }
}
