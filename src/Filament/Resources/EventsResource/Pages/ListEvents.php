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
        
        // Apply saved preferences AFTER custom fields have been added
        $savedColumns = static::getSavedColumnVisibility('events');
        
        if (!empty($savedColumns)) {
            $columns = $table->getColumns();
            
            foreach ($columns as $column) {
                $columnName = $column->getName();
                
                if ($column->isToggleable()) {
                    if (in_array($columnName, $savedColumns)) {
                        $column->toggledHiddenByDefault(false); // Show
                    } else {
                        $column->toggledHiddenByDefault(true);  // Hide
                    }
                }
            }
        }
        
        return $table;
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
            ->body('Your column preferences have been saved successfully.')
            ->success()
            ->send();
    }
}