<?php


namespace App\Enums;

use App\Contracts\AccessControl;
use App\Models\PermissionFlag;
use App\Models\Traits\HasPermissionFlags;
use App\Models\User;
use BenSampo\Enum\Enum;
use Illuminate\Database\Query\Builder;


/**
 * @method static static PUBLIC()
 * @method static static MACHINE()
 * @method static static USER()
 * @method static static SUBJECT_TEAM()
 * @method static static SUBJECT()
 * @method static static CREATOR_TEAM()
 * @method static static CREATOR()
 * @method static static ADMIN_TEAM()
 * @method static static ADMIN()
 */
final class AccessLevel extends Enum
{
    const PUBLIC       = 0;
    const MACHINE      = 5;
    const USER         = 10;
    const SUBJECT_TEAM = 50;
    const SUBJECT      = 60;
    const CREATOR_TEAM = 100;
    const CREATOR      = 110;
    const ADMIN_TEAM   = 250;
    const ADMIN        = 255;

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- DEFAULT LEVEL -------------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    static $default = AccessLevel::ADMIN_TEAM;

    /**
     * @return AccessLevel
     */
    public static function default()
    {
        try {
            return new AccessLevel(static::$default);
        } catch (\BenSampo\Enum\Exceptions\InvalidEnumMemberException $exception) {
            throw new \Error($exception);
        }
    }

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- INITIALISATION ------------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    /**
     * Retrieves a new AccessLevel instance.
     *
     * @param $value
     * @return AccessLevel
     */
    public static function get($value): ?AccessLevel
    {
        if($value instanceof AccessLevel) {
            return $value;
        }

        $result = static::coerce($value);
        if($result === null) {
            return $result;
        }

        if(is_numeric($value)) {
            $possible = array_filter(parent::getValues(), function($v) use ($value) {
                return $v <= $value;
            });
            sort($possible);
            $value = array_pop($possible);
            return static::coerce($value);
        }

        return null;
    }

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- QUERIES -------------------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    /**
     * Filters all elements that have an access level equal to or lower than this access level for a certain ability.
     *
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query
     * @param string $ability
     * @param string $tableName The name of the table that contains the permission flag.
     * @param string $entityType The type of the entity.
     * @param mixed|null $defaultAccessLevel The default access level.
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    public function withMaxAccessLevel($query, string $ability, string $tableName, string $entityType, $defaultAccessLevel = null)
    {
        // Filter all entities with an access level higher than this access level for the given ability.
        $levelSubQuery = PermissionFlag::select('level')->where([
            'ability' => $ability,
            'entity_type' => $entityType
        ])->whereColumn('entity_id', $tableName.'.id');
        $query->where($levelSubQuery, '<=', $this->value);

        // Get the default access level.
        $defaultAccessLevel = AccessLevel::get($defaultAccessLevel);
        if($defaultAccessLevel !== null && $this->value >= $defaultAccessLevel->value) {
            /**
             * @param Builder|\Illuminate\Database\Eloquent\Builder $query
             * @return Builder|\Illuminate\Database\Eloquent\Builder
             */
            $sub = function ($query) use ($ability, $tableName, $entityType) {
                return $query->from('permission_flags')->select('level')->where([
                    'ability' => $ability,
                    'entity_type' => $entityType,
                ])->whereColumn('entity_id', $tableName.'.id');
            };
            $query->orWhereNotExists($sub);
        }

        // Return the resulting query.
        return $query;
    }

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- CHECKS ON MODELS ----------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    public static function getAccess(AccessControl $to, ?User $user = null)
    {
        if($user === null) {
            return AccessLevel::PUBLIC();
        }

        if(isset($user->is_admin) && $user->is_admin) {
            return AccessLevel::ADMIN();
        }

        $creatorId = $to->getCreatorId();
        if($creatorId !== null && $user->id === $creatorId) {
            return AccessLevel::CREATOR();
        }

        $creatorTeamId = $to->getCreatorTeamId();
        if($creatorTeamId !== null && $user->current_team_id === $creatorTeamId) {
            return AccessLevel::CREATOR_TEAM();
        }

        $subjectId = $to->getSubjectId();
        if($subjectId !== null && $user->contact_id === $subjectId) {
            return AccessLevel::SUBJECT();
        }

        return AccessLevel::USER();
    }

    /**
     * Checks if a given user has access to the provided Model that has permission flags.
     *
     * @param AccessControl $to
     * @param User|null $user
     * @return boolean
     */
    public function hasAccess(AccessControl $to, ?User $user = null)
    {
        $accessLevel = AccessLevel::getAccess($to, $user);
        return $this->value <= $accessLevel->value;
    }
}
