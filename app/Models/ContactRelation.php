<?php

namespace App\Models;

use App\Models\Traits\BelongsToContact;
use App\Models\Traits\OrderableWithIndex;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Wildside\Userstamps\Userstamps;

class ContactRelation extends Pivot
{
    use HasFactory;
    use Userstamps;

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
