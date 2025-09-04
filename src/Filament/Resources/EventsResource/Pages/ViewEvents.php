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



// Note for future: it is possible for a view to over-ride and that drove me nuts for 2hrs! 

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
                Section::make('Event Overview')
                    ->schema([
                        Split::make([
                            AvatarName::make('banner')
                                ->avatar('banner')
                                ->name('name')
                                ->avatarSize('lg')
                                ->textSize('2xl')
                                ->square()
                                ->label('')
                                ->columnSpanFull(),
                        ]),

                        Split::make([
                            Section::make('Event Details')
                                ->schema([
                                    TextEntry::make('creation_source')
                                        ->label('Source')
                                        ->badge()
                                        ->color(fn (string $state): string => match ($state) {
                                            'manual' => 'gray',
                                            'import' => 'info',
                                            'api' => 'success',
                                            'webhook' => 'warning',
                                            default => 'gray',
                                        })
                                        ->icon('heroicon-o-arrow-down-tray'),

                                    TextEntry::make('location')
                                        ->label('Location')
                                        ->icon('heroicon-o-map-pin')
                                        ->placeholder('Not specified'),

                                ])
                                ->columns(2)
                                ->columnSpan(['md' => 6]),

                            Section::make('Timeline & Organizers')
                                ->schema([
                                    TextEntry::make('start_date')
                                        ->label('Start Date')
                                        ->icon('heroicon-o-calendar-days')
                                        ->dateTime()
                                        ->placeholder('Not set'),

                                    TextEntry::make('end_date')
                                        ->label('End Date')
                                        ->icon('heroicon-o-calendar-days')
                                        ->dateTime()
                                        ->placeholder('Not set'),

                                    AvatarName::make('accountOwner')
                                        ->avatar('accountOwner.avatar')
                                        ->name('accountOwner.name')
                                        ->avatarSize('sm')
                                        ->textSize('sm')
                                        ->circular()
                                        ->label('Account Owner')
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

                        CustomFieldsInfolists::make()
                            ->columnSpanFull(),

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