<?php

return [
    \App\Enums\AccessLevel::class => [
        \App\Enums\AccessLevel::PUBLIC         => "Access for everyone, even clients that haven't been logged in.",
        \App\Enums\AccessLevel::MACHINE        => "Access for OAuth-clients that use a Machine to Machine token.",
        \App\Enums\AccessLevel::USER          => "Access to all logged in users.",
        \App\Enums\AccessLevel::SUBJECT_TEAM   => "Access for all the team members of the user about whom this data is about.",
        \App\Enums\AccessLevel::SUBJECT        => "Access for the user about whom this data is about.",
        \App\Enums\AccessLevel::CREATOR_TEAM   => "Access for all team members of the team that created the entry.",
        \App\Enums\AccessLevel::CREATOR        => "Access to the user that created the entry.",
        \App\Enums\AccessLevel::ADMIN_TEAM     => "Access to users that are in a team of the admin user.",
        \App\Enums\AccessLevel::ADMIN          => "Access only to an admin user.",
    ]
];
