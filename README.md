# Relaticle Mods Plugin

This package adds Events, Projects, and Ideas functionality to Relaticle CRM with full relationship management.

## Installation Steps

### 1. Install the package
```bash
composer require ofthewildfire/relaticle-mods
```

### 2. Publish and run migrations
```bash
php artisan vendor:publish --tag="relaticle-mods-migrations"
php artisan migrate
```

### 3. Add traits to existing models

**In your `app/Models/Note.php`:**
```php
use Ofthewildfire\RelaticleModsPlugin\Traits\HasNotesRelationships;

class Note extends Model
{
    use HasNotesRelationships;
}
```

**In your `app/Models/Task.php`:**
```php
use Ofthewildfire\RelaticleModsPlugin\Traits\HasTasksRelationships;

class Task extends Model
{
    use HasTasksRelationships;
}
```

**In your `app/Models/People.php`:**
```php
use Ofthewildfire\RelaticleModsPlugin\Traits\HasPeopleRelationships;

class People extends Model
{
    use HasPeopleRelationships;
}
```

**In your `app/Models/Company.php`:**
```php
use Ofthewildfire\RelaticleModsPlugin\Traits\HasCompanyRelationships;

class Company extends Model
{
    use HasCompanyRelationships;
}
```

**If you have a separate `app/Models/Projects.php` (not the plugin one):**
```php
use Ofthewildfire\RelaticleModsPlugin\Traits\HasProjectsRelationships;

class Projects extends Model
{
    use HasProjectsRelationships;
}
```

### 4. Register the plugin in Filament
In `app/Providers/Filament/AppPanelProvider.php`:
```php
use Ofthewildfire\RelaticleModsPlugin\RelaticleModsPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            RelaticleModsPlugin::make(),
        ]);
}
```

### 5. Clear cache
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload
```

## Requirements

- PHP ^8.2
- Laravel ^11.0
- Filament ^3.0
- Existing Relaticle CRM installation