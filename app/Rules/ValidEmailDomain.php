<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidEmailDomain implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value) || !str_contains($value, "@")) {
            return;
        }
        
        $parts = explode("@", $value);
        if (count($parts) !== 2) return;
        
        $domain = strtolower($parts[1]);
        $blockedDomains = ["gmial.com", "gmal.com", "gamil.com", "gmail.con", "yaho.com", "yahoo.con", "hotmal.com", "hotmail.con"];
        
        if (in_array($domain, $blockedDomains)) {
            $fail("Tên miền email dường như bị gõ nhầm. Vui lòng kiểm tra lại (ví dụ: gmail.com).");
        }
    }
}

