<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

//Controlador de pedidos: compras del usuario y artículos vendidos.
class OrderController extends Controller
{
    // Lista las compras del usuario autenticado.
    public function index(Request $request)
    {
        $orders = $request->user()->orders()
            ->with('listing:id,title,price')
            ->latest()
            ->paginate(20);

        return view('orders.index', compact('orders'));
    }

    // Lista los articulos vendidos por el usuario autenticado.
    public function sold(Request $request)
    {
        $orders = $request->user()->soldOrders()
            ->with('listing:id,title,price')
            ->latest()
            ->paginate(20);

        return view('orders.sold', compact('orders'));
    }

    // Cambia el estado de un pedido segun el rol (vendedor o comprador).
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:no_enviado,en_envio,recibido'],
        ]);

        // El vendedor puede marcar el pedido en envio
        if ($order->seller_id === $request->user()->id && $validated['status'] === 'en_envio') {
            $order->update(['status' => 'en_envio']);
            return back()->with('status', 'Pedido marcado como enviado');
        }

        // El comprador puede marcar el pedido recibido
        if ($order->user_id === $request->user()->id && $validated['status'] === 'recibido') {
            $order->update(['status' => 'recibido']);
            return back()->with('status', 'Pedido marcado como recibido');
        }

        abort(403);
    }

    // Permite al comprador cancelar un pedido si no se ha recibido
    public function destroy(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        if (in_array($order->status, ['no_enviado', 'en_envio'], true)) {
            $order->listing()->update(['status' => 'available']);
            $order->delete();
            return back()->with('status', 'Pedido cancelado');
        }

        return back()->withErrors(['order' => 'No se puede cancelar un pedido recibido.']);
    }
}
