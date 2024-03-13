<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    function index (){
        $producto = Product::with(['category' => function ($query){
            $query->select('id', 'category');
        }])->where('is_active', 1)->get();
        
        return response()->json([
            "products" => $producto
        ]);
    }

    function indexDisabled (){
        $producto = Product::with(['category' => function ($query){
            $query->select('id', 'category');
        }])->where('is_active', 0)->get();
        
        return response()->json([
            "products" => $producto
        ]);
    }


    function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required | min:3 | max:100 | unique:products,name',
            'description' => 'required | min:3 | max:100',
            'category_id' => 'required | integer | exists:categories,id',
            'stock' => 'required | integer | min:1',
            'price' => 'required | numeric | min:1'
        ], [
            'name.required' => 'El nombre es requerido',
            'name.min' => 'El nombre debe tener al menos 3 caracteres',
            'name.max' => 'El nombre debe tener como máximo 100 caracteres',
            'name.unique' => 'El producto ya existe',

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
    
        $productosActivos = Product::where('is_active', 1)->get();
    
        return response()->json([
            "message" => "Producto eliminado con éxito",
            "products" => $productosActivos
        ]);
    }

    function activateProd($id){
        $producto = Product::find($id);
    
        if(!$producto){
            return response()->json([
                "error" => "Producto no encontrado"
            ], 404);
        }
    
        $producto->is_active = true;
        $producto->save();
    
        return response()->json([
            "message" => "Producto activado con exito",
        ]);
    }
    
    function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'name' => 'min:3 | max:100',
            'category_id' => 'integer | exists:categories,id',
            'description' => 'min:3 | max:100',
            'price' => 'numeric | min:1', 
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
    
        $producto = Product::find($id);
        if(!$producto){
            return response()->json([
                "error" => "Producto no encontrado"
            ], 404);
        }
        
        $originalData = $producto->toArray(); // Almacena los datos originales antes de la actualización
    
        $producto->category_id = $request->category_id ?? $producto->category_id;
        $producto->name = $request->name ?? $producto->name;
        $producto->description = $request->description ?? $producto->description;
        $producto->price = $request->price ?? $producto->price;
        $producto->stock = $request->stock ?? $producto->stock;
        $producto->save();
    
        $updatedData = $producto->toArray(); // Almacena los datos actualizados después de la actualización
    
        if($originalData === $updatedData){
            return response()->json([
                "message" => "No se realizaron cambios"
            ], 400);
        }
    
        return response()->json([
            "message" => "Producto actualizado con éxito",
            "producto" => $producto
        ]);
    }

    function show($id){
        $product = Product::find($id);

        if(!$product){
            return response()->json([
                "error" => "Producto no encontrado"
            ], 404);
        }

        return response()->json([
            "products" => $product
        ]);
    }

    function totalProducts(){
        $totalProducts = Product::where('is_active', 1)->count();
        return response()->json([
            "total" => $totalProducts
        ]);
    }

    function stockBajo(){
        $productos = Product::where('stock', '<', 5)->get();
        return response()->json([
            "products" => $productos
        ]);
    }
}
