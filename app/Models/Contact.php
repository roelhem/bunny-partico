<?php

namespace App\Models;

use App\Contracts\AccessControl;
use App\Models\Traits\HasPermissionFlags;
use App\Models\Traits\Teamstamps;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Wildside\Userstamps\Userstamps;

/**
 * Class Contact
 *
 * @property-read string $name
 * @property string $name_full
 * @property string $name_initials
 * @property string $name_prefix
 * @property string $name_first
 * @property string $name_middle
 * @property string $name_last
 * @property string $name_suffix
 * @property string $nickname
 * @property PhoneNumber|null $phoneNumber
 * @property PostalAddress|null $postalAddress
 * @property Carbon $birth_date
 * @property Carbon $updated_at
 * @property Carbon $created_at
 * @package App\Models
 * @property int $id
 * @property string|null $title
 * @property string|null $remarks
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User $destroyer
 * @property-read \App\Models\User|null $editor
 * @property-read \App\Models\EmailAddress|null $emailAddress
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EmailAddress[] $emailAddresses
 * @property-read int|null $email_addresses_count
 * @property-read integer|null $age
 * @property mixed $name_parts
 * @property-read \App\Models\ContactLanguage|null $language
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ContactLanguage[] $languages
 * @property-read int|null $languages_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PhoneNumber[] $phoneNumbers
 * @property-read int|null $phone_numbers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PostalAddress[] $postalAddresses
 * @property-read int|null $postal_addresses_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Contact[] $related
 * @property-read int|null $related_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Contact[] $relatesTo
 * @property-read int|null $relates_to_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ContactRelation[] $relations
 * @property-read int|null $relations_count
 * @method static \Illuminate\Database\Eloquent\Builder|Contact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contact query()
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereNameFirst($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereNameFull($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereNameInitials($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereNameLast($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereNameMiddle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereNamePrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereNameSuffix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereUpdatedBy($value)
 * @mixin \Eloquent
 * @property int|null $created_by_team
 * @property int|null $updated_by_team
 * @method static Builder|Contact authView()
 * @method static Builder|Contact whereCreatedByTeam($value)
 * @method static Builder|Contact whereUpdatedByTeam($value)
 * @property-read \App\Models\Team|null $creatorTeam
 * @property-read \App\Models\Team|null $editorTeam
 */
class Contact extends Model implements AccessControl
{
    use HasFactory;
    use Userstamps;
    use Teamstamps;
    use HasPermissionFlags;

    protected $dates = ['birth_date','created_at','updated_at','deleted_at'];

    static $namePartMapping = [
        'name_full'     => 'full',
        'name_initials' => 'initials',
        'name_prefix'   => 'prefix',
        'name_first'    => 'first',
        'name_middle'   => 'middle',
        'name_last'     => 'last',
        'name_suffix'   => 'suffix',
    ];

    public function getNamePartsAttribute() {
        $result = [];
        foreach (static::$namePartMapping as $attribute => $key) {
            $result[$key] = $this->{$attribute};
        }
        return $result;
    }

    public function setNamePartsAttribute(array $values = []) {
        foreach (static::$namePartMapping as $attribute => $key) {
            if(\Arr::has($values, $key)) {
                $this->{$attribute} = \Arr::get($values, $key);
            } elseif (\Arr::has($values, $key)) {
                $this->{$attribute} = \Arr::get($values, $attribute);
            }
        }
    }

    public function getNameAttribute() {
        return $this->name();
    }

    public function name() {
        if($this->name_full) {
            return $this->name_full;
        }

        $parts = \Arr::where([
            $this->name_prefix,
            $this->name_first,
            $this->name_middle,
            $this->name_last,
            $this->name_suffix
        ], function ($value, $key) {
            return is_string($value) && trim($value) !== '';
        });

        return implode(' ', $parts);
    }

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- GETTER METHODS ------------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    /**
     * Returns the day when this person turns the given age.
     *
     * When $age is omitted, the next birthday of this person is returned. If no birth_date was found in this
     * Person, null will be returned.
     *
     * @param integer|null $age
     * @return Carbon|null
     */
    public function getBirthDay($age = null) {
        if($this->birth_date === null) {
            return null;
        }

        if($age === null) {
            $age = $this->age + 1;
        }

        $result = clone $this->birth_date;
        $result->addYears($age);

        return $result;
    }

