<?php

declare(strict_types=1);

namespace Ofthewildfire\RelaticleModsPlugin\Models;

use App\Models\Concerns\HasCreator;
use App\Models\Concerns\HasTeam;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Ideas extends Model
{
    use HasCreator;
    use HasFactory;
    use HasTeam;
    use SoftDeletes;

    protected $table = 'ideas';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'content',
        'creation_source',
    ];

    /**
     * @return array<string, string|class-string>
     */
    protected function casts(): array
    {
        $creationSourceEnum = config('relaticle-mods.classes.creation_source_enum');

        return [
            'creation_source' => is_string($creationSourceEnum) ? $creationSourceEnum : 'string',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Ideas $idea): void {
            if ($idea->getAttribute('created_by') === null) {
                $userId = Filament::auth()->id() ?? auth()->id();
                if ($userId !== null) {
                    $idea->setAttribute('created_by', (int) $userId);
                }
            }
        });

        static::saving(function (Ideas $idea): void {
            if ($idea->getAttribute('created_by') === null) {
                $userId = Filament::auth()->id() ?? auth()->id();
                if ($userId !== null) {
                    $idea->setAttribute('created_by', (int) $userId);
                }
            }
        });
    }

    /**
     * @return MorphToMany<\Illuminate\Database\Eloquent\Model, $this>
     */
    public function companies(): MorphToMany
    {
        $companyClass = (string) config('relaticle-mods.classes.company');

        return $this->morphedByMany($companyClass, 'ideaable');
    }
}


