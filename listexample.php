<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\CompanyResource\Pages;

use App\Filament\App\Resources\CompanyResource;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Ofthewildfire\RelaticleModsPlugin\Traits\HasTablePreferences;
use Relaticle\CustomFields\Filament\Tables\Concerns\InteractsWithCustomFields;

final class ListCompanies extends ListRecords
{
    use InteractsWithCustomFields;
    use HasTablePreferences;

    /** @var class-string<CompanyResource> */
    protected static string $resource = CompanyResource::class;

    public function getTable(): \Filament\Tables\Table
    {
        $table = parent::getTable();

        // Apply saved preferences AFTER custom fields have been added
        $savedColumns = static::getSavedColumnVisibility(static::getResource()::getModel());

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

    /**
     * Get the actions available on the resource index header.
     */
    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
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
        $visibleColumns = $this->getCurrentlyVisibleColumns();

        $this->saveColumnVisibility(static::getResource()::getModel(), $visibleColumns);

        Notification::make()
            ->title('Preferences Saved')
            ->body('Your column preferences have been saved successfully.')
            ->success()
            ->send();
    }
}