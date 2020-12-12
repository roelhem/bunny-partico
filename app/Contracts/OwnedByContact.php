<?php


namespace App\Contracts;


use App\Models\Contact;

interface OwnedByContact
{
    /**
     * Returns the contact that owns this instance.
     *
     * @return Contact|null
     */
    public function getOwner(): ?Contact;

    /**
     * Returns the id of the contact that owns this instance.
     *
     * @return int|null
     */
    public function getOwnerId(): ?int;
}
