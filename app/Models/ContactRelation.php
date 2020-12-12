<?php

namespace App\Models;

use App\Contracts\AccessControl;
use App\Contracts\OwnedByContact;
use App\Models\Traits\BelongsToContact;
use App\Models\Traits\HasPermissionFlags;
use App\Models\Traits\OrderableWithIndex;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Wildside\Userstamps\Userstamps;

/**
 * App\Models\ContactRelation
 *
 * @property int $id
 * @property int $contact_id
 * @property int $related_contact_id
 * @property int $index
 * @property string $label
 * @property string $options
 * @property string|null $remarks
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property-read \App\Models\Contact $contact
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User $destroyer
 * @property-read \App\Models\User|null $editor
 * @property-read \App\Models\Contact $owner
 * @property-read \App\Models\Contact $related
 * @method static \Illuminate\Database\Eloquent\Builder|ContactRelation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContactRelation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContactRelation ownedBy($contact_id)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactRelation query()
 * @method static \Illuminate\Database\Eloquent\Builder|ContactRelation whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactRelation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactRelation whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactRelation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactRelation whereIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactRelation whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactRelation whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactRelation whereRelatedContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactRelation whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactRelation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactRelation whereUpdatedBy($value)
 * @mixin \Eloquent
 * @property int|null $created_by_team
 * @property int|null $updated_by_team
 * @method static \Illuminate\Database\Eloquent\Builder|ContactRelation whereCreatedByTeam($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactRelation whereUpdatedByTeam($value)
 */
class ContactRelation extends Pivot implements AccessControl, OwnedByContact
{
    use HasFactory;
    use Userstamps;
    use HasPermissionFlags;

    use BelongsToContact, OrderableWithIndex;

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- CONTACT SETUP -------------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    public $incrementing = true;

    public $table = 'contact_relations';

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- METHODS -------------------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    public function self()
    {
        return $this;
    }

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- RELATIONS ------------------------------------------------------------------------------------------ //
    // ---------------------------------------------------------------------------------------------------------- //

    public function related()
    {
        return $this->belongsTo(Contact::class, 'related_contact_id');
    }
}
