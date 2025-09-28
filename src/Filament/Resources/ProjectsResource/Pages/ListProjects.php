<?php

declare(strict_types=1);

namespace Ofthewildfire\RelaticleModsPlugin\Filament\Resources\ProjectsResource\Pages;

use Ofthewildfire\RelaticleModsPlugin\Filament\Resources\ProjectsResource;
use Ofthewildfire\RelaticleModsPlugin\Traits\HasTablePreferences;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Relaticle\CustomFields\Filament\Tables\Concerns\InteractsWithCustomFields;

class ListProjects extends ListRecords
{
    use InteractsWithCustomFields;
    use HasTablePreferences;
    
    protected static string $resource = ProjectsResource::class;

    protected $listeners = [
        'updateColumnToggles' => 'handleColumnToggles',
    ];

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
        $this->syncColumnPreferencesToDatabase('projects');
    }

    public function updatedTableColumnSearches($value = null, ?string $key = null): void
    {
        parent::updatedTableColumnSearches($value, $key);
        
        // Sync column preferences to database when they change
        $this->syncColumnPreferencesToDatabase('projects');
    }

    public function handleColumnToggles($toggledColumns): void
    {
        $this->saveColumnToggleState('projects', $toggledColumns);
    }
}