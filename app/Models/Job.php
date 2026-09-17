<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'title', 'job_type', 'prefecture', 'area', 'required_conditions',
        'target_age', 'gender', 'sport', 'request_frequency', 'request_style',
        'role_description', 'detail', 'notes', 'status', 'publish_start_at', 'publish_end_at',
        'image_path', 'budget_note',
    ];

    protected $casts = [
        'publish_start_at' => 'date',
        'publish_end_at' => 'date',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function offers() { return $this->hasMany(Offer::class); }

    public function isPublished(): bool
    {
        return $this->status === 'published'
            && (!$this->publish_start_at || $this->publish_start_at->isToday() || $this->publish_start_at->isPast())
            && (!$this->publish_end_at || $this->publish_end_at->isToday() || $this->publish_end_at->isFuture())
            && $this->organization
            && $this->organization->isApproved();
    }

    public function scopePubliclyVisible($query)
    {
        return $query->where('status', 'published')
            ->where(function ($dateQuery) {
                $dateQuery->whereNull('publish_start_at')->orWhereDate('publish_start_at', '<=', today());
            })
            ->where(function ($dateQuery) {
                $dateQuery->whereNull('publish_end_at')->orWhereDate('publish_end_at', '>=', today());
            })
            ->whereHas('organization', fn ($organization) => $organization->publiclyVisible());
    }
}
