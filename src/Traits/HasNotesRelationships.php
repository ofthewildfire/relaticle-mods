<?php

declare(strict_types=1);

namespace Ofthewildfire\RelaticleModsPlugin\Traits;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Ofthewildfire\RelaticleModsPlugin\Models\Events;
use Ofthewildfire\RelaticleModsPlugin\Models\Projects;

trait HasNotesRelationships
{
    /**
     * Get all events that this note is attached to
     */
    public function events(): MorphToMany
    {
        return $this->morphToMany(Events::class, 'noteable');
    }

    /**
     * Get all projects that this note is attached to
     */
    public function projects(): MorphToMany
    {
        return $this->morphToMany(Projects::class, 'noteable');
    }
}