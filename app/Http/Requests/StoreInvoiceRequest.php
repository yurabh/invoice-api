<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\MatchGrossAmount;
use App\Rules\ValidDueDate;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: "StoreInvoiceRequest",
    title: "Схема створення інвойсу",
    required: ["number", "supplier_name", "supplier_tax_id", "net_amount", "vat_amount", "gross_amount", "currency", "issue_date", "due_date"],
    properties: [
        new OAT\Property(property: "number", description: "Унікальний номер інвойсу", type: "string", example: "INV-2026-001"),
        new OAT\Property(property: "supplier_name", description: "Назва постачальника", type: "string", example: "ТОВ Рога і Копита", maxLength: 255),
        new OAT\Property(property: "supplier_tax_id", description: "Ідентифікаційний код / ЄДРПОУ", type: "string", example: "12345678", maxLength: 50),
        new OAT\Property(property: "net_amount", description: "Сума без ПДВ (> 0)", type: "number", format: "float", example: 100.00, minimum: 0.01),
        new OAT\Property(property: "vat_amount", description: "Сума ПДВ (>= 0)", type: "number", format: "float", example: 20.00, minimum: 0.00),
        new OAT\Property(property: "gross_amount", description: "Загальна сума (має дорівнювати net_amount + vat_amount)", type: "number", format: "float", example: 120.00),
        new OAT\Property(property: "currency", description: "Тризначний код валюти", type: "string", example: "UAH", maxLength: 3, minLength: 3),
        new OAT\Property(property: "issue_date", description: "Дата виписки інвойсу (YYYY-MM-DD)", type: "string", format: "date", example: "2026-08-30"),
        new OAT\Property(property: "due_date", description: "Гранична дата оплати (має бути >= issue_date)", type: "string", format: "date", example: "2026-09-15")
    ]
)]
class StoreInvoiceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'number' => ['required', 'string', 'unique:invoices,number'],
            'supplier_name' => ['required', 'string', 'max:255'],
            'supplier_tax_id' => ['required', 'string', 'max:50'],
            'net_amount' => ['required', 'numeric', 'gt:0'],
            'vat_amount' => ['required', 'numeric', 'gte:0'],
            'gross_amount' => ['required', 'numeric', new MatchGrossAmount()],
            'currency' => ['required', 'string', 'size:3'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', new ValidDueDate()],
        ];
    }
}
