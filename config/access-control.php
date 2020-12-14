<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Abilities Settings
    |--------------------------------------------------------------------------
    |
    | This value determines the default abilities for Access Control Objects.
    | It should be an associative array with the following structure:
    |
    | [
    |   'name_of_ability_1' => [
    |       'level' =>  \App\Enums\AccessLevel::{default minimal access level for this ability},
    |       'scopes' => ['{id of oauth scope 1}', '{id of oauth scope 2}']
    |   ],
    |   'name_of_ability_2' => [
    |       'level' =>  \App\Enums\AccessLevel::{default minimal access level for this ability},
    |       'scopes' => [] // Leave empty or omit when all OAuth keys are allowed have this ability.
    |   ],
    | ]
    |
    */

    "default" => [
        "view" => [
            "level" => \App\Enums\AccessLevel::SUBJECT, // Allow users to see there own data.
        ],
        "update" => [
            "level" => \App\Enums\AccessLevel::CREATOR_TEAM, // Allow all team members of the creator to edit the data.
        ],
        "delete" => [
            "level" => \App\Enums\AccessLevel::CREATOR, // Allow the creator of some data to remove that data.
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Abilities Settings Per Model
    |--------------------------------------------------------------------------
    |
    | This value determines the abilities for echo AccessControl Object.
    | It should be an associative array with as keys the class names of the
    | objects and as values the same structure as the `default` option.
    |
    | The options that you set will be merged with the default options above.
    |
    */

    "models" => [
        \App\Models\Group:: class => [
            "view" => [
                "scopes" => ["view-contacts"],
            ],
        ],
        \App\Models\GroupCategory::class => [
            "view" => [
                "scopes" => ["view-contacts"],
            ],
        ],
        \App\Models\Contact::class => [
            "view" => [
                "scopes" => ["view-contacts"],
            ],
        ],
        \App\Models\ContactLanguage::class => [
            "view" => [
                "scopes" => ["view-contacts"],
            ],
        ],
        \App\Models\ContactRelation::class => [
            "view" => [
                "scopes" => ["view-contacts"],
            ],
        ],
        \App\Models\EmailAddress::class => [
            "view" => [
                "scope" => ["view-contacts", "view-contact-email-addresses"],
            ],
        ],
        \App\Models\PhoneNumber::class => [
            "view" => [
                "scope" => ["view-contacts", "view-contact-phone-numbers"],
            ],
        ],
        \App\Models\PostalAddress::class => [
            "view" => [
                "scope" => ["view-contacts", "view-contact-postal-addresses"],
            ],
        ],
    ],

];
