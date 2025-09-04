<?php

declare(strict_types=1);

namespace Ofthewildfire\RelaticleModsPlugin\Models;

use App\Models\Concerns\HasCreator;
use App\Models\Concerns\HasTeam;
use App\Models\Concerns\HasNotes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Relaticle\CustomFields\Models\Concerns\UsesCustomFields;
use Relaticle\CustomFields\Models\Contracts\HasCustomFields;

/**
 * @property string $project_name
 * @property string|null $description
 * @property float|null $budget
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property string $status
 * @property bool $is_priority
 * @property string|null $contact_email
 * @property int|null $company_id
 * @property int|null $manager_id
 * @property int|null $team_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property \App\Models\Company|null $company
 * @property \App\Models\User|null $manager
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\People> $teamMembers
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\Opportunity> $opportunities
 * @property \Illuminate\Database\Eloquent\Collection<int, Events> $events
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\Note> $notes
 */
class Projects extends Model implements HasCustomFields
{
    use HasFactory;
    use HasTeam;
    use HasCreator;
    use HasNotes;
    use SoftDeletes;
    use UsesCustomFields;

    protected $table = 'custom_projects';

    protected $fillable = [
        'project_name',
        'description',
        'budget',
        'start_date',
        'end_date',
        'status',
        'is_priority',
        'contact_email',
        'company_id',
        'manager_id',
        'team_id',
    ];

    protected function casts(): array
    {
        return [
            'budget' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'is_priority' => 'boolean',
        ];
    }

    // -----------------------------
    // Relationships
    // -----------------------------

    public function company(): BelongsTo
    {
        $class = config('relaticle-mods.classes.company', \App\Models\Company::class);
        return $this->belongsTo($class, 'company_id');
    }

    public function manager(): BelongsTo
    {
        $class = config('relaticle-mods.classes.user', \App\Models\User::class);
        return $this->belongsTo($class, 'manager_id');
    }

    public function teamMembers(): BelongsToMany
    {
        $class = config('relaticle-mods.classes.people', \App\Models\People::class);
        return $this->belongsToMany($class, 'project_team_members', 'projects_id', 'people_id');
    }

    public function opportunities(): BelongsToMany
    {
        $class = config('relaticle-mods.classes.opportunity', \App\Models\Opportunity::class);
        return $this->belongsToMany($class, 'project_opportunities', 'projects_id', 'opportunity_id');
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(
            Events::class,
            'project_events',
            'projects_id',
            'event_id'
        );
    }

    public function tasks()
    {
        return $this->morphToMany(\App\Models\Task::class, 'taskable');
    }

    public function notes()
    {
        return $this->morphToMany(\App\Models\Note::class, 'noteable');
    }

    public function people(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\People::class, 'project_team_members', 'projects_id', 'people_id');
    }

    /**
     * Many-to-many with Ideas.
     *
     * @return BelongsToMany<Ideas, $this>
     */
    public function ideas(): BelongsToMany
    {
        return $this->belongsToMany(
            Ideas::class,
            'idea_projects',
            'projects_id',
            'idea_id'
        );
    }



}