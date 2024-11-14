<?php

namespace App\Http\Controllers\Api;
use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\facades\Validator;

class productController extends Controller
{
    public function index(){
        $products = Product::all();

        // if($products->isEmpty()){
        //     $data = [
        //         'message'=>'There are no products',
        //         'status'=>200
        //     ];
        //     return response()->json($data, 200);
        // }
        $data = ['products'=> $products];
        return response()->json($data, 200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        if($validator->fails()){
            $data = [
                'message'=>"Data validation error",
                'error'=> $validator->errors(),
                'status'=> 400,
            ];
            return response()->json($data,400);

        }

        $product = Product::create([
            'name' => $request->name,
            'quantity' => $request->quantity,
        ]);

        if(!$product){
            $data = [
                'message' => 'Error creating product',
                'status' => 500,
            ];
            return response()->json($data,500);
        }
        $data = [
            'product' => $product,
            'status'=> 201
        ];
        return response()->json($data, 201);

    }
    public function update(Request $request, $id){

        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'string', 'max:255'],
            'quantity' => ['sometimes', 'integer', 'min:0'],
        ]);
        
        if (empty($request->name) && empty($request->quantity)) {
            return response()->json([
                'message' => 'At least one field is required'
            ], 422);
        }       

        $product = Product::find($id);
        if(! empty($request->name)) $product->name = $request->name;
        if(! empty($request->quantity)) $product->quantity = $request->quantity;

        $product->save();
        $data = [
            'message'=>'Product updated',
            'product'=> $product,
            'status'=> 200
        ];
        
        return response()->json($data, 200);

    }
    public function show(Request $request, $id){
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }
        
        return response()->json($product, 200);

    }

    public function delete(Request $request, $id){
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }
        
        $product->delete();

        $data = [
            'message' => 'Producto deleted',
            'status' => 200
        ];
        
        return response()->json($data, 200);

    }
}
