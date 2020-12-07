<?php


namespace App\Models\Traits;


trait AddressingAttributeMappings
{
    // ---------------------------------------------------------------------------------------------------------- //
    // ----- INTERFACE CONFORMATION: AddressInterface ----------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    /**
     * @inheritdoc
     */
    public function getCountryCode()
    {
        return $this->country_code;
    }

    /**
     * @inheritdoc
     */
    public function getAdministrativeArea()
    {
        return $this->administrative_area;
    }

    /**
     * @inheritdoc
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * @inheritdoc
     */
    public function getDependentLocality()
    {
        return $this->dependent_locality;
    }

    /**
     * @inheritdoc
     */
    public function getPostalCode()
    {
        return $this->postal_code;
    }

    /**
     * @inheritdoc
     */
    public function getSortingCode()
    {
        return $this->sorting_code;
    }

    /**
     * @inheritdoc
     */
    public function getAddressLine1()
    {
        return $this->address_line_1;
    }

    /**
     * @inheritdoc
     */
    public function getAddressLine2()
    {
        return $this->address_line_2;
    }

    /**
     * @inheritdoc
     */
    public function getGivenName()
    {
        if($this->individual === null) {
            return null;
        }
        return $this->individual->name_first;
    }

    /**
     * @inheritdoc
     */
    public function getAdditionalName()
    {
        if($this->individual === null) {
            return null;
        }
        return $this->individual->name_prefix;
    }

    /**
     * @inheritdoc
     */
    public function getFamilyName()
    {
        if($this->individual === null) {
            return null;
        }
        return $this->individual->name_last;
    }

    /**
     * @inheritdoc
     */
    public function getOrganization()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getLocale()
    {
        return $this->locale;
    }
}
