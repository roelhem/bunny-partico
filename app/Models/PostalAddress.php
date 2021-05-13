<?php


namespace App\Models;


use App\Contracts\AccessControl;
use App\Contracts\OwnedByContact;
use App\Enums\AddressField;
use App\Models\Traits\AddressingAttributeMappings;
use App\Models\Traits\BelongsToContact;
use App\Models\Traits\HasCountryCode;
use App\Models\Traits\HasPermissionFlags;
use App\Models\Traits\OrderableWithIndex;
use CommerceGuys\Addressing\AddressFormat\AddressFormat;
use CommerceGuys\Addressing\AddressFormat\AddressFormatRepositoryInterface;
use CommerceGuys\Addressing\AddressInterface;
use CommerceGuys\Addressing\Formatter\FormatterInterface;
use CommerceGuys\Addressing\Formatter\PostalLabelFormatterInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

/**
 * App\Models\PostalAddress
 *
 * @property int $id
 * @property int $contact_id
 * @property int $index
 * @property string $label
 * @property string $options
 * @property string $country_code
 * @property string|null $administrative_area
 * @property string|null $locality
 * @property string|null $dependent_locality
 * @property string|null $postal_code
 * @property string|null $sorting_code
 * @property string|null $address_line_1
 * @property string|null $address_line_2
 * @property string|null $organisation
 * @property string $locale
 * @property string|null $remarks
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property-read \App\Models\Contact $contact
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User $destroyer
 * @property-read \App\Models\User|null $editor
 * @property-read AddressFormat $address_format
 * @property-read \CommerceGuys\Addressing\Country\Country|null $country
 * @property-read \App\Models\Contact $owner
 * @method static \Illuminate\Database\Eloquent\Builder|PostalAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PostalAddress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PostalAddress ownedBy($contact_id)
 * @method static \Illuminate\Database\Eloquent\Builder|PostalAddress query()
 * @method static \Illuminate\Database\Eloquent\Builder|PostalAddress whereAddressLine1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostalAddress whereAddressLine2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostalAddress whereAdministrativeArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostalAddress whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostalAddress whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostalAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostalAddress whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostalAddress whereDependentLocality($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostalAddress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostalAddress whereIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostalAddress whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostalAddress whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostalAddress whereLocality($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostalAddress whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostalAddress whereOrganisation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostalAddress wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostalAddress whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostalAddress whereSortingCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostalAddress whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostalAddress whereUpdatedBy($value)
 * @mixin \Eloquent
 * @property int|null $created_by_team
 * @property int|null $updated_by_team
 * @method static \Illuminate\Database\Eloquent\Builder|PostalAddress whereCreatedByTeam($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostalAddress whereUpdatedByTeam($value)
 */
class PostalAddress extends Model implements AddressInterface, AccessControl, OwnedByContact
{
    use HasFactory;
    use Userstamps;

    use HasCountryCode, AddressingAttributeMappings, OrderableWithIndex, BelongsToContact, HasPermissionFlags;

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- MODEL CONFIGURATION -------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    protected $table = 'postal_addresses';

    protected $fillable = ['contact_id','label',
        'country_code','administrative_area','locality','dependent_locality','postal_code',
        'sorting_code','address_line_1','address_line_2','organisation','locale',
        'options','remarks'];

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- FORMATTING METHODS --------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    /**
     * @param array $options
     * @return string
     */
    public function format($options = []) {
        $formatter = resolve(FormatterInterface::class);
        return $formatter->format($this, $options);
    }

    /**
     * @param array $options
     * @return string
     */
    public function postalLabel($options = []) {
        $formatter = resolve(PostalLabelFormatterInterface::class);
        return $formatter->format($this, $options);
    }

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- FORMATTING RESOLVERS ------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    public function resolveFormatField($root, $args) {
        return $this->format($args);
    }

    public function resolvePostalLabelField($root, $args) {
        return $this->postalLabel($args);
    }

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- CUSTOM ACCESSORS ----------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    /**
     * The AddressFormat of this PersonAddress (based on the country_code.)
     *
     * @return AddressFormat
     */
    public function getAddressFormatAttribute() {
        $addressFormatRepository = resolve(AddressFormatRepositoryInterface::class);
        return $addressFormatRepository->get($this->country_code);
    }

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- BOOT AND STATIC ------------------------------------------------------------------------------------ //
    // ---------------------------------------------------------------------------------------------------------- //

    /**
     * The boot settings
     */
    public static function boot()
    {
        static::saving(function(PostalAddress $personAddress) {
            $addressFormat = $personAddress->addressFormat;
            $usedFields = $addressFormat->getUsedFields();
//            foreach (AddressField::getInstances() as $field) {
//                if(!in_array($field->value, $usedFields)) {
//                    $personAddress->{$field->getDatabaseField()} = null;
//                }
//            }
        });

        parent::boot();
    }
}
