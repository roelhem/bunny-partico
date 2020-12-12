<?php

namespace App\Models;

use App\Contracts\AccessControl;
use App\Contracts\OwnedByContact;
use App\Models\Traits\BelongsToContact;
use App\Models\Traits\HasPermissionFlags;
use App\Models\Traits\OrderableWithIndex;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

/**
 * App\Models\EmailAddress
 *
 * @property int $id
 * @property int $contact_id
 * @property int $index
 * @property string $label
 * @property string $options
 * @property string $email_address
 * @property string|null $remarks
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property-read \App\Models\Contact $contact
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User $destroyer
 * @property-read \App\Models\User|null $editor
 * @property-read mixed $link
 * @property-read mixed $with_name
 * @property-read \App\Models\Contact $owner
 * @method static \Illuminate\Database\Eloquent\Builder|EmailAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailAddress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailAddress ownedBy($contact_id)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailAddress query()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailAddress whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailAddress whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailAddress whereEmailAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailAddress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailAddress whereIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailAddress whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailAddress whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailAddress whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailAddress whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailAddress whereUpdatedBy($value)
 * @mixin \Eloquent
 * @property int|null $created_by_team
 * @property int|null $updated_by_team
 * @method static \Illuminate\Database\Eloquent\Builder|EmailAddress whereCreatedByTeam($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailAddress whereUpdatedByTeam($value)
 */
class EmailAddress extends Model implements AccessControl, OwnedByContact
{
    use HasFactory;
    use Userstamps;
    use HasPermissionFlags;

    use BelongsToContact, OrderableWithIndex;

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- MODEL CONFIGURATION -------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    protected $table = 'email_addresses';

    protected $fillable = ['label','email_address','options','remarks'];

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- MAGIC METHODS -------------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    /**
     * Returns the email_address as a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->email_address ?? '(onbekend)';
    }

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- CUSTOM ACCESSORS ----------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    public function getWithNameAttribute() {
        return trim($this->contact->name) . ' <' . trim($this->email_address) . '>';
    }

    public function getLinkAttribute() {
        return 'mailto:'.urlencode($this->contact->name.' ') . '<'.trim($this->email_address).'>';
    }

}
