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
     * Capture current table state and save to database
     */
    protected static function captureAndSaveTableState(string $resourceName): void
    {
        $userId = auth()->id();
        
        if (!$userId) {
            return;
        }

        // Get current request parameters that represent table state
        $request = request();
        
        $preferences = [
            'sort' => [
                'column' => $request->get('sort'),
                'direction' => $request->get('direction', 'asc'),
            ],
            'filters' => $request->get('filters', []),
            'search' => $request->get('search'),
            'timestamp' => now()->toISOString(),
        ];

        // Remove empty values
        $preferences = array_filter($preferences, function($value) {
            return !empty($value);
        });

        static::saveTablePreferences($resourceName, $preferences);
    }
}
