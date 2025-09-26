<?php

declare(strict_types=1);

namespace Ofthewildfire\RelaticleModsPlugin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTablePreferences extends Model
{
    protected $fillable = [
        'user_id',
        'resource_name',
        'preferences',
    ];

    protected $casts = [
        'preferences' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Get preferences for a specific user and resource
     */
    public static function getPreferences(int $userId, string $resourceName): array
    {
        $preferences = static::where('user_id', $userId)
            ->where('resource_name', $resourceName)
            ->first();

        return $preferences ? $preferences->preferences : [];
    }

    /**
     * Save preferences for a specific user and resource
     */
    public static function savePreferences(int $userId, string $resourceName, array $preferences): void
    {
        static::updateOrCreate(
            [
                'user_id' => $userId,
                'resource_name' => $resourceName,
            ],
            [
                'preferences' => $preferences,
            ]
        );
    }
}
