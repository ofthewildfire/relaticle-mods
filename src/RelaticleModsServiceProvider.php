<?php

declare(strict_types=1);

namespace Ofthewildfire\RelaticleModsPlugin;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Ofthewildfire\RelaticleModsPlugin\Models\Events;
use Ofthewildfire\RelaticleModsPlugin\Models\Projects;

class RelaticleModsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'relaticle-mods-migrations');

        // Publish config
        $this->publishes([
            __DIR__ . '/../config/relaticle-mods.php' => config_path('relaticle-mods.php'),
        ], 'relaticle-mods-config');

        // Register morph map entries
        $this->registerMorphMap();
    }

    protected function registerMorphMap(): void
    {
        $existingMorphMap = Relation::$morphMap ?? [];
        
        $newMorphMap = array_merge($existingMorphMap, [
            'events' => Events::class,
            'projects' => Projects::class,
        ]);

        Relation::enforceMorphMap($newMorphMap);
    }
}