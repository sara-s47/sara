<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class MainOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $orders = Order::wherehas('client' , function($q) use($request){
            return $q->where('name' , 'like' , '%' , $request->search , '%');
        })->paginate(5);

        return view('dashboard.orders.index' , compact('orders'));

        // $orders = Order::paginate(5);
        // return view('dashboard.orders.index', compact('orders'));
    }

    public function products(Order $order){
        $products = $order->products()->get();

        return view('dashboard.orders._products' , compact('products'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {

        foreach($order->products as $product){
            $product->update(['stock' => $product->stock + $product->pivot->quantity]);
        }
        $order->delete();
        session()->flash('success' , 'oreder deleted successfully');
        return redirect()->route('dashboard.orders.index');
    }
}
