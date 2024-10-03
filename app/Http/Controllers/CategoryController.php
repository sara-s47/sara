<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = Category::when($request->search , function($q) use ($request) {
            return $q::where('name' , 'like' , '%' . $request->search . '%' );
        })->latest()->paginate(5);

        return view('dashboard.categories.index', compact('categories'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view ('dashboard.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories,name'
        ]);

        Category::create($request->all());
        session()->flash('success' , 'category added successfully');
        return redirect()->route('dashboard.categories.index');
    }

   
    public function edit(Category $category)
    {
        return view('dashboard.categories.edit', compact('caetgory'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|unique,category,name,' . $category->id 
        ]);

        Category::update($request->all());
        session()->flash('success' , 'updated successfully');
        return redirect()->route('dashboard.categories.index');
    }
    
    
    public function destroy(Category $category)
    {
        $category->delete();
        session()->flash('success' , 'deleted successfully');
        return redirect()->route('dashboard.categories.index');
    }
}
