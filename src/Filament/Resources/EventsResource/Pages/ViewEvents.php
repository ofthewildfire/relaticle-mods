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
                                    TextEntry::make('status')
                                        ->label('Status')
                                        ->badge()
                                        ->color(fn (string $state): string => match ($state) {
                                            'draft' => 'gray',
                                            'published' => 'success',
                                            'cancelled' => 'danger',
                                            'completed' => 'success',
                                            default => 'gray',
                                        })
                                        ->icon('heroicon-o-flag'),

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