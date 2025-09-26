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
use Ofthewildfire\RelaticleModsPlugin\Filament\Resources\IdeasResource\Pages;
use Ofthewildfire\RelaticleModsPlugin\Filament\Resources\IdeasResource\RelationManagers;
use Ofthewildfire\RelaticleModsPlugin\Models\Ideas;
use Relaticle\CustomFields\Filament\Forms\Components\CustomFieldsComponent;
use Relaticle\CustomFields\Filament\Tables\Columns\CustomFieldsColumn;

class IdeasResource extends Resource
{
    protected static ?string $model = Ideas::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    protected static ?string $navigationGroup = 'Workspace';

    protected static ?int $navigationSort = 6;
    
    protected static ?string $recordTitleAttribute = 'idea_name';

    public static function form(Form $form): Form
{
    return $form->schema([
        Forms\Components\TextInput::make('idea_name')
            ->required()
            ->maxLength(255),

        Forms\Components\Select::make('status')
            ->options([
                'draft' => 'Draft',
                'active' => 'Active',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
            ]),

        Forms\Components\DatePicker::make('date'),

        Forms\Components\Textarea::make('content')
            ->label('Description')
            ->columnSpanFull(),

        CustomFieldsComponent::make()->columnSpanFull(),
    ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('idea_name')
                    ->label('Idea Name')
                    ->limit(100)
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('content')
                    ->label('Description')
                    ->limit(50)
                    ->wrap()
                    ->searchable()
                    ->toggleable(),
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
                CustomFieldsColumn::make()
                    ->label('Custom Fields')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultPaginationPageOption(25)
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistColumnSearchesInSession()
            ->persistTableStateInSession();
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
            'index' => Pages\ListIdeas::route('/'),
            'create' => Pages\CreateIdeas::route('/create'),
            'view' => Pages\ViewIdeas::route('/{record}'),
            'edit' => Pages\EditIdeas::route('/{record}/edit'),
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


