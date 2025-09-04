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
        'ideas' => \Ofthewildfire\RelaticleModsPlugin\Models\Ideas::class,
        'projects' => \Ofthewildfire\RelaticleModsPlugin\Models\Projects::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | External Classes (host-app overrides)
    |--------------------------------------------------------------------------
    |
    | Allow host applications to override concrete classes used by the plugin.
    | Defaults target a typical Laravel app structure.
    |
    */
    'classes' => [
        'user' => \App\Models\User::class,
        'people' => \App\Models\People::class,
        'company' => \App\Models\Company::class,
        'opportunity' => \App\Models\Opportunity::class,
        'task' => \App\Models\Task::class,
        'note' => \App\Models\Note::class,
        'avatar_service' => \App\Services\AvatarService::class,
        'creation_source_enum' => \App\Enums\CreationSource::class,
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
        'ideas' => \Ofthewildfire\RelaticleModsPlugin\Models\Ideas::class,
        'projects' => \Ofthewildfire\RelaticleModsPlugin\Models\Projects::class,
    ],
];