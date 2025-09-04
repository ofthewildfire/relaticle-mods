<?php

declare(strict_types=1);

namespace Ofthewildfire\RelaticleModsPlugin\Filament\Resources\EventsResource\Pages;

use Ofthewildfire\RelaticleModsPlugin\Filament\Resources\EventsResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Facades\Filament;

class CreateEvents extends CreateRecord
{
    protected static string $resource = EventsResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $teamId = Filament::getTenant()?->id ?? auth()->user()?->current_team_id ?? auth()->user()?->team_id;
        $data['team_id'] = $teamId;

        return $data;
    }
}