<?php


namespace App\Services\OpenID;


class ClaimType
{
    const DEFAULT_CLAIM = "DEFAULT";
    const CUSTOM_CLAIM = "CUSTOM";

    /**
     * The default claims of the openid connect protocol.
     *
     * @see http://openid.net/specs/openid-connect-core-1_0.html#ScopeClaims
     * @var array
     */
    public static $defaultClaims = [
        'profile' => [
            'name',
            'family_name',
            'given_name',
            'middle_name',
            'nickname',
            'preferred_username',
            'profile',
            'picture',
            'website',
            'gender',
            'birthdate',
            'zoneinfo',
            'locale',
            'updated_at'
        ],
        'email' => [
            'email',
            'email_verified'
        ],
        'address' => [
            'address'
        ],
        'phone' => [
            'phone_number',
            'phone_number_verified'
        ]
    ];

    public static $claimMethodCache = [];
}
