<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Wildside\Userstamps\Userstamps;

class Individual extends Model
{
    use HasFactory;
    use Userstamps;

    protected $dates = ['birth_date','created_at','updated_at','deleted_at'];

    static $namePartMapping = [
        'name_full'   => 'full',
        'name_prefix' => 'prefix',
        'name_first'  => 'first',
        'name_middle' => 'middle',
        'name_last'   => 'last',
        'name_suffix' => 'suffix',
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
    // ----- RELATIONAL DEFINITIONS ----------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    /**
     * Returns the primary Email Address of this Person.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function emailAddress() {
        return $this->hasOne(EmailAddress::class, EmailAddress::$individualKey)->orderBy('index');
    }

    /**
     * Gives all the EmailAddresses that belong to this Person.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function emailAddresses() {
        return $this->hasMany(EmailAddress::class, EmailAddress::$individualKey)->orderBy('index');
    }

    /**
     * Returns the primary address of this Person.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function postalAddress() {
        return $this->hasOne(PostalAddress::class, PostalAddress::$individualKey)->orderBy('index');
    }

    /**
     * Gives all the PersonAddresses that belong to this Person.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function postalAddresses() {
        return $this->hasMany(PostalAddress::class, PostalAddress::$individualKey)->orderBy('index');
    }

    /**
     * Returns the primary address of this Person.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function phoneNumber() {
        return $this->hasOne(PhoneNumber::class, PhoneNumber::$individualKey)->orderBy('index');
    }

    /**
     * Gives all the PersonAddresses that belong to this Person.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function phoneNumbers() {
        return $this->hasMany(PhoneNumber::class, PhoneNumber::$individualKey)->orderBy('index');
    }
}
