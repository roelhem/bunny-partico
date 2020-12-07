<?php


namespace App\Models\Traits;


use App\Models\Individual;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait BelongsToIndividual
 *
 * @property-read Individual $individual
 */
trait BelongsToIndividual
{

    public static $individualKey = 'individual_id';

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- RELATIONAL DEFINITIONS ----------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    /**
     * Gives the Person where this Model belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function individual() {
        return $this->belongsTo(Individual::class, static::$individualKey);
    }

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- IMPLEMENTS: OwnedByPerson -------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    /**
     * Gives the owner of this model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner() {
        return $this->belongsTo(Individual::class, static::$individualKey);
    }

    /**
     * @inheritdoc
     */
    public function getOwner()
    {
        return $this->individual;
    }

    /**
     * @inheritdoc
     */
    public function getOwnerId()
    {
        return $this->{static::$individualKey};
    }

    /**
     * Scope that filters only the objects that are owned by the Person with the provided id.
     *
     * @param Builder $query
     * @param integer $individual_id
     * @return Builder
     */
    public function scopeOwnedBy($query, $individual_id) {
        return $query->where(static::$individualKey, '=', $individual_id);
    }
}
