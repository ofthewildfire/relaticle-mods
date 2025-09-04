<?php

declare(strict_types=1);

namespace Ofthewildfire\RelaticleModsPlugin;

use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\Panel;
use Ofthewildfire\RelaticleModsPlugin\Filament\Resources\EventsResource;
use Ofthewildfire\RelaticleModsPlugin\Filament\Resources\ProjectsResource;
use Ofthewildfire\RelaticleModsPlugin\Filament\Resources\IdeasResource;

class RelaticleModsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'relaticle-mods';
    }

    public function register(Panel $panel): Panel
    {
        return $panel
            ->resources([
                EventsResource::class,
                ProjectsResource::class,
                IdeasResource::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        $panel = Filament::getCurrentPanel();

        /** @var static|null $plugin */
        $plugin = $panel?->getPlugin(static::make()->getId());

        return $plugin ?? static::make();
    }
}