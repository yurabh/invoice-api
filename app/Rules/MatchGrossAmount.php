<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class MatchGrossAmount implements ValidationRule, DataAwareRule
{
    protected array $data = [];

    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $net = (float)($this->data['net_amount'] ?? 0);
        $vat = (float)($this->data['vat_amount'] ?? 0);
        $gross = (float)$value;

        if (abs(($net + $vat) - $gross) > 0.01) {
            $fail('The gross amount must be exactly equal to net_amount + vat_amount.');
        }
    }
}
