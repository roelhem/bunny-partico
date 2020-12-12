<?php


namespace App\Models\Traits;


use App\Contracts\AccessControl;
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
    public function getDefaultPermissionLevel(string $ability)
    {
        $level = null;
        if(!is_array($this->defaultPermissionLevels)) {
            $level = AccessLevel::get($this->defaultPermissionLevels);
        } elseif (isset($this->defaultPermissionLevels[$ability])) {
            $level = AccessLevel::get($this->defaultPermissionLevels[$ability]);
        }

        if($level === null) {
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
            return $this->getDefaultPermissionLevel($ability);
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
            $this->getDefaultPermissionLevel($ability),
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
        if(\Auth::id() === null) {
            return $query->where(function ($query) use ($ability) {
                return $this->scopeMaxAccessLevel($query, $ability, AccessLevel::PUBLIC());
            });
        }


        // Creator permission.
        $query->where(function ($query) use ($ability) {
            /** @var Builder|\Illuminate\Database\Eloquent\Builder $query */
            return $query
                ->where('created_by', \Auth::id())
                ->where(function ($query) use ($ability) {
                    return $this->scopeMaxAccessLevel($query, $ability, AccessLevel::CREATOR());
                });
        })->orWhere(function ($query) use ($ability) {
            /** @var Builder|\Illuminate\Database\Eloquent\Builder $query */
            return $query
                ->where('created_by_team', \Auth::user()->current_team_id)
                ->where(function ($query) use ($ability) {
                    return $this->scopeMaxAccessLevel($query, $ability, AccessLevel::CREATOR_TEAM());
                });
        })->orWhere(function ($query) use ($ability) {
            return $this->scopeMaxAccessLevel($query, $ability, AccessLevel::USER());
        });

        // Select subject if connected to a contact entry.
        if(\Auth::user()->contact_id !== null) {
            $contactKey = $this->getContactIdKey();

            if($contactKey !== null && \Auth::user()->contact_id !== null) {
                $query->orWhere(function ($query) use ($contactKey, $ability) {
                    /** @var Builder|\Illuminate\Database\Eloquent\Builder $query */
                    return $query
                        ->where($contactKey, \Auth::user()->contact_id)
                        ->where(function ($query) use ($ability) {
                            return $this->scopeMaxAccessLevel($query, $ability, AccessLevel::SUBJECT());
                        });
                });
            }
        }

        // Return te query
        return $query;
    }

    /**
     * @param Builder|\Illuminate\Database\Eloquent\Builder $query
     * @return Builder|\Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithViewAccess($query)
    {
        return $this->scopeWithAccess($query, 'view');
    }

    public function getCreatorId()
    {
        return $this->created_by;
    }

    public function getCreatorTeamId()
    {
        return $this->created_by_team;
    }

    /**
     * Returns the name of the column in the table that stores the associated contact_id.
     *
     * @return string
     */
    private function getContactIdKey()
    {
        if(isset(static::$contactKey)) {
            return static::$contactKey;
        } elseif (static::class === Contact::class) {
            return 'id';
        } else {
            return null;
        }
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
