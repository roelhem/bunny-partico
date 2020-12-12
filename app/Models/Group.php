<?php

namespace App\Models;

use App\Contracts\AccessControl;
use App\Contracts\OwnedByContact;
use App\Models\Traits\HasPermissionFlags;
use App\Models\Traits\Teamstamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class Group extends Model implements AccessControl
{
    use HasFactory;
    use Userstamps;
    use Teamstamps;

    use HasPermissionFlags;

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
        if($contactRef instanceof OwnedByContact) {
            $contactRef = $contactRef->getOwnerId();
        }

        return $query->whereHas('contacts', function ($query) use ($contactRef) {
            /** @var \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query */
            return $query->where('id', $contactRef);
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

        return $this->contacts()->where('id', $contactRef)->exists();
    }
}
