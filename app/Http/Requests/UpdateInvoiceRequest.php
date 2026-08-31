<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\MatchGrossAmount;
use App\Rules\ValidDueDate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('invoice')?->status === 'pending';
    }

    public function rules(): array
    {
        $invoice = $this->route('invoice');

        return [
            'net_amount' => ['required', 'numeric', 'gt:0'],
            'vat_amount' => ['required', 'numeric', 'gte:0'],
            'gross_amount' => ['required', 'numeric', new MatchGrossAmount()],
            'due_date' => ['required', 'date', new ValidDueDate($invoice?->issue_date)],
        ];
    }
}
