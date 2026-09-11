<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoachProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'kana', 'roman_name', 'birth_year', 'affiliation',
        'main_prefecture', 'available_prefectures', 'area', 'sports', 'fields',
        'degree', 'qualifications', 'other_qualifications', 'keywords',
        'target_ages', 'target_levels', 'teaching_styles', 'achievements',
        'request_history', 'is_former_athlete', 'recommended_athlete', 'photo_path',
        'desired_fee_range', 'message', 'email', 'phone', 'status', 'profile_updated_at',
        'show_birth_year', 'show_available_prefectures', 'show_request_history', 'show_recommended_athlete',
        'identity_document_path', 'qualification_document_path', 'verification_status', 'completeness_score',
    ];

    protected $casts = [
        'available_prefectures' => 'array',
        'sports' => 'array',
        'fields' => 'array',
        'is_former_athlete' => 'boolean',
        'profile_updated_at' => 'datetime',
        'show_birth_year' => 'boolean',
        'show_available_prefectures' => 'boolean',
        'show_request_history' => 'boolean',
        'show_recommended_athlete' => 'boolean',
        'completeness_score' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function offers() { return $this->hasMany(Offer::class); }
    public function reviews() { return $this->hasMany(Review::class)->where('status', 'published'); }
    public function favoritedByOrganizations() { return $this->belongsToMany(Organization::class, 'coach_favorites')->withTimestamps(); }

    public function reviewAverage(): float
    {
        return round((float) $this->reviews()->avg('rating'), 1);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved' && (!$this->user_id || optional($this->user)->isApproved());
    }

    public function scopePubliclyVisible($query)
    {
        return $query->where('status', 'approved')
            ->whereHas('user', fn ($user) => $user->where('status', 'approved'));
    }
}
