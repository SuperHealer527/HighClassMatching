<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileRevisionRequest extends Model
{
    protected $fillable = [
        'coach_profile_id', 'requested_by', 'message', 'status', 'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function coachProfile()
    {
        return $this->belongsTo(CoachProfile::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }
}
