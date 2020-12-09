<?php


namespace App\Models;


use App\Models\Traits\AddressingAttributeMappings;
use App\Models\Traits\BelongsToContact;
use App\Models\Traits\HasCountryCode;
use App\Models\Traits\OrderableWithIndex;
use CommerceGuys\Addressing\AddressFormat\AddressFormat;
use CommerceGuys\Addressing\AddressFormat\AddressFormatRepositoryInterface;
use CommerceGuys\Addressing\AddressInterface;
use CommerceGuys\Addressing\Formatter\FormatterInterface;
use CommerceGuys\Addressing\Formatter\PostalLabelFormatterInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class PostalAddress extends Model implements AddressInterface
{
    use HasFactory;
    use Userstamps;

    use HasCountryCode, AddressingAttributeMappings, OrderableWithIndex, BelongsToContact;

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
            foreach (self::addressFieldAttributeNames() as $attributeName) {
                if($attributeName === 'organisation') {
                    $fieldName = 'organization';
                } else {
                    $fieldName = \Str::camel($attributeName);
                }

                if(!in_array($fieldName, $usedFields)) {
                    $personAddress->$attributeName = null;
                }
            }
        });

        parent::boot();
    }

    /**
     * Returns the names of the attributes that store the address values.
     *
     * @return array
     */
    public static function addressFieldAttributeNames() {
        return [
            'administrative_area','locality','dependent_locality','postal_code','sorting_code',
            'address_line_1','address_line_2','organisation'
        ];
    }
}
