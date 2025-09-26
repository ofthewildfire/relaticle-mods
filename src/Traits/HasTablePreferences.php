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
    protected function applyTablePreferences(Table $table, string $resourceName): Table
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
            $table = $this->applyColumnVisibility($table, $preferences['visible_columns']);
        }

        // Apply sort preferences
        if (isset($preferences['sort'])) {
            $table = $this->applySortPreferences($table, $preferences['sort']);
        }

        // Apply filter preferences
        if (isset($preferences['filters'])) {
            $table = $this->applyFilterPreferences($table, $preferences['filters']);
        }

        return $table;
    }

    /**
     * Apply column visibility preferences
     */
    private function applyColumnVisibility(Table $table, array $visibleColumns): Table
    {
        // This would need to be implemented based on how Filament handles column visibility
        // For now, we'll return the table as-is
        return $table;
    }

    /**
     * Apply sort preferences
     */
    private function applySortPreferences(Table $table, array $sort): Table
    {
        if (isset($sort['column']) && isset($sort['direction'])) {
            $table->defaultSort($sort['column'], $sort['direction']);
        }
        
        return $table;
    }

    /**
     * Apply filter preferences
     */
    private function applyFilterPreferences(Table $table, array $filters): Table
    {
        // This would need to be implemented based on how Filament handles filters
        // For now, we'll return the table as-is
        return $table;
    }

    /**
     * Save current table state as preferences
     */
    protected function saveTablePreferences(string $resourceName, array $preferences): void
    {
        $userId = auth()->id();
        
        if (!$userId) {
            return;
        }

        UserTablePreferences::savePreferences($userId, $resourceName, $preferences);
    }
}
