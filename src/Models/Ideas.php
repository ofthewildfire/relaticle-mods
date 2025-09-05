<?php

declare(strict_types=1);

namespace Ofthewildfire\RelaticleModsPlugin\Models;

use App\Models\Concerns\HasCreator;
use App\Models\Concerns\HasTeam;
use App\Models\Concerns\HasNotes; 
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Relaticle\CustomFields\Models\Concerns\UsesCustomFields;
use Relaticle\CustomFields\Models\Contracts\HasCustomFields;

/**
 * @property string $idea_name
 * @property string|null $content
 * @property string|null $status
 * @property \Illuminate\Support\Carbon|null $date
 * @property int|null $team_id
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\Company> $companies
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\People> $people
 * @property \Illuminate\Database\Eloquent\Collection<int, Projects> $projects
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\Note> $notes
 */
final class Ideas extends Model implements HasCustomFields
{
    use HasFactory;
    use HasTeam;
    use HasCreator;
    use HasNotes; 
    use SoftDeletes;
    use UsesCustomFields;

    protected $table = 'ideas';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'idea_name',
        'content',
        'status',
        'date',
        'team_id', 
        'created_by', 
    ];

    /**
     * @return array<string, string|class-string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    /**
     * Automatically set created_by during creation.
     */
    protected static function booted(): void
    {
        static::creating(function (Ideas $idea): void {
            if ($idea->getAttribute('created_by') === null) {
                $userId = Filament::auth()->id() ?? auth()->id();
                if ($userId !== null) {
                    $idea->setAttribute('created_by', (int) $userId);
                }
            }

            // Ensure team_id is set if not provided
            if ($idea->getAttribute('team_id') === null && Filament::getCurrentTeam()) {
                $idea->setAttribute('team_id', Filament::getCurrentTeam()->id);
            }
        });
    }

    // -----------------------------
    // Relationships
    // -----------------------------

    /**
     * Direct many-to-many with People.
     *
     * @return BelongsToMany<\App\Models\People, $this>
     */
    public function people(): BelongsToMany
    {
        $peopleClass = config('relaticle-mods.classes.people', \App\Models\People::class);

        return $this->belongsToMany(
            $peopleClass,
            'idea_people',
            'idea_id',
            'people_id'
        );
    }

    /**
     * Many-to-many with Projects.
     *
     * @return BelongsToMany<Projects, $this>
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(
            Projects::class,
            'idea_projects',
            'idea_id',
            'projects_id'
        );
    }

    /**
     * Tasks assigned to this idea (polymorphic)
     *
     * @return MorphToMany<\App\Models\Task, $this>
     */
    public function tasks(): MorphToMany
    {
        return $this->morphToMany(\App\Models\Task::class, 'taskable');
    }

    /**
     * Notes attached to this idea (polymorphic)
     *
     * @return MorphToMany<\App\Models\Note, $this>
     */
    public function notes(): MorphToMany
    {
        return $this->morphToMany(\App\Models\Note::class, 'noteable');
    }
}