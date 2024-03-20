<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    function index(){
        $categories = Category::where('is_active', 1)->get();
        return response()->json([
            "categories" => $categories
        ]);
    }

    function indexDesactivated(){
        $categories = Category::where('is_active', 0)->get();
        return response()->json([
            "categories" => $categories
        ]);
    }

    function indexActivate($id){
        $category = Category::find($id);
        if(!$category){
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }
        $category->is_active = 1;
        $category->save();
        return response()->json(['message' => 'Categoría activada exitosamente']);
    }

    function destroy($id){
        $category = Category::find($id);
        if(!$category){
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }
        $category->is_active = 0;
        $category->save();
        return response()->json(['message' => 'Categoría eliminada exitosamente']);
    }

    function getCategory($id){
        $category = Category::find($id);
        if(!$category){
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }
        return response()->json(['category' => $category]);
    }

    function update(Request $request, $id){
        $category = Category::find($id);
        if(!$category){
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }

        Validator::extend('alpha_spaces', function($attribute, $value) {
            return preg_match('/^[\pL\s]+$/u', $value);
        });
        
        $validator = Validator::make($request->all(), [
            'category' => 'alpha_spaces|string|max:100|unique:categories,category,'.$id,
            'description' => 'alpha_spaces | string | max:255'
        ],
        [
            'category.alpha_spaces' => 'La categoría debe ser un texto',
            'category.unique' => 'La categoría ya existe',
            'category.string' => 'La categoría debe ser un texto',
            'category.max' => 'La categoría debe tener máximo 100 caracteres',
            'description.alpha_spaces' => 'La descripción debe ser un texto',
            'description.string' => 'La descripción debe ser un texto',
            'description.max' => 'La descripción debe tener máximo 255 caracteres'
        ]); 

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 400);
        }

        $category->category = $request->category ?? $category->category;
        $category->description = $request->description ?? $category->description;
        $category->save();
        return response()->json(['message' => 'Categoría actualizada exitosamente']);
    }

    function store(Request $request){
        Validator::extend('alpha_spaces', function($attribute, $value) {
            return preg_match('/^[\pL\s]+$/u', $value);
        });
        
        $validator = Validator::make($request->all(), [
            'category' => 'alpha_spaces|required|string|max:100|unique:categories,category',
            'description' => 'alpha_spaces | string | max:255'
        ],
        [
            'category.alpha_spaces' => 'La categoría debe ser un texto',
            'category.unique' => 'La categoría ya existe',
            'category.string' => 'La categoría debe ser un texto',
            'category.max' => 'La categoría debe tener máximo 100 caracteres',
            'category.required' => 'La categoría es requerida',
            'description.alpha_spaces' => 'La descripción debe ser un texto',
            'description.string' => 'La descripción debe ser un texto',
            'description.max' => 'La descripción debe tener máximo 255 caracteres'
        ]); 

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 400);
        }
        $category = new Category();
        $category->category = $request->category;
        $category->description = $request->description;
        $category->save();
        return response()->json(['message' => 'Categoría creada exitosamente'], 201);
    }
}
