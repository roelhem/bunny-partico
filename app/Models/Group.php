<?php

namespace App\Models;

use App\Models\Traits\Teamstamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class Group extends Model
{
    use HasFactory;
    use Userstamps;
    use Teamstamps;

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- MODEL CONFIGURATION -------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- RELATIONS ------------------------------------------------------------------------------------------ //
    // ---------------------------------------------------------------------------------------------------------- //

    public function category()
    {
        return $this->belongsTo(GroupCategory::class, 'category_id');
    }

    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'group_contact')->withPivot(
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            'created_by_team',
            'updated_by_team',
        )->using(GroupContact::class);
    }
}
