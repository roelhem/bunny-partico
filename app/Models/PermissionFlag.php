<?php

namespace App\Models;

use App\Enums\AccessLevel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

/**
 * App\Models\PermissionFlag
 *
 * @property int $id
 * @property string $ability
 * @property string $entity_type
 * @property int $entity_id
 * @property AccessLevel $level
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User $destroyer
 * @property-read \App\Models\User|null $editor
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionFlag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionFlag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionFlag query()
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionFlag whereAbility($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionFlag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionFlag whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionFlag whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionFlag whereEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionFlag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionFlag whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionFlag whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionFlag whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class PermissionFlag extends Model
{
    use HasFactory;
    use Userstamps;

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- MODEL SETUP ---------------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    protected $casts = [
        'level' => AccessLevel::class,
    ];

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- SCOPES --------------------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    public function scopeAbility(Builder $query, string $ability)
    {
        return $query->where(['ability' => $ability]);
    }

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- RELATIONS ------------------------------------------------------------------------------------------ //
    // ---------------------------------------------------------------------------------------------------------- //

    public function entity()
    {
        return $this->morphTo();
    }
}
