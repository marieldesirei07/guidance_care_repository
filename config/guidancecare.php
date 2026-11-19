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

    /*
    | Pause (milliseconds) between emails sent in the same request.
    | Mailtrap's free sandbox drops emails sent within the same second; set to 0 for a real mail server.
    */
    'mail_gap_ms' => (int) env('GC_MAIL_GAP_MS', 1100),

    /*
    | Also email the STUDENT when a teacher refers them (the counselor is always emailed).
    | Off by default: Mailtrap's free test inbox rejects a second email within 10 seconds.
    | Turn on with GC_REFERRAL_EMAIL_STUDENT=true in .env, then run: php artisan config:cache
    */
    'referral_email_student' => (bool) env('GC_REFERRAL_EMAIL_STUDENT', false),

];
