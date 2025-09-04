<?php

declare(strict_types=1);

namespace Ofthewildfire\RelaticleModsPlugin\Filament\Resources\EventsResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PeopleRelationManager extends RelationManager
{
    protected static string $relationship = 'people';

    // Disable create/edit of People here; only attach existing records from host app
    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('display')
                    ->label('Person')
                    ->getStateUsing(function ($record): string {
                        $name = $record->name ?? trim((string) (($record->first_name ?? '') . ' ' . ($record->last_name ?? '')));
                        return $name !== '' ? $name : (string) ($record->email ?? $record->id);
                    })
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->recordSelectTitleAttribute('name')
                    ->recordSelectSearchColumns(['name', 'first_name', 'last_name', 'email'])
                    ->recordSelectOptionsQuery(fn ($query) => $query->orderBy('name')),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}