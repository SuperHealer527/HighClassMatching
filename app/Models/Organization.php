<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'main_prefecture', 'area', 'sport', 'target_age', 'gender',
        'member_count', 'manager_name', 'manager_email', 'manager_phone', 'official_url',
        'registered_at', 'status',
        'image_path', 'introduction',
    ];

    protected $casts = [
        'registered_at' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    public function offers() { return $this->hasMany(Offer::class); }
    public function favoriteCoaches() { return $this->belongsToMany(CoachProfile::class, 'coach_favorites')->withTimestamps(); }
    public function reviews() { return $this->hasMany(Review::class); }

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
