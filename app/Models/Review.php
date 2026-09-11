<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['application_id', 'organization_id', 'coach_profile_id', 'rating', 'title', 'body', 'status'];

    public function application() { return $this->belongsTo(Application::class); }
    public function organization() { return $this->belongsTo(Organization::class); }
    public function coachProfile() { return $this->belongsTo(CoachProfile::class); }
}
