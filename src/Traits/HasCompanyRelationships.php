<?php

declare(strict_types=1);

namespace Ofthewildfire\RelaticleModsPlugin\Traits;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Ofthewildfire\RelaticleModsPlugin\Models\Ideas;

trait HasCompanyRelationships
{
    /**
     * Get all ideas that this company is associated with
     */
    public function ideas(): MorphToMany
    {
        return $this->morphToMany(Ideas::class, 'ideaable');
    }
}