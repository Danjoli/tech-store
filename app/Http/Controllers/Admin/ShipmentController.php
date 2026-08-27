<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ShipmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateShipmentRequest;
use App\Models\Shipment;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ShipmentController extends Controller
{
    public function index(): Response
    {
        $shipments = Shipment::query()->with('order.user:id,name,email')->latest()->paginate(20)->through(fn (Shipment $shipment): array => $this->present($shipment));

        return Inertia::render('Admin/Shipments/Index', ['shipments' => $shipments]);
    }

    public function edit(Shipment $shipment): Response
    {
        $shipment->load('order.user:id,name,email');

        return Inertia::render('Admin/Shipments/Edit', ['shipment' => $this->present($shipment), 'statuses' => array_map(fn (ShipmentStatus $status): array => ['value' => $status->value, 'label' => $status->label()], ShipmentStatus::cases())]);
    }

    public function update(UpdateShipmentRequest $request, Shipment $shipment): RedirectResponse
    {
        $data = $request->validated();
        $status = ShipmentStatus::from($data['status']);
        $shipment->update([...$data, 'shipped_at' => $status === ShipmentStatus::SHIPPED ? ($shipment->shipped_at ?? now()) : $shipment->shipped_at, 'delivered_at' => $status === ShipmentStatus::DELIVERED ? ($shipment->delivered_at ?? now()) : null]);

        return to_route('admin.shipments.index')->with('success', 'Envio atualizado com sucesso.');
    }

    /** @return array<string, mixed> */
    private function present(Shipment $shipment): array
    {
        return ['id' => $shipment->id, 'status' => $shipment->status->value, 'status_label' => $shipment->status->label(), 'carrier' => $shipment->carrier, 'tracking_code' => $shipment->tracking_code, 'shipped_at' => $shipment->shipped_at, 'delivered_at' => $shipment->delivered_at, 'order' => $shipment->order ? ['id' => $shipment->order->id, 'number' => $shipment->order->number, 'user' => $shipment->order->user] : null];
    }
}
