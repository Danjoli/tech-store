<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(Request $request): Response
    {
        $orders = $request->user()->orders()->with(['payment', 'shipment'])->latest()->get()->map(fn (Order $order): array => $this->summary($order));

        return Inertia::render('Store/Orders/Index', ['orders' => $orders]);
    }

    public function show(Request $request, Order $order): Response
    {
        abort_unless($order->user_id === $request->user()->id, 404);
        $order->load(['items', 'payment', 'shipment']);

        return Inertia::render('Store/Orders/Show', ['order' => [...$this->summary($order), 'items' => $order->items, 'shipping_address' => $order->shipping_address]]);
    }

    /** @return array<string, mixed> */
    private function summary(Order $order): array
    {
        return [
            'id' => $order->id, 'number' => $order->number,
            'status' => $order->status->value, 'status_label' => $order->status->label(),
            'total_amount' => $order->total_amount, 'created_at' => $order->created_at,
            'payment' => $order->payment ? ['method' => $order->payment->method->value, 'method_label' => $order->payment->method->label(), 'status' => $order->payment->status->value, 'status_label' => $order->payment->status->label(), 'metadata' => $order->payment->metadata] : null,
            'shipment' => $order->shipment ? ['status' => $order->shipment->status->value, 'status_label' => $order->shipment->status->label(), 'carrier' => $order->shipment->carrier, 'tracking_code' => $order->shipment->tracking_code] : null,
        ];
    }
}
