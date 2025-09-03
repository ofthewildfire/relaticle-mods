<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Navigation Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how the resources appear in the Filament navigation
    |
    */
    'navigation' => [
        'events' => [
            'group' => 'Workspace',
            'sort' => 4,
            'icon' => 'heroicon-o-calendar-days',
        ],
        'projects' => [
            'group' => 'Workspace',
            'sort' => 5,
            'icon' => 'heroicon-o-briefcase',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Model Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the models used by the package
    |
    */
    'models' => [
        'events' => \Ofthewildfire\RelaticleModsPlugin\Models\Events::class,
        'projects' => \Ofthewildfire\RelaticleModsPlugin\Models\Projects::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Morph Map Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the morph map entries for polymorphic relationships
    |
    */
    'morph_map' => [
        'events' => \Ofthewildfire\RelaticleModsPlugin\Models\Events::class,
        'projects' => \Ofthewildfire\RelaticleModsPlugin\Models\Projects::class,
    ],
];