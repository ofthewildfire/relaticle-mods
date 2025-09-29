<?php

declare(strict_types=1);

namespace Ofthewildfire\RelaticleModsPlugin\Filament\Resources\ProjectsResource\Pages;

use Ofthewildfire\RelaticleModsPlugin\Filament\Resources\ProjectsResource;
use Ofthewildfire\RelaticleModsPlugin\Traits\HasTablePreferences;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Relaticle\CustomFields\Filament\Tables\Concerns\InteractsWithCustomFields;

class ListProjects extends ListRecords
{
    use InteractsWithCustomFields;
    use HasTablePreferences;
    
    protected static string $resource = ProjectsResource::class;

    public function getTable(): \Filament\Tables\Table
    {
        $table = parent::getTable();
        
        // Load saved preferences and force them into session after table is built
        $this->loadSavedColumnPreferences($table);
        
        return $table;
    }

    protected function loadSavedColumnPreferences(\Filament\Tables\Table $table): void
    {
        // Always force load our database preferences on every page load
        $savedColumns = static::getSavedColumnVisibility('projects');
        
        if (!empty($savedColumns)) {
            // Get all possible columns from the table
            $allColumns = collect($table->getColumns())->pluck('name')->toArray();
            
            // Calculate hidden columns (inverse of visible columns)
            $hiddenColumns = array_diff($allColumns, $savedColumns);
            
            // FORCE override any existing session data with our database preferences
            $sessionKey = $this->getTableColumnToggleSessionKey();
            session()->put($sessionKey, $hiddenColumns);
        }
    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('savePreferences')
                ->label('Save Column Preferences')
                ->icon('heroicon-o-bookmark')
                ->color('gray')
                ->action(function () {
                    $this->saveCurrentColumnPreferences();
                })
                ->requiresConfirmation()
                ->modalHeading('Save Column Preferences')
                ->modalDescription('This will save your current column visibility settings for this table.')
                ->modalSubmitActionLabel('Save'),
        ];
    }

    public function saveCurrentColumnPreferences(): void
    {
        // Get the current visible columns from Livewire state
        $visibleColumns = $this->getCurrentlyVisibleColumns();
        
        $this->saveColumnVisibility('projects', $visibleColumns);
        
        Notification::make()
            ->title('Preferences Saved')
            ->body('Visible columns: ' . implode(', ', $visibleColumns))
            ->success()
            ->send();
    }
}