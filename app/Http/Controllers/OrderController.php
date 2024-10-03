<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index (Request $request)
    {

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Client $client)
    {
        $categories = Category::with('products')->get();
        $order = $client->orders()->with('products')->paginate(5);
        return view('dashboard.clients.orders.create', compact('client', 'categories' , 'orders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Client $client)
    {
        $request->validate([

            'products' => 'required|array',
        ]);

        $this->attach_order($request , $client);
        
        session()->flash('success' , 'added successfully');
        return redirect()->route('dashboard.orders.index');

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
    public function edit(Client $client, Order $order)
    {
        $categories = Category::with('products')->get();
        $order = $client->orders()->with('products')->paginate(5);
        return view('dasboard.clients.orders.index' , compact('client', 'order' , 'categories' , 'orders'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client, Order $order)
    {
        $request->validate([

            'products' => 'required|array',

        ]);

        $this->detach_order($order);
        $this->attach_order($request , $client);

        session()->flash('success' , 'updated successfully');

        return redirect()->route('dashboard.orders.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client, Order $order)
    {
        //
    }

    private function attach_order( $request , $client ){
        $order = $client->orders()->create([]);

        $order->product()->attach($request->products);

        $total_price = 0;

        foreach ($request->products as $id=> $quantity) {

            $product = Product::findOrFail($id);

            $total_price += $product->sale_price * $quantity['quanitty'];

            $product->update(['stock'=> $product->stoc - $request->quantities['quantity']]);

        }

        $order->update(['total_price' => $total_price]);

    }

    private function detach_order($order){
        
        foreach($order->products as $product){
            $product->update(['stock' => $product->stock + $product->pivot->quantity]);
        }
        $order->delete();
    }
}
