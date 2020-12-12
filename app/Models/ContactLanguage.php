<?php

namespace App\Models;

use App\Contracts\AccessControl;
use App\Models\Traits\BelongsToContact;
use App\Models\Traits\HasPermissionFlags;
use App\Models\Traits\OrderableWithIndex;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

/**
 * App\Models\ContactLanguage
 *
 * @property int $id
 * @property int $contact_id
 * @property string $language_code
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
 * @property-read mixed $language
 * @property-read \App\Models\Contact $owner
 * @method static \Illuminate\Database\Eloquent\Builder|ContactLanguage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContactLanguage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContactLanguage ownedBy($contact_id)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactLanguage query()
 * @method static \Illuminate\Database\Eloquent\Builder|ContactLanguage whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactLanguage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactLanguage whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactLanguage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactLanguage whereIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactLanguage whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactLanguage whereLanguageCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactLanguage whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactLanguage whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactLanguage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactLanguage whereUpdatedBy($value)
 * @mixin \Eloquent
 * @property int|null $created_by_team
 * @property int|null $updated_by_team
 * @method static \Illuminate\Database\Eloquent\Builder|ContactLanguage whereCreatedByTeam($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactLanguage whereUpdatedByTeam($value)
 */
class ContactLanguage extends Model implements AccessControl
{
    use HasFactory;
    use Userstamps;
    use HasPermissionFlags;

    use BelongsToContact, OrderableWithIndex;

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- MODEL CONFIGURATION -------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    protected $table = 'contact_languages';

    protected $fillable = ['label','language_code','options','remarks'];

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- MAGIC METHODS -------------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    /**
     * Returns the language as a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->language_code ?? '(onbekend)';
    }

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- CUSTOM ACCESSORS ----------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    public function getLanguageAttribute() {
        return \StaticData::getLanguage($this->language_code);
    }
}
