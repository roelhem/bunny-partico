<?php

namespace App\Models;

use App\Contracts\AccessControl;
use App\Contracts\OwnedByContact;
use App\Models\Traits\BelongsToContact;
use App\Models\Traits\HasCountryCode;
use App\Models\Traits\HasPermissionFlags;
use App\Models\Traits\OrderableWithIndex;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use libphonenumber\geocoding\PhoneNumberOfflineGeocoder;
use Wildside\Userstamps\Userstamps;
use libphonenumber\PhoneNumber as PhoneNumberHelper;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

/**
 * Class PhoneNumber
 *
 * @property PhoneNumberHelper $phone_number
 * @property string $raw
 * @package App\Models
 * @property int $id
 * @property int $contact_id
 * @property int $index
 * @property string $label
 * @property string $options
 * @property string $country_code
 * @property string|null $remarks
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property-read \App\Models\Contact $contact
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User $destroyer
 * @property-read \App\Models\User|null $editor
 * @property-read \CommerceGuys\Addressing\Country\Country|null $country
 * @property-read mixed $extension
 * @property-read mixed $link
 * @property-read mixed $location
 * @property-read int $number_type
 * @property-read \App\Models\Contact $owner
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber ownedBy($contact_id)
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber query()
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber whereIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber whereUpdatedBy($value)
 * @mixin \Eloquent
 * @property int|null $created_by_team
 * @property int|null $updated_by_team
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber whereCreatedByTeam($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber whereUpdatedByTeam($value)
 */
class PhoneNumber extends Model implements AccessControl, OwnedByContact
{
    use Userstamps;
    use HasFactory;

    use BelongsToContact;
    use HasCountryCode, OrderableWithIndex, HasPermissionFlags;

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- MODEL CONFIGURATION -------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    protected $table = 'phone_numbers';

    protected $fillable = ['label','country_code','phone_number','options','remarks'];

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- MAGIC METHODS -------------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    /**
     * @var PhoneNumberUtil
     */
    protected $util;

    /**
     * PersonPhoneNumber constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->util = resolve(PhoneNumberUtil::class);
        parent::__construct($attributes);
    }

    /**
     * Returns the phone_number as a string.
     *
     * @return string
     * @throws
     */
    public function __toString()
    {
        if ($this->country_code === 'NL') {
            return $this->format(PhoneNumberFormat::NATIONAL);
        } else {
            return $this->format(PhoneNumberFormat::INTERNATIONAL);
        }
    }

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- HELPER METHODS ------------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    /**
     * Returns the phone number in the given phoneNumberFormat.
     *
     * @param $phoneNumberFormat
     * @return string
     */
    public function format($phoneNumberFormat) {
        return $this->util->format($this->phone_number, $phoneNumberFormat);
    }

    /**
     * Returns the phone-number in a format that can be dialed form the given country.
     *
     * @param string $country_code
     * @return string
     */
    public function formatFor($country_code = 'NL') {
        return $this->util->formatOutOfCountryCallingNumber($this->phone_number, $country_code);
    }

    /**
     * Returns the phone-number in a format such it can be dialed from a mobile number form the given country.
     *
     * @param string $country_code
     * @param bool $numFormatting
     * @return string
     */
    public function formatMobile($country_code = 'NL', $numFormatting = true) {
        return $this->util->formatNumberForMobileDialing($this->phone_number, $country_code, $numFormatting);
    }

    public function location(?string $locale = null) {
        if($locale === null) {
            $locale = config('app.locale');
        }
        $geocoder = resolve(PhoneNumberOfflineGeocoder::class);
        return $geocoder->getDescriptionForNumber($this->phone_number, $locale);
    }

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- CUSTOM ACCESSORS ----------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    /**
     * Returns the phone_number as a PhoneNumber
     *
     * @param $value
     * @return PhoneNumberHelper
     * @throws
     */
    public function getPhoneNumberAttribute($value) {
        return $this->util->parse($value, $this->country_code);
    }

    /**
     * Returns the number type of this PhoneNumber
     *
     * @return int
     */
    public function getNumberTypeAttribute() {
        return $this->util->getNumberType($this->phone_number);
    }

    public function getExtensionAttribute() {
        return $this->phone_number->getExtension();
    }

    public function getRawAttribute() {
        return $this->phone_number->getRawInput();
    }

    public function getLinkAttribute() {
        $res =  $this->format(PhoneNumberFormat::RFC3966);
        return str_replace('tel:', 'tel://', $res);
    }

    public function getLocationAttribute() {
        return $this->location();
    }

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- CUSTOM RESOLVER METHODS ---------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    public function resolveNumberAttribute(PhoneNumber $root, array $args) {
        switch ($args['format']) {
            case 0:
            case 1:
            case 2:
            case 3: return $this->format($args['format']);
            case 4: return $this->formatFor($args['from']);
            case 5: return $this->formatMobile($args['from'], true);
            case 6: return $this->formatMobile($args['from'], false);
            default: return $this->raw;
        }
    }

    public function resolveLocationAttribute(PhoneNumber $root, array $args) {
        return $this->location($args['locale']);
    }

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- CUSTOM MUTATORS ------------------------------------------------------------------------------------ //
    // ---------------------------------------------------------------------------------------------------------- //

    /**
     * Saves the phone_number in the right format.
     *
     * @param $value
     * @throws
     */
    public function setPhoneNumberAttribute($value) {
        if(is_string($value)) {
            $value = $this->util->parse($value, $this->country_code);
        }

        if($value instanceof PhoneNumberHelper) {
            $this->attributes['phone_number'] = $this->util->format($value, PhoneNumberFormat::E164);
        }
    }
}
