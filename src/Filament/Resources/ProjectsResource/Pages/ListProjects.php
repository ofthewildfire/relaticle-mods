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