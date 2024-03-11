<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    function index (){
        return response()->json([
            "products" => Product::all()->where('is_active', true)
        ]);
    }

    function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required | min:3 | max:100',
            'category_id' => 'required | integer | exists:categories,id',
            'description' => 'required | min:3 | max:100',
            'price' => 'required | numeric | min:0', 
            'stock' => 'required | integer | min:1'
        ], [
            'name.required' => 'El nombre es requerido',
            'name.min' => 'El nombre debe tener al menos 3 caracteres',
            'name.max' => 'El nombre debe tener como máximo 100 caracteres',

            'category_id.required' => 'La categoría es requerida',
            'category_id.integer' => 'La categoría debe ser un número entero',
            'category_id.exists' => 'La categoría no existe',

            'description.required' => 'La descripción es requerida',
            'description.min' => 'La descripción debe tener al menos 3 caracteres',
            'description.max' => 'La descripción debe tener como máximo 100 caracteres',
            
            'price.required' => 'El precio es requerido',
            'price.numeric' => 'El precio debe ser un número',
            'price.min' => 'El precio debe ser mayor a 0',
            
            'stock.required' => 'El stock es requerido',
            'stock.integer' => 'El stock debe ser un número entero',
            'stock.min' => 'El stock debe ser mayor a 0'
        ]);

        $validatorErrors = $validator->errors()->toArray();

        if ($validator->fails()) {
            return response()->json([
                'error' => $validatorErrors
            ], 400);
        }

        $producto = new Product();
        $producto->name = $request->name;
        $producto->category_id = $request->category_id;
        $producto->description = $request->description;
        $producto->price = $request->price;
        $producto->stock = $request->stock;
        $producto->save();

        return response()->json([
            "message" => "Producto creado con éxito",
            "producto" => $producto
        ]);
    }

    function destroy($id){
        $producto = Product::find($id);
        if(!$producto){
            return response()->json([
                "error" => "Producto no encontrado"
            ], 404);
        }

        $producto->is_active = false;
        $producto->save();

        return response()->json([
            "message" => "Producto eliminado con éxito"
        ]);
    }

    function update(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'min:3 | max:100',
            'category_id' => 'integer | exists:categories,id',
            'description' => 'min:3 | max:100',
            'price' => 'numeric | min:0', 
            'stock' => 'integer | min:1'
        ], [

            'name.min' => 'El nombre debe tener al menos 3 caracteres',
            'name.max' => 'El nombre debe tener como máximo 100 caracteres',

            'category_id.integer' => 'La categoría debe ser un número entero',
            'category_id.exists' => 'La categoría no existe',

            'description.min' => 'La descripción debe tener al menos 3 caracteres',
            'description.max' => 'La descripción debe tener como máximo 100 caracteres',
            
            'price.numeric' => 'El precio debe ser un número',
            'price.min' => 'El precio debe ser mayor a 0',
            
            'stock.integer' => 'El stock debe ser un número entero',
            'stock.min' => 'El stock debe ser mayor a 0'
        ]);

        $validatorErrors = $validator->errors()->toArray();
        if($validator->fails()){
            return response()->json([
                'error' => $validatorErrors
            ], 400);
        }

        $producto = Product::find($request->id);
        if(!$producto){
            return response()->json([
                "error" => "Producto no encontrado"
            ], 404);
        }
        
        $producto->category_id = $request->category_id ?? $producto->category_id;
        $producto->name = $request->name ?? $producto->name;
        $producto->description = $request->description ?? $producto->description;
        $producto->price = $request->price ?? $producto->price;
        $producto->stock = $request->stock ?? $producto->stock;
        $producto->save();

        return response()->json([
            "message" => "Producto actualizado con éxito",
            "producto" => $producto
        ]);
    }
}
