<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchingMaster extends Model
{
    use HasFactory;
    protected $fillable = ['type', 'value', 'sort_order', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
}
