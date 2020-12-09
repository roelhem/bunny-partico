<?php

namespace App\Models;

use App\Models\Traits\BelongsToContact;
use App\Models\Traits\OrderableWithIndex;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class ContactLanguage extends Model
{
    use HasFactory;
    use Userstamps;

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
