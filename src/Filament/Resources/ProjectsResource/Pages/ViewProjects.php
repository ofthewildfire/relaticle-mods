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
                            Section::make('Project Details')
                                ->schema([
                                    TextEntry::make('status')
                                        ->label('Status')
                                        ->badge()
                                        ->color(fn (string $state): string => match ($state) {
                                            'planning' => 'gray',
                                            'active' => 'success',
                                            'on_hold' => 'warning',
                                            'completed' => 'success',
                                            'cancelled' => 'danger',
                                            default => 'gray',
                                        })
                                        ->icon('heroicon-o-flag'),
                                ])
                                ->columns(2)
                                ->columnSpan(['md' => 6]),

                            Section::make('Metadata')
                                ->schema([
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

                        CustomFieldsInfolists::make()
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
            ]);
    }

}