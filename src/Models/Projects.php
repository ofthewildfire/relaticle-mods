<?php

declare(strict_types=1);

namespace Ofthewildfire\RelaticleModsPlugin\Models;

use App\Models\Concerns\HasCreator;
use App\Models\Concerns\HasTeam;
use App\Models\Concerns\HasNotes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Relaticle\CustomFields\Models\Concerns\UsesCustomFields;
use Relaticle\CustomFields\Models\Contracts\HasCustomFields;

/**
 * @property string $project_name
 * @property string|null $description
 * @property string|null $status
 * @property int|null $team_id
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
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
        'status',
        'team_id',
        'created_by',
    ];

    // -----------------------------
    // Relationships
    // -----------------------------

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