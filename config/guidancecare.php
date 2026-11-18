<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Allowed email domains
    |--------------------------------------------------------------------------
    | Only addresses on these domains can be used when creating or changing an
    | account (registration, profile, Admin > Users). Set GC_ALLOWED_EMAIL_DOMAINS
    | in .env as a comma-separated list. For the live deployment remove
    | "test.com" so only school addresses are accepted.
    */
    'allowed_email_domains' => array_values(array_filter(array_map(
        fn ($d) => strtolower(trim($d)),
        explode(',', env('GC_ALLOWED_EMAIL_DOMAINS', 'globalcity.sti.edu.ph'))
    ))),

];
