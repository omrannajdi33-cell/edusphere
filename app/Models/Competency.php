<?php

namespace App\Models;

use App\CompetencyModuleType;
use App\Modules\CompetencyModuleInterface;
use App\Modules\CompetencyModuleRegistry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Competency extends Model
{
    protected $fillable = [
        'subject_id',
        'name',
        'slug',
        'module_type',
        'sort_order',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class)->orderBy('sort_order');
    }

    public function moduleTypeEnum(): ?CompetencyModuleType
    {
        return CompetencyModuleType::tryFromString($this->module_type);
    }

    public function resolveModule(): CompetencyModuleInterface
    {
        return app(CompetencyModuleRegistry::class)->resolve($this->module_type);
    }

    public function hasSpecializedModule(): bool
    {
        return $this->module_type !== null && $this->module_type !== '';
    }
}
