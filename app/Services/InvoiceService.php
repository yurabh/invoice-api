<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class InvoiceService
{
    public function getAllPaginatedInvoices(int $perPage = 15): LengthAwarePaginator
    {
        return Invoice::query()
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function createInvoice(array $data): Invoice
    {
        return Invoice::create($data);
    }

    public function updateInvoice(Invoice $invoice, array $data): Invoice
    {
        $invoice->update($data);
        return $invoice;
    }
}
