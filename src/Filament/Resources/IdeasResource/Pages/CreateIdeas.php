<?php

declare(strict_types=1);

namespace Ofthewildfire\RelaticleModsPlugin\Filament\Resources\IdeasResource\Pages;

use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Ofthewildfire\RelaticleModsPlugin\Filament\Resources\IdeasResource;

class CreateIdeas extends CreateRecord
{
    protected static string $resource = IdeasResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $teamId = Filament::getTenant()?->id ?? auth()->user()?->current_team_id ?? auth()->user()?->team_id;
        $data['team_id'] = $teamId;

        return $data;
    }
}


