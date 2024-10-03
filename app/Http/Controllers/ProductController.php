<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Storage;

class ProductController extends Controller
{

    public function index(Request $request)
    {

        $categories = Category::all();

        $products = Product::when($request->search , function($q) use($request){

            return $q->where('name', 'LIKE', '%' . $request->search . '%');

        })->when($request->category_id , function($q)use ($request){

            return $q->where('category_id' , $request->category_id);
        })->latest()->paginate(5);
        return view('dashboard.products.index', compact('products' , 'categories'));
    }


    public function create()
    {
        $categories = Category::all();
        return view('dashboard.products.create', compact('categories'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique',
            'category_id' => 'required',
            'description' => 'required|string',
            'purchase_price' => 'required|string',
            'sales_price' => 'required|string',
            'image' => 'rquired',
            'stock' => 'required|string'
        ]);

        $request_data = $request->all();

        if ($request->image) {
            Image::make($request->image)->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save(public_path('uploads/products_images' . $request->image->hashName()), 60);

            $request_data['image'] = $request->image->hashName();
        }

        Product::create($request_data);
        session()->flash('success' , ('added successfuly'));
        return redirect()->route('dashboard.products.index');
    }


    public function show(Product $product)
    {
        //
    }


    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('dashboard.products.edit', compact('categories', 'product'));
    }


    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|unique',
            'category_id' => 'required',
            'description' => 'required|string',
            'purchase_price' => 'required|string',
            'sales_price' => 'required|string',
            'image' => 'rquired',
            'stock' => 'required|string'
        ]);

        $request_data = $request->all();

        if ($request->image) {
            Image::make($request->image)->resize(300, null, function ($constraint) {

                $constraint->aspectRatio();

            })->save(public_path('uploads/products_images' . $request->image->hashName()), 60);

            if ($product->image) {

                $oldImagePath = public_path('uploads/products_images/' . $product->image);

                if (file_exists($oldImagePath)) {

                    unlink($oldImagePath); 
                }
            }

            $request_data['image'] = $request->image->hashName();
        }

        $product->update($request_data);
        session()->flash('success' , ('updated successfuly'));
        return redirect()->route('dashboard.products.index');
    }


    public function destroy(Product $product)
    {
        $product->delete();
        Storage::disk('public_uploads')->delete('/product_images/' . $product->image);
        session()->flash('success' , 'deleted sucessfully');
        return redirect()->route('dashboard.products.index');
    }
}
