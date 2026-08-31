<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: "InvoiceResource",
    description: "Ресурс представлення даних інвойсу у форматі JSON",
    properties: [
        new OAT\Property(property: "id", description: "Унікальний ідентифікатор інвойсу", type: "string", format: "uuid", example: "9df13a22-381a-4b9d-b4b3-57f920258144"),
        new OAT\Property(property: "number", description: "Унікальний номер документу", type: "string", example: "INV-2026-001"),
        new OAT\Property(property: "supplier_name", description: "Назва постачальника", type: "string", example: "ТОВ Рога і Копита"),
        new OAT\Property(property: "supplier_tax_id", description: "ЄДРПОУ / ІПН постачальника", type: "string", example: "12345678"),
        new OAT\Property(property: "net_amount", description: "Сума без ПДВ", type: "number", format: "float", example: 100.00),
        new OAT\Property(property: "vat_amount", description: "Сума ПДВ", type: "number", format: "float", example: 20.00),
        new OAT\Property(property: "gross_amount", description: "Загальна сума з ПДВ", type: "number", format: "float", example: 120.00),
        new OAT\Property(property: "currency", description: "Валюта інвойсу", type: "string", example: "UAH"),
        new OAT\Property(property: "status", description: "Поточний статус", type: "string", example: "pending", enum: ["pending", "approved", "rejected"]),
        new OAT\Property(property: "issue_date", description: "Дата виписки (ISO 8601)", type: "string", format: "date-time", example: "2026-08-30T00:00:00+03:00"),
        new OAT\Property(property: "due_date", description: "Гранична дата оплати (ISO 8601)", type: "string", format: "date-time", example: "2026-09-15T00:00:00+03:00"),
        new OAT\Property(property: "created_at", description: "Дата створення запису", type: "string", format: "date-time", example: "2026-08-31T12:00:00+03:00", nullable: true),
        new OAT\Property(property: "updated_at", description: "Дата останнього оновлення", type: "string", format: "date-time", example: "2026-08-31T12:45:00+03:00", nullable: true)
    ]
)]
class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'number' => $this->resource->number,
            'supplier_name' => $this->resource->supplier_name,
            'supplier_tax_id' => $this->resource->supplier_tax_id,
            'net_amount' => $this->resource->net_amount,
            'vat_amount' => $this->resource->vat_amount,
            'gross_amount' => $this->resource->gross_amount,
            'currency' => $this->resource->currency,
            'status' => $this->resource->status,
            'issue_date' => $this->resource->issue_date->toIso8601String(),
            'due_date' => $this->resource->due_date->toIso8601String(),
            'created_at' => $this->resource->created_at?->toIso8601String(),
            'updated_at' => $this->resource->updated_at?->toIso8601String(),
        ];
    }
}
