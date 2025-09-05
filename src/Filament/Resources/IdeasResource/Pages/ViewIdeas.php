<?php

declare(strict_types=1);

namespace Ofthewildfire\RelaticleModsPlugin\Filament\Resources\IdeasResource\Pages;

use Filament\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Ofthewildfire\RelaticleModsPlugin\Filament\Resources\IdeasResource;
use Relaticle\CustomFields\Filament\Infolists\CustomFieldsInfolists;

class ViewIdeas extends ViewRecord
{
    protected static string $resource = IdeasResource::class;

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
                Section::make('Idea Overview')
                    ->schema([
                        Split::make([
                            TextEntry::make('idea_name')
                                ->label('')
                                ->textSize('2xl')
                                ->columnSpanFull(),
                        ]),

                        Split::make([
                            Section::make('Idea Details')
                                ->schema([
                                    TextEntry::make('status')
                                        ->label('Status')
                                        ->badge()
                                        ->color(fn (string $state): string => match ($state) {
                                            'draft' => 'gray',
                                            'active' => 'success',
                                            'completed' => 'success',
                                            'cancelled' => 'danger',
                                            default => 'gray',
                                        })
                                        ->icon('heroicon-o-flag'),

                                    TextEntry::make('date')
                                        ->label('Date')
                                        ->icon('heroicon-o-calendar-days')
                                        ->date()
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
                                TextEntry::make('content')
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


