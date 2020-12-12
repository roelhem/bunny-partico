<?php


namespace App\Models;


use App\Contracts\OwnedByContact;
use App\Models\Traits\BelongsToContact;
use App\Models\Traits\Teamstamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Wildside\Userstamps\Userstamps;

class GroupContact extends Pivot implements OwnedByContact
{
    use HasFactory;
    use Userstamps;
    use Teamstamps;

    use BelongsToContact;

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- RELATIONS ------------------------------------------------------------------------------------------ //
    // ---------------------------------------------------------------------------------------------------------- //

    public function group()
    {
        $this->belongsTo(Group::class, 'group_id');
    }
}
