<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

class OrderController extends Controller
{   /**
    * @OA\Get(
    *     path="/api/orders",
    *     summary="Obtener todas las órdenes",
    *     tags={"Orders"},
    *     @OA\Response(
    *         response=200,
    *         description="Listado de órdenes exitosamente",
    *         @OA\JsonContent(
    *             type="array",
    *             @OA\Items(ref="#/components/schemas/Order")
    *         )
    *     )
    * )
    */
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

    /**
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     summary="Obtener una orden por ID",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la orden",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles de la orden",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Orden no encontrada"
     *     )
     * )
     */
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

    /**
    * @OA\Post(
    *     path="/api/orders",
    *     summary="Crear una nueva orden",
    *     tags={"Orders"},
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(property="items", type="array", @OA\Items(
    *                 @OA\Property(property="product_id", type="integer"),
    *                 @OA\Property(property="quantity", type="integer")
    *             ))
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Order created successfully",
    *         @OA\JsonContent(
    *             @OA\Property(property="order_id", type="integer"),
    *             @OA\Property(property="items", type="array", @OA\Items(
    *                 @OA\Property(property="product_id", type="integer"),
    *                 @OA\Property(property="quantity_requested", type="integer"),
    *                 @OA\Property(property="quantity_to_deliver", type="integer")
    *             ))
    *         )
    *     )
    * )
    */
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
            $product = Product::findOrFail($item['product_id']);
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

    /**
    * @OA\Put(
    *     path="/api/orders/{id}",
    *     summary="Actualizar completamente una orden",
    *     tags={"Orders"},
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         required=true,
    *         description="ID de la orden a actualizar",
    *         @OA\Schema(type="integer")
    *     ),
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(
    *                 property="items",
    *                 type="array",
    *                 description="Lista de productos a actualizar",
    *                 @OA\Items(
    *                     type="object",
    *                     @OA\Property(property="product_id", type="integer", description="ID del producto"),
    *                     @OA\Property(property="quantity", type="integer", description="Nueva cantidad solicitada")
    *                 )
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Orden actualizada correctamente",
    *         @OA\JsonContent(ref="#/components/schemas/Order")
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Orden o producto no encontrado"
    *     )
    * )
    */
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
        $order->total = 0;
    
        foreach ($request->items as $item) {
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
                $order->total += $orderItem->subtotal; 
            } else {
                // Si el producto no está en la orden, agregarlo como nuevo
                $quantityToDeliver = min($newQuantityRequested, $product->quantity);
                
                $newOrderItem = new OrderItem([
                    'product_id' => $productId,
                    'quantity_requested' => $newQuantityRequested,
                    'quantity_to_deliver' => $quantityToDeliver,
                    'subtotal'=>$quantityToDeliver*$product->price,
                ]);
                $order->total += $newOrderItem->subtotal;
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
                    'subtotal' => $item->subtotal,
                ];
            }),
            'total' => $order->total,
        ]);
    }

    /**
    * @OA\Delete(
    *     path="/api/orders/{id}",
    *     summary="Cancelar una orden",
    *     tags={"Orders"},
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         required=true,
    *         description="ID de la orden a cancelar",
    *         @OA\Schema(type="integer")
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Orden cancelada y productos devueltos",
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(property="message", type="string", example="Orden cancelada y productos devueltos al inventario")
    *         )
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Orden no encontrada"
    *     )
    * )
    */
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
