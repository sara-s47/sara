<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use DB;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        $products_count = Product::count();
        $categories_count = Category::count();
        $clients_count = Category::count();
        $order_count = Order::count();
        $users_count = User::count();

        $sales_data = Order::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_price) as total_price')
        )->groupBy('month')->get();

        
        return view('dashboard.welcome', compact('products_count', 'categories_count', 'users_count', 'clients_count', 'sales_data' , 'order_count'));
    }

    


}
