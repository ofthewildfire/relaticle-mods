<?php

declare(strict_types=1);

namespace Ofthewildfire\RelaticleModsPlugin\Filament\Resources\ProjectsResource\Pages;

use App\Filament\Components\Infolists\AvatarName;
use App\Filament\App\Resources\CompanyResource\RelationManagers;
use Ofthewildfire\RelaticleModsPlugin\Filament\Resources\ProjectsResource;
use Filament\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Relaticle\CustomFields\Filament\Infolists\CustomFieldsInfolists;

class ViewProjects extends ViewRecord
{
    protected static string $resource = ProjectsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ]),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Split::make([
                    // Left Side: Project Info
                    Section::make([
                        Split::make([
                            // Project Name (Large)
                            AvatarName::make('project_name')
                                ->name('project_name')
                                ->textSize('xl')
                                ->label(''), // Hide label

                            // Created By
                            AvatarName::make('creator')
                                ->avatar('creator.avatar')
                                ->name('creator.name')
                                ->avatarSize('sm')
                                ->textSize('sm')
                                ->circular()
                                ->label('Created By'),

                            // Project Manager
                            AvatarName::make('manager')
                                ->avatar('manager.avatar')
                                ->name('manager.name')
                                ->avatarSize('sm')
                                ->textSize('sm')
                                ->circular()
                                ->label('Project Manager'),
                        ])->from('md'),

                        // Custom Fields
                        CustomFieldsInfolists::make(),
                    ])->columnSpan(['md' => 8]),

                    // Right Side: Metadata
                    Section::make([
                        TextEntry::make('start_date')
                            ->label('Start Date')
                            ->icon('heroicon-o-calendar')
                            ->date(),

                        TextEntry::make('end_date')
                            ->label('End Date')
                            ->icon('heroicon-o-calendar')
                            ->date(),

                        TextEntry::make('budget')
                            ->label('Budget')
                            ->icon('heroicon-o-currency-dollar')
                            ->money('USD'), // change if needed

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'planning' => 'gray',
                                'active' => 'success',
                                'on-hold' => 'warning',
                                'completed' => 'info',
                                'archived' => 'secondary',
                                default => 'gray',
                            }),

                        TextEntry::make('is_priority')
                            ->label('Priority')
                            ->icon('heroicon-o-star')
                            ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No')
                            ->color(fn ($state) => $state ? 'danger' : 'secondary'),

                        TextEntry::make('created_at')
                            ->label('Created')
                            ->icon('heroicon-o-clock')
                            ->dateTime(),

                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->icon('heroicon-o-clock')
                            ->dateTime(),
                    ])
                        ->grow(false)
                        ->columnSpan(['md' => 4]),
                ])
                    ->columns(1)
                    ->columnSpan('full'),
            ]);
    }

    public function getRelationManagers(): array
    {
        return [
            RelationManagers\PeopleRelationManager::class,
            RelationManagers\TasksRelationManager::class,
            RelationManagers\NotesRelationManager::class,
        ];
    }
}