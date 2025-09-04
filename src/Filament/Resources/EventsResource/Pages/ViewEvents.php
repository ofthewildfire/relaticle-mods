<?php

declare(strict_types=1);

namespace Ofthewildfire\RelaticleModsPlugin\Filament\Resources\EventsResource\Pages;

// Import the *same* relation managers used by Company
use App\Filament\App\Resources\CompanyResource\RelationManagers;
use App\Filament\Components\Infolists\AvatarName;
use Ofthewildfire\RelaticleModsPlugin\Filament\Resources\EventsResource;
use Filament\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Relaticle\CustomFields\Filament\Infolists\CustomFieldsInfolists;

class ViewEvents extends ViewRecord
{
    protected static string $resource = EventsResource::class;

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
                \Filament\Infolists\Components\Split::make([
                    \Filament\Infolists\Components\Section::make([
                        \Filament\Infolists\Components\Split::make([
                            AvatarName::make('banner')
                                ->avatar('banner')
                                ->name('name')
                                ->avatarSize('lg')
                                ->textSize('xl')
                                ->square()
                                ->label(''),

                            AvatarName::make('creator')
                                ->avatar('creator.avatar')
                                ->name('creator.name')
                                ->avatarSize('sm')
                                ->textSize('sm')
                                ->circular()
                                ->label('Created By'),

                            AvatarName::make('accountOwner')
                                ->avatar('accountOwner.avatar')
                                ->name('accountOwner.name')
                                ->avatarSize('sm')
                                ->textSize('sm')
                                ->circular()
                                ->label('Account Owner'),
                        ])->from('md'),

                        CustomFieldsInfolists::make(),
                    ])->columnSpan(['md' => 8]),

                    \Filament\Infolists\Components\Section::make([
                        \Filament\Infolists\Components\TextEntry::make('start_date')
                            ->label('Start Date')
                            ->icon('heroicon-o-calendar')
                            ->dateTime(),

                        \Filament\Infolists\Components\TextEntry::make('end_date')
                            ->label('End Date')
                            ->icon('heroicon-o-calendar')
                            ->dateTime(),

                        \Filament\Infolists\Components\TextEntry::make('location')
                            ->label('Location')
                            ->icon('heroicon-o-map-pin'),

                        \Filament\Infolists\Components\TextEntry::make('created_at')
                            ->label('Created')
                            ->icon('heroicon-o-clock')
                            ->dateTime(),

                        \Filament\Infolists\Components\TextEntry::make('updated_at')
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