    /**
     * Returns the age of this person at the given moment.
     *
     * @param Carbon|string|null $at
     * @return integer|null
     */
    public function getAge($at = null) {
        $birth_date = $this->birth_date;
        if($birth_date === null) {
            return null;
        } else {
            return $birth_date->diffInYears(Carbon::parse($at), false);
        }
    }

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- CUSTOM ACCESSORS ----------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    /**
     * Returns the age of this Person if the birth date is known.
     *
     * @return integer|null
     */
    public function getAgeAttribute() {
        return $this->getAge();
    }

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- CUSTOM RESOLVERS ----------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    public function resolveBirthDayField($root, array $args = []) {
        return $this->getBirthDay(\Arr::get($args, 'age', null));
    }

    public function resolveAgeField($root, array $args = []) {
        return $this->getAge(\Arr::get($args, 'at', null));
    }

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- SCOPES --------------------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    public function scopeAuthView(Builder $query) {
        $user = \Auth::user();
        return $query->where('id', $user->contact_id)
                     ->orWhere('created_by', $user->id)
                     ->orWhere('created_by_team', $user->current_team_id);
    }

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- RELATIONAL DEFINITIONS ----------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    /**
     * Returns the primary Email Address of this Person.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function emailAddress() {
        return $this->hasOne(EmailAddress::class, EmailAddress::$contactKey)->orderBy('index');
    }

    /**
     * Gives all the EmailAddresses that belong to this Person.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function emailAddresses() {
        return $this->hasMany(EmailAddress::class, EmailAddress::$contactKey)->orderBy('index');
    }

    /**
     * Returns the primary address of this Person.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function postalAddress() {
        return $this->hasOne(PostalAddress::class, PostalAddress::$contactKey)->orderBy('index');
    }

    /**
     * Gives all the PersonAddresses that belong to this Person.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function postalAddresses() {
        return $this->hasMany(PostalAddress::class, PostalAddress::$contactKey)->orderBy('index');
    }

    /**
     * Returns the primary address of this Person.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function phoneNumber() {
        return $this->hasOne(PhoneNumber::class, PhoneNumber::$contactKey)->orderBy('index');
    }

    /**
     * Gives all the PersonAddresses that belong to this Person.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function phoneNumbers() {
        return $this->hasMany(PhoneNumber::class, PhoneNumber::$contactKey)->orderBy('index');
    }

    /**
     * Returns the primary address of this Person.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function language() {
        return $this->hasOne(ContactLanguage::class, ContactLanguage::$contactKey)->orderBy('index');
    }

    /**
     * Gives all the PersonAddresses that belong to this Person.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function languages() {
        return $this->hasMany(ContactLanguage::class, ContactLanguage::$contactKey)->orderBy('index');
    }

    public function relations() {
        return $this->hasMany(ContactRelation::class, ContactLanguage::$contactKey)->orderBy('index');
    }

    public function related() {
        return $this->belongsToMany(
            Contact::class,
            'contact_relations',
            ContactRelation::$contactKey,
            'related_contact_id',
        )->withPivot([
            'id',
            'label',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            'created_by_team',
            'updated_by_team',
        ])->using(ContactRelation::class);
    }

    public function relatesTo() {
        return $this->belongsToMany(
            Contact::class,
            'contact_relations',
            'related_contact_id',
            ContactRelation::$contactKey,
        )->withPivot([
            'id',
            'label',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            'created_by_team',
            'updated_by_team',
        ])->using(ContactRelation::class);
    }

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- IMPLEMENT: AccessControl --------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    public function getSubjectId()
    {
        return $this->id;
    }
}
