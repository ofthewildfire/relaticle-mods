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
        if (isset($preferences['toggled_columns'])) {
            $table = static::applyColumnVisibilityPreferences($table, $preferences['toggled_columns']);
        }

        // Apply sort preferences
        if (isset($preferences['sort'])) {
            $table = static::applySortPreferences($table, $preferences['sort']);
        }

        // Apply filter preferences
        if (isset($preferences['filters'])) {
            $table = static::applyFilterPreferences($table, $preferences['filters']);
        }

        return $table;
    }

    /**
     * Apply column visibility preferences
     */
    private static function applyColumnVisibilityPreferences(Table $table, array $toggledColumns): Table
    {
        // Apply the saved column visibility states to each column
        $columns = $table->getColumns();
        
        foreach ($columns as $column) {
            $columnName = $column->getName();
            
            // If we have saved visibility state for this column, apply it
            if (isset($toggledColumns[$columnName])) {
                $isVisible = $toggledColumns[$columnName];
                
                // Force the column to be visible or hidden based on saved state
                if ($isVisible) {
                    $column->visible();
                } else {
                    $column->hidden();
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
     * Apply filter preferences
     */
    private static function applyFilterPreferences(Table $table, array $filters): Table
    {
        // This would need to be implemented based on how Filament handles filters
        // For now, we'll return the table as-is
        return $table;
    }

    /**
     * Save current table state as preferences
     */
    protected static function saveTablePreferences(string $resourceName, array $preferences): void
    {
        $userId = auth()->id();
        
        if (!$userId) {
            return;
        }

        UserTablePreferences::savePreferences($userId, $resourceName, $preferences);
    }

    /**
     * Save column toggle state to preferences
     */
    protected static function saveColumnToggleState(string $resourceName, array $toggledColumns): void
    {
        $userId = auth()->id();
        
        if (!$userId) {
            return;
        }

        // Get existing preferences
        $existingPreferences = UserTablePreferences::getPreferences($userId, $resourceName);
        
        // Update only the toggled_columns part
        $existingPreferences['toggled_columns'] = $toggledColumns;
        $existingPreferences['updated_at'] = now()->toISOString();

        UserTablePreferences::savePreferences($userId, $resourceName, $existingPreferences);
    }

    /**
     * Save column toggle preferences using Filament's session data
     */
    public static function syncColumnPreferencesToDatabase(string $resourceName): void
    {
        $userId = auth()->id();
        
        if (!$userId) {
            return;
        }

        // Get the current session-stored column toggles
        $sessionKey = "filament.tables." . static::getTableSessionKey($resourceName) . ".toggledColumns";
        $toggledColumns = session()->get($sessionKey, []);
        
        $toggledColumns = session()->get($sessionKey);
        static::saveColumnToggleState($resourceName, $toggledColumns ?? []);    }

    /**
     * Get the table session key for a resource
     */
    private static function getTableSessionKey(string $resourceName): string
    {
        // This matches Filament's internal session key pattern
        return str_replace(['\\', '/'], '.', $resourceName);
    }
    /**
     * Initialize column preferences from database to session
     */
    public static function initializeColumnPreferences(string $resourceName): void
    {
        $userId = auth()->id();
        
        if (!$userId) {
            return;
        }

        $preferences = UserTablePreferences::getPreferences($userId, $resourceName);
        
        if (isset($preferences['toggled_columns']) && !empty($preferences['toggled_columns'])) {
            $sessionKey = "filament.tables." . static::getTableSessionKey($resourceName) . ".toggledColumns";
            session()->put($sessionKey, $preferences['toggled_columns']);
        }
    }

    /**
     * Get the table with persistence enabled
     */
    protected static function getTableWithPersistence(Table $table, string $resourceName): Table
    {
        // Initialize column preferences from database to session
        static::initializeColumnPreferences($resourceName);
        
        // Apply saved preferences to set initial column visibility
        $table = static::applyTablePreferences($table, $resourceName);
        
        // Enable Filament's built-in persistence for future changes
        $table->persistTableColumnSearchInSession()
              ->persistTableFiltersInSession()
              ->persistTableSortInSession()
              ->persistTableColumnTogglesInSession();

        return $table;
    }
}
