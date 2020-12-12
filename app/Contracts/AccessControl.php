<?php


namespace App\Contracts;


use App\Enums\AccessLevel;

interface AccessControl
{
    public function getAccessLevel(string $ability): AccessLevel;

    /**
     * @return string|int|null
     */
    public function getSubjectId();

    /**
     * @return string|int|null
     */
    public function getCreatorId();

    /**
     * @return string|int|null
     */
    public function getCreatorTeamId();
}
