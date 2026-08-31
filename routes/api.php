<?php

declare(strict_types=1);

use App\Http\Controllers\Invoice\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::apiResource('invoices', InvoiceController::class)
    ->only(['index', 'show', 'store', 'update']);
