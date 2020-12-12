<?php

namespace App\Models;

use App\Models\Traits\Teamstamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class GroupCategory extends Model
{
    use HasFactory;
    use Userstamps;
    use Teamstamps;

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- RELATIONS ------------------------------------------------------------------------------------------ //
    // ---------------------------------------------------------------------------------------------------------- //

    public function groups()
    {
        return $this->hasMany(Group::class, 'category_id');
    }
}
