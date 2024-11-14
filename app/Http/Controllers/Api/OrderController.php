<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

class OrderController extends Controller
{      
    public function index(){
        // Obtener todas las órdenes junto con los productos relacionados
        $orders = Order::with('items.product')->get();
        
        foreach($orders as $order){
            $total = 0;
            foreach($order->items as $item){
                $total += $item->subtotal;
                if($item->quantity_to_deliver != 0){
                    $item->unitPrice = $item->subtotal / $item->quantity_to_deliver;
                }
                
            }
            $order->total = $total;
        }
        return response()->json($orders, 200);
    }

    public function show($id){
        // Buscar la orden por ID junto con los productos relacionados
        $order = Order::with('items.product')->find($id);

        if (!$order) {
            return response()->json(['message' => 'Orden no encontrada'], 404);
        }
        
        $total = 0;
        foreach($order->items as $item){
            $total += $item->subtotal;
            if ($item->subtotal != 0){
                $item->unitPrice = $item->subtotal / $item->quantity_to_deliver  ;
            } 
        }
        $order->total = $total;
        

        return response()->json($order, 200);
    }

    public function store(Request $request) {

        if (count($request->items) === 0) {
            return response()->json([
                'message' => 'No se recibieron items para procesar la orden.',
            ], 400);
        }

        $order = Order::create(); // Crear una nueva orden
        $itemsData = [];
        $total = 0;
    
        foreach ($request->items as $item) {
            if (!isset($item['product_id']) || empty($item['product_id'])) {
                throw new \Exception('product_id es requerido para cada item');
            }
            $product = Product::find($item['product_id']);
            if(!$product || $item['quantity'] < 1 ){
                continue; 
            }
            $quantityToDeliver = min($product->quantity, $item['quantity']);
            $subtotal = $quantityToDeliver * $product->price;
            $total += $subtotal;
            // Guardar item en la orden
            $orderItem = new OrderItem();
            $orderItem->product_id = $item['product_id'];
            $orderItem->quantity_requested = $item['quantity'];
            $orderItem->quantity_to_deliver = $quantityToDeliver;
            $orderItem->subtotal = $subtotal;
            $orderItem->order_id = $order->id;
            $orderItem->save();
    
            // Actualizar inventario
            $product->quantity -= $quantityToDeliver;
            $product->save();
    
            $itemsData[] = [
                'product_id' => $item['product_id'],
                'requested' => $item['quantity'],
                'delivered' => $quantityToDeliver,
                'subtotal' => $subtotal,
            ];
        }

        
        
    
        return response()->json([
            'order_id' => $order->id,
            'items' => $itemsData,
            'total' => $total,
        ]);
    }

    public function updateOrder(Request $request, $orderId) {

        if (count($request->items) === 0) {
            return response()->json([
                'message' => 'No se recibieron items para actualizar la orden.',
            ], 400);
        }
        
        $order = Order::with('items.product')->find($orderId);
    
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // valida que en los product_id no hayan repetidos 
        $listValidation = collect();
        foreach ($request->items as $item ){
            if (!$listValidation->contains($item['product_id'])) {
                $listValidation->push($item['product_id']);
            }else{
                return response()->json(['message'=>'product_id repeated'],422);
            }
        }

        // coloca cantidad en cero la copia de productos que no llegaron
        // y si se encontraban antes en la orden
        // para posteriormente hacer los respectivos cambios
        $items = collect($request->items);
        foreach($order->items as $item){
            if(! $listValidation->contains($item->product_id)){
                $itemX =  clone $item;
                $itemX["quantity"] = 0;
                $items->push($itemX);
                
            }
        }

        $total = 0;
    
        foreach ($items as $item) {
            
            $productId = $item['product_id'];
            $newQuantityRequested = $item['quantity'];
    
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
                $orderItem->subtotal = $orderItem->quantity_to_deliver * $product->price;
                $orderItem->save();
                $total += $orderItem->subtotal; 
            } else {
                // Si el producto no está en la orden, agregarlo como nuevo
                $quantityToDeliver = min($newQuantityRequested, $product->quantity);
                
                $newOrderItem = new OrderItem([
                    'product_id' => $productId,
                    'quantity_requested' => $newQuantityRequested,
                    'quantity_to_deliver' => $quantityToDeliver,
                    'subtotal'=>$quantityToDeliver*$product->price,
                ]);
                $total += $newOrderItem->subtotal;
                $order->items()->save($newOrderItem);
    
                // Ajustar el inventario
                $product->quantity -= $quantityToDeliver;
            }
    
            // Guardar cambios en el producto
            $product->save();
        }
        $orderUpdated = Order::with('items.product')->find($orderId);
        return response()->json([
            'order_id' => $orderUpdated->id,
            'items' => $orderUpdated->items,
            'total' => $total,
        ]);
    }

    public function cancelOrder($orderId) {
        $order = Order::with('items')->find($orderId);
    
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        if ($order->status == 'cancelled'){
            return response()->json(['message' => 'The order had previously been cancelled'], 200);
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
