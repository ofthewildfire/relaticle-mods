<?php

declare(strict_types=1);

namespace Ofthewildfire\RelaticleModsPlugin\Filament\Resources\EventsResource\Pages;

use Ofthewildfire\RelaticleModsPlugin\Filament\Resources\EventsResource;
use Ofthewildfire\RelaticleModsPlugin\Traits\HasTablePreferences;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Relaticle\CustomFields\Filament\Tables\Concerns\InteractsWithCustomFields;

class ListEvents extends ListRecords
{
    use InteractsWithCustomFields;
    use HasTablePreferences;
    
    protected static string $resource = EventsResource::class;

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
        $savedColumns = static::getSavedColumnVisibility('events');
        
        if (!empty($savedColumns)) {
            // Get all possible columns from the table
            $allColumns = array_map(fn($col) => $col->getName(), $table->getColumns());
            
            // Calculate hidden columns (inverse of visible columns)
            $hiddenColumns = array_diff($allColumns, $savedColumns);
            
            // FORCE override any existing session data with our database preferences
            // Use Filament's session key format for column toggles
            $sessionKey = 'tables.' . static::class . '.toggledHiddenColumns';
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
        
        $this->saveColumnVisibility('events', $visibleColumns);
        
        Notification::make()
            ->title('Preferences Saved')
            ->body('Visible columns: ' . implode(', ', $visibleColumns))
            ->success()
            ->send();
    }
}