<?php

declare(strict_types=1);

namespace Ofthewildfire\RelaticleModsPlugin\Filament\Resources\EventsResource\Pages;

use Ofthewildfire\RelaticleModsPlugin\Filament\Resources\EventsResource;
use Ofthewildfire\RelaticleModsPlugin\Traits\HasTablePreferences;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Relaticle\CustomFields\Filament\Tables\Concerns\InteractsWithCustomFields;

class ListEvents extends ListRecords
{
    use InteractsWithCustomFields;
    use HasTablePreferences;
    
    protected static string $resource = EventsResource::class;

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
        static::syncColumnPreferencesToDatabase('events');
    }

    public function updatedTableColumnSearches(): void
    {
        parent::updatedTableColumnSearches();
        
        // Sync column preferences to database when they change
        static::syncColumnPreferencesToDatabase('events');
    }
}