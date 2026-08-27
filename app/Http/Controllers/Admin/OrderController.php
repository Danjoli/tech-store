<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(): Response
    {
        $orders = Order::query()->with(['user:id,name,email', 'payment', 'shipment'])->latest()->paginate(20)->through(fn (Order $order): array => $this->present($order));

        return Inertia::render('Admin/Orders/Index', ['orders' => $orders]);
    }

    public function show(Order $order): Response
    {
        $order->load(['user:id,name,email', 'items', 'payment', 'shipment']);

        return Inertia::render('Admin/Orders/Show', ['order' => [...$this->present($order), 'items' => $order->items, 'shipping_address' => $order->shipping_address]]);
    }

    /** @return array<string, mixed> */
    private function present(Order $order): array
    {
        return ['id' => $order->id, 'number' => $order->number, 'status' => $order->status->value, 'status_label' => $order->status->label(), 'total_amount' => $order->total_amount, 'created_at' => $order->created_at, 'user' => $order->user, 'payment' => $order->payment ? ['method_label' => $order->payment->method->label(), 'status_label' => $order->payment->status->label()] : null, 'shipment' => $order->shipment ? ['id' => $order->shipment->id, 'status' => $order->shipment->status->value, 'status_label' => $order->shipment->status->label(), 'carrier' => $order->shipment->carrier, 'tracking_code' => $order->shipment->tracking_code] : null];
    }
}
