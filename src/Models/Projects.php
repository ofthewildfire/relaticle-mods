<?php

declare(strict_types=1);

namespace Ofthewildfire\RelaticleModsPlugin\Models;

use App\Models\Concerns\HasCreator;
use App\Models\Concerns\HasTeam;
use App\Models\Company;
use App\Models\Opportunity;
use App\Models\People;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Projects extends Model
{
    use HasFactory;
    use HasTeam;
    use HasCreator;
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'custom_projects';

    /**
     * The attributes that are mass assignable.
     */
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
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'project_name' => 'string',
        'description' => 'string',
        'budget' => 'float',
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => 'string',
        'is_priority' => 'boolean',
        'contact_email' => 'string',
    ];
    
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function tasks(): MorphToMany
    {
        return $this->morphToMany(\App\Models\Task::class, 'taskable');
    }

    public function notes(): MorphToMany
    {
        return $this->morphToMany(\App\Models\Note::class, 'noteable');
    }

    public function teamMembers(): BelongsToMany
    {
        return $this->belongsToMany(People::class, 'project_team_members', 'projects_id', 'people_id');
    }

    public function opportunities(): BelongsToMany
    {
        return $this->belongsToMany(Opportunity::class, 'project_opportunities', 'projects_id', 'opportunity_id');
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Events::class, 'project_events', 'projects_id', 'event_id');
    }
}