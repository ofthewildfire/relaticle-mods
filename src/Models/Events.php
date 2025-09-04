<?php

declare(strict_types=1);

namespace Ofthewildfire\RelaticleModsPlugin\Models;

use App\Models\Concerns\HasCreator;
use App\Models\Concerns\HasNotes;
use App\Models\Concerns\HasTeam;
use App\Enums\CreationSource;
use Filament\Facades\Filament;
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


/**
 * @property string $name
 * @property string|null $description
 * @property Carbon $start_date
 * @property Carbon|null $end_date
 * @property string $location
 * @property int|null $team_id
 * @property int|null $created_by
 * @property int|null $account_owner_id
 * @property CreationSource|string $creation_source
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @property \App\Models\User|null $accountOwner
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\People> $people
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\Opportunity> $opportunities
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\Task> $tasks
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\Note> $notes
 * @property \Illuminate\Database\Eloquent\Collection<int, Projects> $projects
 */
final class Events extends Model implements HasCustomFields
{
    use HasFactory;
    use HasTeam;
    use HasCreator;
    use HasNotes;
    use SoftDeletes;
    use UsesCustomFields;

    protected $table = 'events';

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
        'account_owner_id',
        'team_id',
        'created_by',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'creation_source' => 'web', // fallback
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string|class-string>
     */
    protected function casts(): array
    {
        $creationSourceEnum = config('relaticle-mods.classes.creation_source_enum');

        return [
            'creation_source' => is_string($creationSourceEnum) ? $creationSourceEnum : 'string',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    /**
     * Boot the model and set created_by/team_id on creation.
     */
    protected static function booted(): void
    {
        static::creating(function (Events $event): void {
            // Set created_by
            if ($event->getAttribute('created_by') === null) {
                $userId = Filament::auth()->id() ?? auth()->id();
                if ($userId !== null) {
                    $event->setAttribute('created_by', (int) $userId);
                }
            }

            // Set team_id if not set
            if ($event->getAttribute('team_id') === null && Filament::getCurrentTeam()) {
                $event->setAttribute('team_id', Filament::getCurrentTeam()->id);
            }
        });
    }

    // -----------------------------
    // Accessors
    // -----------------------------

    public function getBannerAttribute(): string
    {
        $avatarServiceClass = config('relaticle-mods.classes.avatar_service');
        $avatarService = is_string($avatarServiceClass) ? app($avatarServiceClass) : null;

        return $avatarService ? $avatarService->generateAuto(name: $this->name) : '';
    }

    // -----------------------------
    // Relationships
    // -----------------------------

    /**
     * Team member responsible for managing the event
     *
     * @return BelongsTo<\App\Models\User, $this>
     */
    public function accountOwner(): BelongsTo
    {
        $userClass = config('relaticle-mods.classes.user', \App\Models\User::class);
        return $this->belongsTo($userClass, 'account_owner_id');
    }

    public function notes(): MorphToMany
    {
        $noteClass = config('relaticle-mods.classes.note', \App\Models\Note::class);
        return $this->morphToMany($noteClass, 'noteable', 'noteable', 'noteable_id', 'note_id');
    }

    /**
     * People attending or involved in the event
     *
     * @return BelongsToMany<\App\Models\People, $this>
     */
    public function people(): BelongsToMany
    {
        $peopleClass = config('relaticle-mods.classes.people', \App\Models\People::class);

        return $this->belongsToMany($peopleClass, 'event_people', 'event_id', 'people_id')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Opportunities linked to this event
     *
     * @return HasMany<\App\Models\Opportunity, $this>
     */
    public function opportunities(): HasMany
    {
        $opportunityClass = config('relaticle-mods.classes.opportunity', \App\Models\Opportunity::class);
        return $this->hasMany($opportunityClass, 'event_id'); // adjust foreign key if needed
    }

    /**
     * Tasks assigned to this event (polymorphic)
     *
     * @return MorphToMany<\App\Models\Task, $this>
     */
    public function tasks(): MorphToMany
    {
        $taskClass = config('relaticle-mods.classes.task', \App\Models\Task::class);
        return $this->morphToMany($taskClass, 'taskable', 'taskable', 'taskable_id', 'task_id');
    }

    /**
     * Projects associated with this event
     *
     * @return BelongsToMany<Projects, $this>
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(
            Projects::class,
            'project_events',
            'event_id',
            'projects_id'
        );
    }
}
