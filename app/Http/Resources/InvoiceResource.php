<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
