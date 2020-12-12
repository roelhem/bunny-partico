<?php


namespace App\Models;


use App\Models\Traits\Teamstamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Wildside\Userstamps\Userstamps;

class GroupContact extends Pivot
{
    use HasFactory;
    use Userstamps;
    use Teamstamps;

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- RELATIONS ------------------------------------------------------------------------------------------ //
    // ---------------------------------------------------------------------------------------------------------- //

    public function group()
    {
        $this->belongsTo(Group::class, 'group_id');
    }

    public function contact()
    {
        $this->belongsTo(Contact::class, 'contact_id');
    }
}
