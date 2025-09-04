<?php

declare(strict_types=1);

namespace Ofthewildfire\RelaticleModsPlugin\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Ofthewildfire\RelaticleModsPlugin\Models\Ideas;

trait HasProjectsRelationships
{
    /**
     * Get all ideas that this project is associated with
     */
    public function ideas(): BelongsToMany
    {
        return $this->belongsToMany(Ideas::class, 'idea_projects', 'projects_id', 'idea_id');
    }
}