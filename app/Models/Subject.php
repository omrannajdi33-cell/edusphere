<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'color',
        'icon',
        'sort_order',
    ];

    public function competencies(): HasMany
    {
        return $this->hasMany(Competency::class)->orderBy('sort_order');
    }

    public function publishedActivitiesCount(): int
    {
        return Activity::query()
            ->whereHas('competency', fn ($q) => $q->where('subject_id', $this->id))
            ->published()
            ->count();
    }

    public function calendarEvents(): HasMany
    {
        return $this->hasMany(CalendarEvent::class);
    }
}
