<?php

declare(strict_types=1);

namespace Ofthewildfire\RelaticleModsPlugin\Filament\Resources\IdeasResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Ofthewildfire\RelaticleModsPlugin\Filament\Resources\IdeasResource;

class ListIdeas extends ListRecords
{
    protected static string $resource = IdeasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}


