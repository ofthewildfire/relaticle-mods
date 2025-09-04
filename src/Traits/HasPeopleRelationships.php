<?php

declare(strict_types=1);

namespace Ofthewildfire\RelaticleModsPlugin\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Ofthewildfire\RelaticleModsPlugin\Models\Events;
use Ofthewildfire\RelaticleModsPlugin\Models\Projects;

trait HasPeopleRelationships
{
    /**
     * Get all events that this person is attending
     */
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Events::class, 'event_people', 'people_id', 'event_id')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get all projects that this person is a team member of
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Projects::class, 'project_team_members', 'people_id', 'projects_id');
    }

    /**
     * Get all ideas that this person is associated with
     */
    public function ideas(): BelongsToMany
    {
        return $this->belongsToMany(\Ofthewildfire\RelaticleModsPlugin\Models\Ideas::class, 'idea_people', 'people_id', 'idea_id');
    }
}