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
use Ofthewildfire\RelaticleModsPlugin\Filament\Resources\ProjectsResource\Pages;
use Ofthewildfire\RelaticleModsPlugin\Filament\Resources\ProjectsResource\RelationManagers;
use Ofthewildfire\RelaticleModsPlugin\Models\Projects;
use Relaticle\CustomFields\Filament\Forms\Components\CustomFieldsComponent;

class ProjectsResource extends Resource
{
    protected static ?string $model = Projects::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Workspace';

    protected static ?int $navigationSort = 5;

    // public static function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //             Forms\Components\TextInput::make('project_name')
    //                 ->required()
    //                 ->maxLength(255),
    //             Forms\Components\Textarea::make('description')
    //                 ->columnSpanFull(),
    //             Forms\Components\TextInput::make('budget')
    //                 ->numeric()
    //                 ->prefix('$'),
    //             Forms\Components\DatePicker::make('start_date')
    //                 ->required(),
    //             Forms\Components\DatePicker::make('end_date'),
    //             Forms\Components\Select::make('status')
    //                 ->options([
    //                     'planning' => 'Planning',
    //                     'active' => 'Active',
    //                     'on_hold' => 'On Hold',
    //                     'completed' => 'Completed',
    //                     'cancelled' => 'Cancelled',
    //                 ])
    //                 ->required(),
    //             Forms\Components\Toggle::make('is_priority')
    //                 ->label('Priority Project'),
    //             Forms\Components\TextInput::make('contact_email')
    //                 ->email()
    //                 ->maxLength(255),
    //             Forms\Components\Select::make('company_id')
    //                 ->relationship('company', 'name')
    //                 ->searchable()
    //                 ->preload(),
    //             Forms\Components\Select::make('manager_id')
    //                 ->relationship('manager', 'name')
    //                 ->searchable()
    //                 ->preload(),
    //             CustomFieldsComponent::make()->columns(1),
    //         ]);
    // }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('project_name')
                ->required()
                ->maxLength(255),
    
            Forms\Components\Select::make('status')
                ->options([
                    'planning' => 'Planning',
                    'active' => 'Active',
                    'on_hold' => 'On Hold',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                ]),
    
            Forms\Components\Textarea::make('description')
                ->columnSpanFull(),
    
            CustomFieldsComponent::make()->columnSpanFull(),
        ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('project_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'planning' => 'gray',
                        'active' => 'success',
                        'on_hold' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    }),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'planning' => 'Planning',
                        'active' => 'Active',
                        'on_hold' => 'On Hold',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
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
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PeopleRelationManager::class,
            RelationManagers\TasksRelationManager::class,
            RelationManagers\NotesRelationManager::class,
            RelationManagers\EventsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProjects::route('/create'),
            'view' => Pages\ViewProjects::route('/{record}'),
            'edit' => Pages\EditProjects::route('/{record}/edit'),
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