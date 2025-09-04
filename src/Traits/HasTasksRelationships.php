<?php

declare(strict_types=1);

namespace Ofthewildfire\RelaticleModsPlugin\Traits;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Ofthewildfire\RelaticleModsPlugin\Models\Events;
use Ofthewildfire\RelaticleModsPlugin\Models\Projects;

trait HasTasksRelationships
{
    /**
     * Get all events that this task is attached to
     */
    public function events(): MorphToMany
    {
        return $this->morphToMany(Events::class, 'taskable');
    }

    /**
     * Get all projects that this task is attached to
     */
    public function projects(): MorphToMany
    {
        return $this->morphToMany(Projects::class, 'taskable');
    }
}