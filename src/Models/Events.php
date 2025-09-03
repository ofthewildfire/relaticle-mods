<?php

declare(strict_types=1);

namespace Ofthewildfire\RelaticleModsPlugin\Models;

use App\Enums\CreationSource;
use App\Models\Concerns\HasCreator;
use App\Models\Concerns\HasNotes;
use App\Models\Concerns\HasTeam;
use App\Models\People;
use App\Services\AvatarService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Relaticle\CustomFields\Models\Concerns\UsesCustomFields;
use Relaticle\CustomFields\Models\Contracts\HasCustomFields;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property string $name
 * @property string $description
 * @property Carbon $start_date
 * @property Carbon|null $end_date
 * @property string $location
 * @property Carbon|null $deleted_at
 * @property CreationSource $creation_source
 * @property-read string $created_by
 */
final class Events extends Model implements HasCustomFields, HasMedia
{
    protected $table = 'events';
    
    use HasCreator;
    use HasFactory;
    use HasNotes;
    use HasTeam;
    use InteractsWithMedia;
    use SoftDeletes;
    use UsesCustomFields;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'location',
        'creation_source',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'creation_source' => CreationSource::WEB,
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string|class-string>
     */
    protected function casts(): array
    {
        return [
            'creation_source' => CreationSource::class,
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    public function getBannerAttribute(): string
    {
        $banner = $this->getFirstMediaUrl('banner');

        return $banner === '' || $banner === '0' ? app(AvatarService::class)->generateAuto(name: $this->name) : $banner;
    }

    /**
     * Team member responsible for managing the event
     *
     * @return BelongsTo<\App\Models\User, $this>
     */
    public function accountOwner(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'account_owner_id');
    }

    /**
     * @return BelongsToMany<People, $this>
     */
    public function people(): BelongsToMany
    {
        return $this->belongsToMany(People::class, 'event_people', 'event_id', 'people_id')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * @return HasMany<\App\Models\Opportunity, $this>
     */
    public function opportunities(): HasMany
    {
        return $this->hasMany(\App\Models\Opportunity::class);
    }

    /**
     * @return MorphToMany<\App\Models\Task, $this>
     */
    public function tasks(): MorphToMany
    {
        return $this->morphToMany(\App\Models\Task::class, 'taskable');
    }

    /**
     * @return MorphToMany<\App\Models\Note, $this>
     */
    public function notes(): MorphToMany
    {
        return $this->morphToMany(\App\Models\Note::class, 'noteable');
    }

    /**
     * @return BelongsToMany<Projects, $this>
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Projects::class, 'project_events', 'event_id', 'projects_id');
    }
}