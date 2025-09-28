<?php

declare(strict_types=1);

namespace Ofthewildfire\RelaticleModsPlugin\Filament\Resources\IdeasResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Ofthewildfire\RelaticleModsPlugin\Filament\Resources\IdeasResource;
use Ofthewildfire\RelaticleModsPlugin\Traits\HasTablePreferences;
use Relaticle\CustomFields\Filament\Tables\Concerns\InteractsWithCustomFields;

class ListIdeas extends ListRecords
{
    use InteractsWithCustomFields;
    use HasTablePreferences;
    
    protected static string $resource = IdeasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function mount(): void
    {
        parent::mount();
        
        // Sync any existing session column preferences to database
        static::syncColumnPreferencesToDatabase('ideas');
    }

    public function updatedTableColumnSearches($value = null, ?string $key = null): void
    {
        parent::updatedTableColumnSearches($value, $key);
        
        // Sync column preferences to database when they change
        static::syncColumnPreferencesToDatabase('ideas');
    }
}


