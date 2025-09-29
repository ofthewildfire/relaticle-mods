<?php

declare(strict_types=1);

namespace Ofthewildfire\RelaticleModsPlugin\Traits;

use Filament\Tables\Table;
use Ofthewildfire\RelaticleModsPlugin\Models\UserTablePreferences;

trait HasTablePreferences
{
    /**
     * Apply user's saved table preferences to the table
     */
    protected static function applyTablePreferences(Table $table, string $resourceName): Table
    {
        $userId = auth()->id();
        
        if (!$userId) {
            return $table;
        }

        $preferences = UserTablePreferences::getPreferences($userId, $resourceName);
        
        if (empty($preferences)) {
            return $table;
        }

        // Apply column visibility preferences
        if (isset($preferences['hidden_columns'])) {
            $table = static::applyColumnVisibilityPreferences($table, $preferences['hidden_columns']);
        }

        // Apply sort preferences
        if (isset($preferences['sort'])) {
            $table = static::applySortPreferences($table, $preferences['sort']);
        }

        return $table;
    }

    /**
     * Apply column visibility preferences
     */
    private static function applyColumnVisibilityPreferences(Table $table, array $hiddenColumns): Table
    {
        // Apply the saved column visibility states to each column
        $columns = $table->getColumns();
        
        foreach ($columns as $column) {
            $columnName = $column->getName();
            
            // Only apply to toggleable columns
            if ($column->isToggleable()) {
                // If this column should be hidden, hide it
                if (in_array($columnName, $hiddenColumns)) {
                    $column->toggleable(isToggledHiddenByDefault: true);
                } else {
                    $column->toggleable(isToggledHiddenByDefault: false);
                }
            }
        }

        return $table;
    }

    /**
     * Apply sort preferences
     */
    private static function applySortPreferences(Table $table, array $sort): Table
    {
        if (isset($sort['column']) && isset($sort['direction'])) {
            $table->defaultSort($sort['column'], $sort['direction']);
        }
        
        return $table;
    }

    /**
     * Save column visibility state directly to database
     */
    public function saveColumnVisibility(string $resourceName, array $hiddenColumns): void
    {
        $userId = auth()->id();
        
        if (!$userId) {
            \Log::info('saveColumnVisibility: No user ID');
            return;
        }

        // Get existing preferences
        $existingPreferences = UserTablePreferences::getPreferences($userId, $resourceName);
        
        // Update the hidden columns
        $existingPreferences['hidden_columns'] = $hiddenColumns;
        $existingPreferences['updated_at'] = now()->toISOString();

        \Log::info('saveColumnVisibility', [
            'user_id' => $userId,
            'resource' => $resourceName,
            'hidden_columns' => $hiddenColumns,
            'existing_preferences' => $existingPreferences
        ]);

        UserTablePreferences::savePreferences($userId, $resourceName, $existingPreferences);
    }

    /**
     * Get saved column visibility preferences
     */
    public static function getSavedColumnVisibility(string $resourceName): array
    {
        $userId = auth()->id();
        
        if (!$userId) {
            return [];
        }

        $preferences = UserTablePreferences::getPreferences($userId, $resourceName);
        
        return $preferences['hidden_columns'] ?? [];
    }

    /**
     * Get currently hidden columns by checking user-toggled state
     */
    public function getCurrentlyHiddenColumns(): array
    {
        $table = $this->getTable();
        
        // Get all columns (including toggleable ones)
        $allColumns = $table->getColumns();
        $allColumnNames = array_map(fn($col) => $col->getName(), $allColumns);
        
        // Get currently visible columns (respects user toggles)
        $visibleColumns = $table->getVisibleColumns();
        $visibleColumnNames = array_map(fn($col) => $col->getName(), $visibleColumns);
        
        // Hidden columns = All columns - Visible columns
        $hiddenColumns = array_diff($allColumnNames, $visibleColumnNames);
        
        // Log for debugging
        \Log::info('getCurrentlyHiddenColumns', [
            'all_columns' => $allColumnNames,
            'visible_columns' => $visibleColumnNames,
            'hiddenColumns' => array_values($hiddenColumns)
        ]);
        
        return array_values($hiddenColumns);
    }

    /**
     * Get the table with persistence enabled
     */
    protected static function getTableWithPersistence(Table $table, string $resourceName): Table
    {
        // Apply saved preferences to set initial column visibility
        $table = static::applyTablePreferences($table, $resourceName);
        
        // Enable Filament's built-in persistence for filters, sort, and search
        $table->persistFiltersInSession()
              ->persistSortInSession()
              ->persistSearchInSession();

        return $table;
    }
}