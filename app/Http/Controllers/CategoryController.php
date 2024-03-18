<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    function index(){
        return response()->json([
            "categories" => Category::all()->where('is_active', true)
        ]);
    }
}
