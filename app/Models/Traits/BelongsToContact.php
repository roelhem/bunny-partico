<?php


namespace App\Models\Traits;


use App\Contracts\OwnedByContact;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait BelongsToIndividual
 *
 * @property-read Contact $contact
 */
trait BelongsToContact
{

    public static $contactKey = 'contact_id';

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- RELATIONAL DEFINITIONS ----------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    /**
     * Gives the Person where this Model belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contact() {
        return $this->belongsTo(Contact::class, static::$contactKey);
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
        return $this->belongsTo(Contact::class, static::$contactKey);
    }

    /**
     * @inheritdoc
     */
    public function getOwner(): ?Contact
    {
        return $this->contact;
    }

    /**
     * @inheritdoc
     */
    public function getOwnerId(): ?int
    {
        return $this->{static::$contactKey};
    }

    /**
     * Scope that filters only the objects that are owned by the Person with the provided id.
     *
     * @param Builder $query
     * @param integer $contact_id
     * @return Builder
     */
    public function scopeOwnedBy($query, $contact_id) {
        return $query->where(static::$contactKey, '=', $contact_id);
    }

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- IMPLEMENTS: AccessControl -------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    /**
     * @inheritDoc
     */
    public function isSubject($contactRef) {
        if($contactRef instanceof OwnedByContact) {
            $contactRef = $contactRef->getOwnerId();
        }
        if($contactRef === null) {
            return false;
        }
        return $contactRef === $this->getOwnerId();
    }
}
