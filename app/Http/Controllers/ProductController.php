<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

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


    function getProductByName($name){
        $producto = Product::with(['category' => function ($query){
            $query->select('id', 'category');
        }])->where('name', 'like', "%$name%")
        ->where('stock', '>', 0)->get();
        
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

        
        Validator::extend('alpha_spaces', function($attribute, $value) {
            return preg_match('/^[\pL\s]+$/u', $value);
        });

        $validator = Validator::make($request->all(), [
            'name' => 'alpha_spaces | required | min:3 | max:100 | unique:products,name',
            'description' => 'alpha_spaces | required | min:3 | max:100',
            'category_id' => 'required | integer | exists:categories,id',
            'stock' => 'required | integer | min:1',
            'price' => 'required | numeric | min:1'
        ], [
            'name.alpha_spaces' => 'El nombre debe contener solo letras',
            'name.required' => 'El nombre es requerido',
            'name.min' => 'El nombre debe tener al menos 3 caracteres',
            'name.max' => 'El nombre debe tener como máximo 100 caracteres',
            'name.unique' => 'El producto ya existe',

            'category_id.required' => 'La categoría es requerida',
            'category_id.integer' => 'La categoría debe ser un número entero',
            'category_id.exists' => 'La categoría no existe',

            'description.alpha_spaces' => 'La descripción debe contener solo letras',
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

        Validator::extend('alpha_spaces', function($attribute, $value) {
            return preg_match('/^[\pL\s]+$/u', $value);
        });

        $validator = Validator::make($request->all(), [
            'name' => 'alpha_spaces | min:3 | max:100',
            'category_id' => 'integer | exists:categories,id',
            'description' => 'alpha_spaces | min:3 | max:100',
            'price' => 'numeric | min:1', 
            'stock' => 'integer | min:1'
        ], [
            'name.alpha_spaces' => 'El nombre debe contener solo letras',
            'name.min' => 'El nombre debe tener al menos 3 caracteres',
            'name.max' => 'El nombre debe tener como máximo 100 caracteres',

            'category_id.integer' => 'La categoría debe ser un número entero',
            'category_id.exists' => 'La categoría no existe',

            'description.alpha_spaces' => 'La descripción debe contener solo letras',
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
                "message" => false
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

    function getTotal(Request $request){
        $products = $request->input('products');
        $total = 0;
    
        foreach ($products as $product) {
            // Obtener el precio del producto desde la base de datos
            $productId = $product['id'];
            $productQuantity = $product['cantidad'];
            $productPrice = Product::find($productId)->price;
    
            // Calcular el subtotal del producto y sumarlo al total
            $subtotal = $productPrice * $productQuantity;
            $total += $subtotal;
        }
    
        return $total;
    }
    

    function stockBajo(){
        $productos = Product::where('stock', '<', 5)->get();
        return response()->json([
            "products" => $productos
        ]);
    }

    public function realizarVenta(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'products' => 'required | array',
            'products.*.id' => 'required | integer | exists:products,id',
            'products.*.cantidad' => 'required | integer | min:1',
            'customerName' => 'required | string | min:3 | max:100',
            'customerLastName' => 'required | string | min:3 | max:100',
            'customerPhone' => 'required | string | min:10 | max:10'
        ], [
            'products.required' => 'Los productos son requeridos',
            'products.array' => 'Los productos deben ser un arreglo',
            'products.*.id.required' => 'El id del producto es requerido',
            'products.*.id.integer' => 'El id del producto debe ser un número entero',
            'products.*.id.exists' => 'El producto no existe',
            'products.*.quantity.required' => 'La cantidad es requerida',
            'products.*.quantity.integer' => 'La cantidad debe ser un número entero',
            'products.*.quantity.min' => 'La cantidad debe ser mayor a 0',

            'customerName.required' => 'El nombre de cliente es requerido',
            'customerName.string' => 'El nombre de cliente debe ser una cadena de texto',
            'customerName.min' => 'El nombre de cliente debe tener al menos 3 caracteres',
            'customerName.max' => 'El nombre de cliente  debe tener como máximo 100 caracteres',

            'customerLastName.required' => 'El apellido de cliente es requerido',
            'customerLastName.string' => 'El apellido de cliente  debe ser una cadena de texto',
            'customerLastName.min' => 'El apellido de cliente  debe tener al menos 3 caracteres',
            'customerLastName.max' => 'El apellido de cliente  debe tener como máximo 100 caracteres',

            'customerPhone.required' => 'El teléfono de cliente  es requerido',
            'customerPhone.string' => 'El teléfono de cliente  debe ser una cadena de texto',
            'customerPhone.min' => 'El teléfono de cliente  debe tener 10 caracteres',
            'customerPhone.max' => 'El teléfono de cliente  debe tener 10 caracteres'
        ]);

        if($validator->fails()){
            return response()->json([
                'error' => $validator->errors()
            ], 400);
        }

        foreach ($request->products as $index => $product) {
            $productId = $product['id'];
            $productQuantity = $product['cantidad'];
            $stock = Product::find($productId)->stock;
        
            if ($productQuantity > $stock) {
                return response()->json([
                    "error" => "El producto con id $productId no tiene suficiente stock"
                ], 400);
            }
        }

        $productos = json_encode($request->products);
        $customerName = $request->customerName;
        $customerLast = $request->customerLastName;
        $customerPhone = $request->customerPhone;
        $total = $request->total;

        DB::select("CALL RealizarVenta('$customerName', '$customerLast', '$customerPhone', '$productos', '$total')");

        return response()->json([
            "message" => "Venta realizada con éxito",
        ]);
    }

    public function indexVetas()
    {
        $ventas = Order::with('customer', 'orderDetails.product')->get();

        $ventasInfo = $ventas->map(function ($venta) {
            return [
                'cliente' => [ 
                    'nombre' => $venta->customer->name,
                    'telefono' => $venta->customer->phone,
                ],
                'fecha_venta' => now()->parse($venta->created_at)->format('d-m-Y'),
                'productos' => $venta->orderDetails->map(function ($detalle) {
                    return [
                        'producto' => $detalle->product->name,
                        'cantidad' => $detalle->quantity,
                    ];
                }),
                'total' => $venta->Total,
            ];
         });
        return response()->json($ventasInfo);
    }
}