<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\MatchGrossAmount;
use App\Rules\ValidDueDate;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: "UpdateInvoiceRequest",
    title: "Схема оновлення інвойсу",
    required: ["net_amount", "vat_amount", "gross_amount", "due_date"],
    properties: [
        new OAT\Property(property: "net_amount", description: "Нова сума без ПДВ (> 0)", type: "number", format: "float", example: 150.00, minimum: 0.01),
        new OAT\Property(property: "vat_amount", description: "Нова сума ПДВ (>= 0)", type: "number", format: "float", example: 30.00, minimum: 0.00),
        new OAT\Property(property: "gross_amount", description: "Нова загальна сума (net_amount + vat_amount)", type: "number", format: "float", example: 180.00),
        new OAT\Property(property: "due_date", description: "Нова гранична дата оплати (має бути >= оригінальної issue_date)", type: "string", format: "date", example: "2026-09-20")
    ]
)]
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
