<?php

declare(strict_types=1);

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OAT;

#[OAT\Info(
    version: "1.0.0",
    description: "API для автоматизації роботи бухгалтерів з інвойсами",
    title: "Invoice Management API"
)]
#[OAT\Server(url: "http://localhost:8000")]
#[OAT\Schema(
    schema: "InvoiceResource",
    properties: [
        new OAT\Property(property: "id", type: "string", format: "uuid", example: "9df13a22-381a-4b9d-b4b3-57f920258144"),
        new OAT\Property(property: "number", type: "string", example: "INV-2026-001"),
        new OAT\Property(property: "supplier_name", type: "string", example: "ТОВ Рога і Копита"),
        new OAT\Property(property: "supplier_tax_id", type: "string", example: "12345678"),
        new OAT\Property(property: "net_amount", type: "number", format: "float", example: 100.00),
        new OAT\Property(property: "vat_amount", type: "number", format: "float", example: 20.00),
        new OAT\Property(property: "gross_amount", type: "number", format: "float", example: 120.00),
        new OAT\Property(property: "currency", type: "string", example: "UAH"),
        new OAT\Property(property: "status", type: "string", example: "pending", enum: ["pending", "approved", "rejected"]),
        new OAT\Property(property: "issue_date", type: "string", format: "date", example: "2026-08-30"),
        new OAT\Property(property: "due_date", type: "string", format: "date", example: "2026-09-15")
    ]
)]
class InvoiceController extends Controller
{
    public function __construct(
        protected InvoiceService $invoiceService
    )
    {
    }

    #[OAT\Get(
        path: "/api/invoices",
        summary: "Отримати список інвойсів",
        tags: ["Invoices"],
        responses: [
            new OAT\Response(
                response: 200,
                description: "Успішне отримання списку інвойсів",
                content: new OAT\JsonContent(
                    type: "array",
                    items: new OAT\Items(ref: "#/components/schemas/InvoiceResource")
                )
            )
        ]
    )]
    public function index(): JsonResource
    {
        $invoices = $this->invoiceService->getAllPaginatedInvoices();
        return InvoiceResource::collection($invoices);
    }

    #[OAT\Post(
        path: "/api/invoices",
        summary: "Створити новий інвойс",
        requestBody: new OAT\RequestBody(
            required: true,
            content: new OAT\JsonContent(
                required: ["number", "supplier_name", "supplier_tax_id", "net_amount", "vat_amount", "gross_amount", "currency", "issue_date", "due_date"],
                properties: [
                    new OAT\Property(property: "number", type: "string", example: "INV-2026-001"),
                    new OAT\Property(property: "supplier_name", type: "string", example: "ТОВ Рога і Копита"),
                    new OAT\Property(property: "supplier_tax_id", type: "string", example: "12345678"),
                    new OAT\Property(property: "net_amount", type: "number", format: "float", example: 100.00),
                    new OAT\Property(property: "vat_amount", type: "number", format: "float", example: 20.00),
                    new OAT\Property(property: "gross_amount", type: "number", format: "float", example: 120.00),
                    new OAT\Property(property: "currency", type: "string", example: "UAH"),
                    new OAT\Property(property: "issue_date", type: "string", format: "date", example: "2026-08-30"),
                    new OAT\Property(property: "due_date", type: "string", format: "date", example: "2026-09-15")
                ]
            )
        ),
        tags: ["Invoices"],
        responses: [
            new OAT\Response(
                response: 201,
                description: "Інвойс успішно створено",
                content: new OAT\JsonContent(ref: "#/components/schemas/InvoiceResource")
            ),
            new OAT\Response(response: 422, description: "Помилка валідації (суми або дати)")
        ]
    )]
    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        $invoice = $this->invoiceService->createInvoice($request->validated());
        return (new InvoiceResource($invoice))
            ->toResponse($request)
            ->setStatusCode(Response::HTTP_CREATED);
    }

    #[OAT\Get(
        path: "/api/invoices/{id}",
        summary: "Отримати один інвойс за ID",
        tags: ["Invoices"],
        parameters: [
            new OAT\Parameter(name: "id", in: "path", required: true, schema: new OAT\Schema(type: "string", format: "uuid"))
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: "Інвойс знайдено",
                content: new OAT\JsonContent(ref: "#/components/schemas/InvoiceResource")
            ),
            new OAT\Response(response: 404, description: "Інвойс не знайдено")
        ]
    )]
    public function show(Invoice $invoice): InvoiceResource
    {
        return new InvoiceResource($invoice);
    }

    #[OAT\Put(
        path: "/api/invoices/{id}",
        description: "Оновлення дозволене тільки для інвойсів у статусі 'pending'",
        summary: "Оновити інвойс",
        requestBody: new OAT\RequestBody(
            required: true,
            content: new OAT\JsonContent(
                required: ["net_amount", "vat_amount", "gross_amount", "due_date"],
                properties: [
                    new OAT\Property(property: "net_amount", type: "number", format: "float", example: 150.00),
                    new OAT\Property(property: "vat_amount", type: "number", format: "float", example: 30.00),
                    new OAT\Property(property: "gross_amount", type: "number", format: "float", example: 180.00),
                    new OAT\Property(property: "due_date", type: "string", format: "date", example: "2026-09-20")
                ]
            )
        ),
        tags: ["Invoices"],
        parameters: [
            new OAT\Parameter(name: "id", in: "path", required: true, schema: new OAT\Schema(type: "string", format: "uuid"))
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: "Інвойс успішно оновлено",
                content: new OAT\JsonContent(ref: "#/components/schemas/InvoiceResource")
            ),
            new OAT\Response(response: 403, description: "Редагування заборонено (статус не pending)"),
            new OAT\Response(response: 422, description: "Помилка валідації")
        ]
    )]
    public function update(UpdateInvoiceRequest $request, Invoice $invoice): JsonResponse
    {
        $updatedInvoice = $this->invoiceService->updateInvoice($invoice, $request->validated());
        return (new InvoiceResource($updatedInvoice))
            ->toResponse($request)
            ->setStatusCode(Response::HTTP_OK);
    }
}
