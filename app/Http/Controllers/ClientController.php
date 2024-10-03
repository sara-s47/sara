<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $clients = Client::when($request->search, function ($q) use ($request) {
            return $q->where('name', 'like', '%', $request->search, '%')
                ->orWhere('phone', 'like', '%', $request->search, '%')
                ->orWhere('email', 'like', '%', $request->search, '%');
        })->latest()->paginate(5);
        return view('dashboard.clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.clients.create');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required|array',
            'phone.0' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        $request_data = $request->all();
        $request_data['phone'] = array_filter($request->phone);

        Client::create($request_data);
        session()->flash('success', 'client added successfully');
        return redirect()->route('dashboard.clients.index');
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
    public function edit(Client $client)
    {
        return view('dashboard.clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required|array',
            'phone.0' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        $request_data = $request->all();
        $request_data['phone'] = array_filter($request->phone);

        $client->update($request_data);
        session()->flash('success', 'client updated successfully');
        return redirect()->route('dashboard.clients.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $client->delete();
        session()->flash('success', 'deleted successfully');
        return redirect()->route('dashboard.clients.index');

    }
}
