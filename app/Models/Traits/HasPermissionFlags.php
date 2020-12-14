<?php


namespace App\Models\Traits;


use App\Contracts\AccessControl;
use App\Contracts\OwnedByContact;
use App\Enums\AccessLevel;
use App\Models\Contact;
use App\Models\PermissionFlag;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Trait HasPermissionFlags
 *
 * @method static \Illuminate\Database\Eloquent\Builder where($a)
 * @package App\Models\Traits
 */
trait HasPermissionFlags
{
    public static function byIdWithAccess($id)
    {
        /** @var static $result */
        $result = static::where(['id' => $id])->first();
        if($result !== null && $result->hasAccess('view', \Auth::user())) {
            return $result;
        } else {
            return null;
        }
    }

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
        return AccessLevel::getDefaultAccessLevel($ability, $this);
    }

    public function getAbilities()
    {
        return AccessLevel::getAbilities($this);
    }

    public function getAbilityScopes(string $ability)
    {
        return AccessLevel::getAbilityScopes($ability, $this);
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

    public function hasAccess(string $ability, ?User $user = null)
    {
        if($this instanceof AccessControl) {
            return $this->getAccessLevel($ability)->hasAccess($this, $user);
        } else {
            $className = static::class;
            $accessControlName = AccessControl::class;
            throw new \Error("Class $className doesn't implement $accessControlName. Therefore, it can't determine if a user has access to this entity.");
        }
    }

    public function getAccess(?User $user = null)
    {
        if($this instanceof AccessControl) {
            return AccessLevel::getAccess($this, $user);
        } else {
            $className = static::class;
            $accessControlName = AccessControl::class;
            throw new \Error("Class $className doesn't implement $accessControlName. Therefore, it can't determine the access-level of a user.");
        }
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

        // Check the OAuth scopes of the user.
        foreach ($this->getAbilityScopes($ability) as $scope) {

            if(!\Auth::user()->tokenCan($scope)) {
                return $query->where(function ($query) use ($ability) {
                    return $this->scopeMaxAccessLevel($query, $ability, AccessLevel::PUBLIC());
                });
            }
        }

        // No restrictions with admin permissions.
        if(\Auth::user()->is_admin && \Auth::user()->tokenCan('admin-access')) {
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
