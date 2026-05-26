<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointTransaction extends Model
{
    protected $fillable = [
        'student_id',
        'point_behavior_id',
        'points',
        'reason',
        'given_by_id',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function behavior(): BelongsTo
    {
        return $this->belongsTo(PointBehavior::class, 'point_behavior_id');
    }

    public function givenBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'given_by_id');
    }
}
