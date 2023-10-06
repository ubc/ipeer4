<?php

namespace App\Models;

use \DateTimeInterface;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class Assignment extends Model
{
    use HasFactory;

    protected $casts = [
        'due' => 'datetime',
        'open_from' => 'datetime',
        'open_until' => 'datetime',
        'results_from' => 'datetime',
        'results_until' => 'datetime',
    ];

    protected $fillable = ['name', 'desc', 'has_self_eval', 'is_published',
        'due', 'open_from', 'open_until', 'results_from', 'results_until',
        'course_id'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

}
