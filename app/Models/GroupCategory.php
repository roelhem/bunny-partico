<?php

namespace App\Models;

use App\Contracts\AccessControl;
use App\Contracts\OwnedByContact;
use App\Enums\Color;
use App\Models\Traits\HasPermissionFlags;
use App\Models\Traits\Teamstamps;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class GroupCategory extends Model implements AccessControl
{
    use HasFactory;
    use Userstamps;
    use Teamstamps;

    use HasPermissionFlags;
    use CastsEnums;

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- SETUP ---------------------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    protected $casts = [
        'color' => Color::class,
    ];

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- RELATIONS ------------------------------------------------------------------------------------------ //
    // ---------------------------------------------------------------------------------------------------------- //

    public function groups()
    {
        return $this->hasMany(Group::class, 'category_id');
    }

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- IMPLEMENT: AccessControl --------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    /**
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query
     * @param OwnedByContact|int|null $contactRef
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    public function scopeSubject($query, $contactRef)
    {
        return $query->whereHas('groups', function ($query) use ($contactRef) {
            /** @var \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query */
            return $query->subject($contactRef);
        });
    }

    public function isSubject($contactRef)
    {
        if($contactRef instanceof OwnedByContact) {
            $contactRef = $contactRef->getOwnerId();
        }
        if($contactRef === null) {
            return false;
        }

        return $this->groups()->whereHas('contacts', function($query) use ($contactRef) {
            /** @var \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query */
            return $query->where('id', $contactRef);
        })->exists();
    }
}
