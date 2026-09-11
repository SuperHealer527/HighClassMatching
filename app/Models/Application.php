<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id', 'coach_profile_id', 'message', 'available_schedule',
        'condition_note', 'attachment_url', 'status',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function coachProfile()
    {
        return $this->belongsTo(CoachProfile::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(ApplicationStatusHistory::class)->oldest();
    }

    public function review() { return $this->hasOne(Review::class); }
}
