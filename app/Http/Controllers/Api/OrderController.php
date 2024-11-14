<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request) {
        $order = Order::create(); // Crear una nueva orden
        $itemsData = [];
    
        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            $quantityToDeliver = min($product->quantity, $item['quantity_requested']);
    
            // Guardar item en la orden
            $orderItem = new OrderItem([
                'product_id' => $item['product_id'],
                'quantity_requested' => $item['quantity_requested'],
                'quantity_to_deliver' => $quantityToDeliver,
            ]);
            $order->items()->save($orderItem);
    
            // Actualizar inventario
            $product->quantity -= $quantityToDeliver;
            $product->save();
    
            $itemsData[] = [
                'product_id' => $item['product_id'],
                'requested' => $item['quantity_requested'],
                'delivered' => $quantityToDeliver,
            ];
        }
    
        return response()->json([
            'order_id' => $order->id,
            'items' => $itemsData,
        ]);
    }

    public function updateOrder(Request $request, $orderId) {
        $order = Order::with('items.product')->find($orderId);
    
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
    
        foreach ($request->items as $item) {
            $productId = $item['product_id'];
            $newQuantityRequested = $item['quantity_requested'];
    
            // Verificar si el producto ya está en la orden
            $orderItem = $order->items()->where('product_id', $productId)->first();
            $product = Product::find($productId);
    
            if (!$product) {
                continue; // Si el producto no existe, pasar al siguiente
            }
    
            if ($orderItem) {
                // Si el producto ya está en la orden, ajustar inventario según la nueva cantidad
                $previousQuantityRequested = $orderItem->quantity_requested;
                $previousQuantityDelivered = $orderItem->quantity_to_deliver;
    
                if ($newQuantityRequested > $previousQuantityRequested) {
                    // Caso 1: Se solicita más cantidad
                    $additionalQuantity = $newQuantityRequested - $previousQuantityRequested;
                    $additionalQuantityToDeliver = min($additionalQuantity, $product->quantity);
    
                    // Actualizar el inventario y la cantidad a entregar
                    $orderItem->quantity_to_deliver += $additionalQuantityToDeliver;
                    $product->quantity -= $additionalQuantityToDeliver;
                } elseif ($newQuantityRequested < $previousQuantityRequested) {
                    // Caso 2: Se solicita menos cantidad
                    $reducedQuantity = $previousQuantityRequested - $newQuantityRequested;
                    $product->quantity += $reducedQuantity;
    
                    // Ajustar la cantidad a entregar si es menor que la cantidad solicitada
                    $orderItem->quantity_to_deliver = min($newQuantityRequested, $orderItem->quantity_to_deliver);
                }
    
                // Guardar la nueva cantidad solicitada y entregada
                $orderItem->quantity_requested = $newQuantityRequested;
                $orderItem->save();
            } else {
                // Si el producto no está en la orden, agregarlo como nuevo
                $quantityToDeliver = min($newQuantityRequested, $product->quantity);
                
                $newOrderItem = new OrderItem([
                    'product_id' => $productId,
                    'quantity_requested' => $newQuantityRequested,
                    'quantity_to_deliver' => $quantityToDeliver,
                ]);
                $order->items()->save($newOrderItem);
    
                // Ajustar el inventario
                $product->quantity -= $quantityToDeliver;
            }
    
            // Guardar cambios en el producto
            $product->save();
        }
    
        return response()->json([
            'order_id' => $order->id,
            'items' => $order->items->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'requested' => $item->quantity_requested,
                    'delivered' => $item->quantity_to_deliver,
                ];
            }),
        ]);
    }
    
    public function cancelOrder($orderId) {
        $order = Order::with('items')->find($orderId);
    
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
    
        // Revertir la cantidad a entregar para cada producto en el inventario
        foreach ($order->items as $orderItem) {
            $product = Product::find($orderItem->product_id);
    
            if ($product) {
                // Sumar al inventario la cantidad que iba a ser entregada en la orden
                $product->quantity += $orderItem->quantity_to_deliver;
                $product->save();
            }
        }
    
        // Cambiar el estado de la orden a "cancelada" en lugar de eliminarla
        $order->status = 'cancelled';
        $order->save();
    
        return response()->json(['message' => 'Order cancelled successfully, inventory updated'], 200);
    }
}
