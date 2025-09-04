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
                Section::make('Project Overview')
                    ->schema([
                        Split::make([
                            AvatarName::make('project_name')
                                ->name('project_name')
                                ->textSize('2xl')
                                ->label('')
                                ->columnSpanFull(),
                        ]),

                        Split::make([
                            // Left Column - Project Details
                            Section::make('Project Details')
                                ->schema([
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
                                        })
                                        ->icon('heroicon-o-flag'),

                                    TextEntry::make('is_priority')
                                        ->label('Priority')
                                        ->icon('heroicon-o-star')
                                        ->formatStateUsing(fn ($state) => $state ? 'High Priority' : 'Normal')
                                        ->color(fn ($state) => $state ? 'danger' : 'secondary'),

                                    TextEntry::make('budget')
                                        ->label('Budget')
                                        ->icon('heroicon-o-currency-dollar')
                                        ->money('USD')
                                        ->placeholder('Not set'),

                                ])
                                ->columns(2)
                                ->columnSpan(['md' => 6]),

                            Section::make('Timeline & Team')
                                ->schema([
                                    TextEntry::make('start_date')
                                        ->label('Start Date')
                                        ->icon('heroicon-o-calendar-days')
                                        ->date()
                                        ->placeholder('Not set'),

                                    TextEntry::make('end_date')
                                        ->label('End Date')
                                        ->icon('heroicon-o-calendar-days')
                                        ->date()
                                        ->placeholder('Not set'),

                                    AvatarName::make('manager')
                                        ->avatar('manager.avatar')
                                        ->name('manager.name')
                                        ->avatarSize('sm')
                                        ->textSize('sm')
                                        ->circular()
                                        ->label('Project Manager')
                                        ->placeholder('Not assigned'),
                                ])
                                ->columns(2)
                                ->columnSpan(['md' => 6]),
                        ]),

                        Section::make('Description')
                            ->schema([
                                TextEntry::make('description')
                                    ->label('')
                                    ->placeholder('No description provided')
                                    ->columnSpanFull(),
                            ])
                            ->collapsible()
                            ->collapsed(false)
                            ->columnSpanFull(),

                        // Custom Fields Section
                        CustomFieldsInfolists::make()
                            ->columnSpanFull(),

                        // Metadata Section
                        Split::make([
                            TextEntry::make('created_at')
                                ->label('Created')
                                ->icon('heroicon-o-clock')
                                ->dateTime()
                                ->columnSpan(['md' => 6]),

                            TextEntry::make('updated_at')
                                ->label('Last Updated')
                                ->icon('heroicon-o-clock')
                                ->dateTime()
                                ->columnSpan(['md' => 6]),
                        ])
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
            ]);
    }

}