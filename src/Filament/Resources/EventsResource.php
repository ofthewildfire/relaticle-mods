<?php

declare(strict_types=1);

namespace Ofthewildfire\RelaticleModsPlugin\Filament\Resources;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Ofthewildfire\RelaticleModsPlugin\Filament\Resources\EventsResource\Pages;
use Ofthewildfire\RelaticleModsPlugin\Filament\Resources\EventsResource\RelationManagers;
use Ofthewildfire\RelaticleModsPlugin\Filament\Resources\EventsResource\RelationManagers\ProjectsRelationManager;
use Ofthewildfire\RelaticleModsPlugin\Models\Events;
use Ofthewildfire\RelaticleModsPlugin\Traits\HasTablePreferences;
use Relaticle\CustomFields\Filament\Forms\Components\CustomFieldsComponent;
use Relaticle\CustomFields\Filament\Tables\Columns\CustomFieldsColumn;

class EventsResource extends Resource
{
    use HasTablePreferences;
    protected static ?string $model = Events::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Workspace';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
    
            Forms\Components\Select::make('status')
                ->options([
                    'draft' => 'Draft',
                    'published' => 'Published',
                    'cancelled' => 'Cancelled',
                    'completed' => 'Completed',
                ]),
    
            Forms\Components\DateTimePicker::make('start_date'),
    
            Forms\Components\DateTimePicker::make('end_date'),
    
            // Forms\Components\Textarea::make('description')
            //     ->columnSpanFull(),
    
            CustomFieldsComponent::make()->columnSpanFull(),
        ]);
    }


    public static function table(Table $table): Table
    {
        $table = $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Event Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Start Date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('End Date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('Created By')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                // CustomFieldsColumn::make(),
            ])
            ->defaultSort('start_date', 'desc')
            ->filters([
                Tables\Filters\TrashedFilter::make(),

                Tables\Filters\Filter::make('upcoming')
                    ->label('Upcoming')
                    ->query(fn (Builder $query) => $query->where('start_date', '>=', now())),

                Tables\Filters\Filter::make('past')
                    ->label('Past')
                    ->query(fn (Builder $query) => $query->where('end_date', '<', now())),
            ])
            ->groups([
                Tables\Grouping\Group::make('start_date')
                    ->date()
                    ->collapsible(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->icon('heroicon-o-eye'),
                Tables\Actions\EditAction::make()->icon('heroicon-o-pencil'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultPaginationPageOption(25);

        // Apply persistence and preferences
        return static::getTableWithPersistence($table, 'events');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PeopleRelationManager::class,
            RelationManagers\ProjectsRelationManager::class,
            RelationManagers\TasksRelationManager::class,
            RelationManagers\NotesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvents::route('/create'),
            'view' => Pages\ViewEvents::route('/{record}'),
            'edit' => Pages\EditEvents::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = Filament::getTenant()?->id ?? auth()->user()?->current_team_id ?? auth()->user()?->team_id;

        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->when($teamId !== null, fn (Builder $query) => $query->where('team_id', $teamId));
    }
}