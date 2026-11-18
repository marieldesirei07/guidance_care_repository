<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Accepts an email only if its domain is listed in
 * config('guidancecare.allowed_email_domains').
 */
class AllowedEmailDomain implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $allowed = config('guidancecare.allowed_email_domains', []);

        // No list configured = no restriction (avoids locking everyone out).
        if (empty($allowed)) {
            return;
        }

        $domain = strtolower(substr(strrchr((string) $value, '@') ?: '', 1));

        if (!in_array($domain, $allowed, true)) {
            $fail('Please use an approved school email address (@' . implode(', @', $allowed) . ').');
        }
    }
}
