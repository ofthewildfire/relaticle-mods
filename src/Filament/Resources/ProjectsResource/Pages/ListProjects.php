<?php

declare(strict_types=1);

namespace Ofthewildfire\RelaticleModsPlugin\Filament\Resources\ProjectsResource\Pages;

use Ofthewildfire\RelaticleModsPlugin\Filament\Resources\ProjectsResource;
use Ofthewildfire\RelaticleModsPlugin\Traits\HasTablePreferences;
use Filament\Actions;
use Filament\Forms;
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
        
        // Apply saved column order preferences first
        $userId = auth()->id();
        if ($userId) {
            $preferences = \Ofthewildfire\RelaticleModsPlugin\Models\UserTablePreferences::getPreferences($userId, 'projects');
            
            if (isset($preferences['column_order']) && !empty($preferences['column_order'])) {
                $savedOrder = $preferences['column_order'];
                $columns = $table->getColumns();
                
                // Create a map of column name => column object
                $columnMap = [];
                foreach ($columns as $column) {
                    $columnMap[$column->getName()] = $column;
                }
                
                // Reorder columns according to saved order
                $orderedColumns = [];
                foreach ($savedOrder as $columnName) {
                    if (isset($columnMap[$columnName])) {
                        $orderedColumns[] = $columnMap[$columnName];
                        unset($columnMap[$columnName]);
                    }
                }
                
                // Add any remaining columns that weren't in the saved order
                foreach ($columnMap as $column) {
                    $orderedColumns[] = $column;
                }
                
                // Apply the reordered columns to the table
                $table->columns($orderedColumns);
            }
        }
        
        // Apply saved column visibility preferences
        $savedColumns = static::getSavedColumnVisibility('projects');
        
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
            
            // Column reordering action
            Actions\Action::make('reorderColumns')
                ->label('Reorder Columns')
                ->icon('heroicon-o-arrows-up-down')
                ->color('info')
                ->form([
                    Forms\Components\Repeater::make('column_order')
                        ->label('Drag to reorder columns')
                        ->schema([
                            Forms\Components\Checkbox::make('enabled')
                                ->label('Show')
                                ->default(true),
                            Forms\Components\Hidden::make('name')
                                ->required(),
                            Forms\Components\TextInput::make('label')
                                ->label('Column Name')
                                ->disabled(),
                        ])
                        ->reorderable()
                        ->addable(false)
                        ->deletable(false)
                        ->default(function () {
                            return $this->getColumnOrderFormData();
                        })
                        ->columnSpanFull(),
                ])
                ->action(function (array $data) {
                    $this->saveColumnOrderFromForm($data['column_order'] ?? []);
                })
                ->modalHeading('Reorder Table Columns')
                ->modalDescription('Drag the items below to change the column order.')
                ->modalSubmitActionLabel('Save Order')
                ->modalWidth('md'),
        ];
    }

    public function saveCurrentColumnPreferences(): void
    {
        // Get the current visible columns from Livewire state
        $visibleColumns = $this->getCurrentlyVisibleColumns();
        
        $this->saveColumnVisibility('projects', $visibleColumns);
        
        Notification::make()
            ->title('Preferences Saved')
            ->body('Your column preferences have been saved successfully.')
            ->success()
            ->send();
    }
    
    // Get column data for the reorder form
    protected function getColumnOrderFormData(): array
    {
        $table = $this->getTable();
        $columns = $table->getColumns();
        $savedColumns = static::getSavedColumnVisibility('projects');
        $formData = [];
        
        foreach ($columns as $column) {
            $columnName = $column->getName();
            
            // Skip columns without valid names
            if (empty($columnName) || !is_string($columnName)) {
                continue;
            }
            
            $isVisible = empty($savedColumns) ? !$column->isToggledHiddenByDefault() : in_array($columnName, $savedColumns);
            
            $formData[] = [
                'enabled' => $isVisible,
                'name' => $columnName,
                'label' => $column->getLabel() ?? $columnName,
            ];
        }
        
        return $formData;
    }

    // Save column order from form data
    protected function saveColumnOrderFromForm(array $formData): void
    {
        if (empty($formData)) {
            Notification::make()
                ->title('No Data')
                ->body('No column data was received.')
                ->warning()
                ->send();
            return;
        }
        
        $columnOrder = [];
        $visibleColumns = [];
        
        // Process each item safely
        foreach ($formData as $index => $item) {
            if (is_array($item) && isset($item['name']) && !empty($item['name'])) {
                $columnName = $item['name'];
                $columnOrder[] = $columnName;
                
                if ($item['enabled'] ?? false) {
                    $visibleColumns[] = $columnName;
                }
            }
        }
        
        if (empty($columnOrder)) {
            Notification::make()
                ->title('No Valid Columns')
                ->body('No valid column data was found.')
                ->warning()
                ->send();
            return;
        }
        
        $userId = auth()->id();
        if (!$userId) return;

        // Get existing preferences
        $existingPreferences = \Ofthewildfire\RelaticleModsPlugin\Models\UserTablePreferences::getPreferences($userId, 'projects');
        
        // Update both column order and visibility
        $existingPreferences['column_order'] = $columnOrder;
        $existingPreferences['visible_columns'] = $visibleColumns;
        $existingPreferences['updated_at'] = now()->toISOString();

        \Ofthewildfire\RelaticleModsPlugin\Models\UserTablePreferences::savePreferences($userId, 'projects', $existingPreferences);
        
        Notification::make()
            ->title('Column Preferences Saved')
            ->body('Your column order and visibility preferences have been saved. Refresh the page to see changes.')
            ->success()
            ->send();
    }
}