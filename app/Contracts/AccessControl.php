<?php


namespace App\Contracts;


use App\Enums\AccessLevel;
use App\Models\Contact;
use App\Models\User;

interface AccessControl
{
    public function getAccessLevel(string $ability): AccessLevel;

    public function hasAccess(string $ability, ?User $user = null);

    public function getAccess(?User $user = null);

    /**
     * Check if the provided contact is a subject of this instance.
     *
     * @param Contact|OwnedByContact|int|null $contactReference
     * @return boolean
     */
    public function isSubject($contactReference);

    /**
     * @return string|int|null
     */
    public function getCreatorId();

    /**
     * @return string|int|null
     */
    public function getCreatorTeamId();
}
