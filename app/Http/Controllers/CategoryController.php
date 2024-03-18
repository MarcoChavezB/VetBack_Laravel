<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

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
}
