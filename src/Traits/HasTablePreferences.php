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
        if (isset($preferences['visible_columns'])) {
            $table = static::applyColumnVisibilityPreferences($table, $preferences['visible_columns']);
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
    private static function applyColumnVisibilityPreferences(Table $table, array $visibleColumns): Table
    {
        // Apply the saved column visibility states to each column
        $columns = $table->getColumns();
        
        foreach ($columns as $column) {
            $columnName = $column->getName();
            
            // Only apply to toggleable columns
            if ($column->isToggleable()) {
                // Set the default hidden state - if column is in visible list, show it
                if (in_array($columnName, $visibleColumns)) {
                    $column->toggledHiddenByDefault(false); // Show the column
                } else {
                    $column->toggledHiddenByDefault(true);  // Hide the column
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
    public function saveColumnVisibility(string $resourceName, array $visibleColumns): void
    {
        $userId = auth()->id();
        
        if (!$userId) {
            return;
        }

        // Get existing preferences
        $existingPreferences = UserTablePreferences::getPreferences($userId, $resourceName);
        
        // Update the visible columns
        $existingPreferences['visible_columns'] = $visibleColumns;
        $existingPreferences['updated_at'] = now()->toISOString();

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
        
        return $preferences['visible_columns'] ?? [];
    }

    /**
     * Get currently visible columns by checking user-toggled state
     */
    public function getCurrentlyVisibleColumns(): array
    {
        $table = $this->getTable();
        
        // Get currently visible columns (respects user toggles)
        $visibleColumns = $table->getVisibleColumns();
        $visibleColumnNames = array_map(fn($col) => $col->getName(), $visibleColumns);
        
        return $visibleColumnNames;
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
              ->persistSearchInSession()
              ->persistColumnSearchesInSession();

        return $table;
    }
}