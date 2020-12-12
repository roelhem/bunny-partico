<?php


namespace App\Models\Traits;


use App\Contracts\AccessControl;
use App\Contracts\OwnedByContact;
use App\Enums\AccessLevel;
use App\Models\Contact;
use App\Models\PermissionFlag;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasPermissionFlags
{
    /** @var int|AccessLevel|array $defaultPermissionLevels */
    protected $defaultPermissionLevels = [
        'view' => AccessLevel::SUBJECT,
        'update' => AccessLevel::CREATOR_TEAM,
        'delete' => AccessLevel::CREATOR,
    ];

    /**
     * Relation to permission flags.
     *
     * @return MorphMany
     */
    public function permissionFlags()
    {
        return $this->morphMany(PermissionFlag::class, 'entity');
    }

    /**
     * Returns the default permission level of an ability.
     *
     * @param string $ability
     * @return AccessLevel
     */
    public function getDefaultAccessLevel(string $ability)
    {
        $level = null;
        if(!is_array($this->defaultPermissionLevels)) {
            $level = AccessLevel::get($this->defaultPermissionLevels);
        } elseif (isset($this->defaultPermissionLevels[$ability])) {
            \Log::info('Levels', $this->defaultPermissionLevels);
            $level = AccessLevel::get($this->defaultPermissionLevels[$ability]);
            \Log::info('Level', ['ability' => $ability, 'level' => $level]);
        }

        if($level !== null) {
            return $level;
        } else {
            return AccessLevel::default();
        }
    }

    /**
     * Returns the permission level of some ability.
     *
     * @param string $ability
     * @return AccessLevel
     */
    public function getAccessLevel(string $ability): AccessLevel
    {
        /** @var PermissionFlag|null $flag */
        $flag = $this->permissionFlags()->ability($ability)->first();
        if($flag) {
            return $flag->level;
        } else {
            return $this->getDefaultAccessLevel($ability);
        }
    }

    /**
     * @param string $ability
     * @param mixed $level
     * @return static
     */
    public function setAccessLevel(string $ability, $level)
    {
        $this->permissionFlags()->updateOrInsert([
            'ability' => $ability,
        ], [
            'ability' => $ability,
            'level' => AccessLevel::get($level)->value,
            'entity_type' => static::class,
            'entity_id' => $this->id,
            'created_at' => now(),
            'updated_at' => now(),
            'created_by' => \Auth::id(),
            'updated_by' => \Auth::id()
        ]);
        return $this;
    }

    /**
     * @param Builder|\Illuminate\Database\Eloquent\Builder $query
     * @param string $ability
     * @param mixed $level
     * @return Builder|\Illuminate\Database\Eloquent\Builder
     */
    public function scopeMaxAccessLevel($query, string $ability, $level)
    {
        return AccessLevel::get($level)->withMaxAccessLevel(
            $query, $ability, $this->getTable(), static::class,
            $this->getDefaultAccessLevel($ability),
        );
    }

    /**
     * @param Builder|\Illuminate\Database\Eloquent\Builder $query
     * @param string $ability
     * @return Builder|\Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAccess($query, string $ability)
    {
        // Public permission
        if(\Auth::user() === null) {
            return $query->where(function ($query) use ($ability) {
                return $this->scopeMaxAccessLevel($query, $ability, AccessLevel::PUBLIC());
            });
        }

        // No restrictions with admin permissions.
        if(\Auth::user()->is_admin) {
            return $query;
        }

        // Creator permission.
        $query->where(function ($query) use ($ability) {
            /** @var Builder|\Illuminate\Database\Eloquent\Builder $query */
            return $query
                ->where($this->getTable().'.created_by', \Auth::id())
                ->where(function ($query) use ($ability) {
                    return $this->scopeMaxAccessLevel($query, $ability, AccessLevel::CREATOR());
                });
        })->orWhere(function ($query) use ($ability) {
            /** @var Builder|\Illuminate\Database\Eloquent\Builder $query */
            return $query
                ->where($this->getTable().'.created_by_team', \Auth::user()->current_team_id)
                ->where(function ($query) use ($ability) {
                    return $this->scopeMaxAccessLevel($query, $ability, AccessLevel::CREATOR_TEAM());
                });
        })->orWhere(function ($query) use ($ability) {
            return $this->scopeMaxAccessLevel($query, $ability, AccessLevel::USER());
        });

        // Select subject if connected to a contact entry.
        if(\Auth::user()->contact_id !== null) {
            $query->orWhere(function ($query) use ($ability) {
                /** @var Builder|\Illuminate\Database\Eloquent\Builder $query */
                return $this->scopeSubject($query, \Auth::user())
                    ->where(function ($query) use ($ability) {
                        return $this->scopeMaxAccessLevel($query, $ability, AccessLevel::SUBJECT());
                    });
            });
        }

        // Return te query
        return $query;
    }

    /**
     * @param Builder|\Illuminate\Database\Eloquent\Builder $query
     * @param OwnedByContact|int|null $contactRef
     * @return Builder|\Illuminate\Database\Eloquent\Builder
     */
    public function scopeSubject($query, $contactRef)
    {
        // Get the contact key.
        $contactKey = null;
        if(isset(static::$contactKey)) {
            $contactKey = static::$contactKey;
        } elseif (static::class === Contact::class) {
            $contactKey = 'id';
        }

        // Don't allow anything when no contact key was found.
        if($contactKey === null) {
            return $query->where('id', false);
        }

        // Get the contact reference.
        if($contactRef instanceof OwnedByContact) {
            $contactRef = $contactRef->getOwnerId();
        }

        // Return the result.
        return $query->where($contactKey, $contactRef);
    }

    /**
     * @param Builder|\Illuminate\Database\Eloquent\Builder $query
     * @return Builder|\Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithViewAccess($query)
    {
        return $this->scopeWithAccess($query, 'view');
    }

    /**
     * Returns the id of the user that created this entity.
     *
     * @return int|null
     */
    public function getCreatorId()
    {
        return $this->created_by;
    }

    /**
     * Returns the id of the team that created this entity.
     *
     * @return int|null
     */
    public function getCreatorTeamId()
    {
        return $this->created_by_team;
    }

    /**
     * @return AccessLevel
     */
    public function authAccessLevel()
    {
        if($this instanceof AccessControl) {
            return AccessLevel::getAccess($this, \Auth::user());
        } else {
            return AccessLevel::default();
        }
    }
}
