<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidDueDate implements ValidationRule, DataAwareRule
{
    protected array $data = [];

    public function __construct(
        protected ?Carbon $fallbackIssueDate = null
    )
    {
    }

    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $issueDateStr = $this->data['issue_date'] ?? null;
        $issueDate = $issueDateStr ? Carbon::parse($issueDateStr) : $this->fallbackIssueDate;

        if (!$issueDate) {
            return;
        }

        if (Carbon::parse($value)->lt($issueDate)) {
            $fail('The due date cannot be earlier than the issue date.');
        }
    }
}
