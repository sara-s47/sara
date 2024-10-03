<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Storage;

class UserController extends Controller
{

    public function __construct(){
        $this->middleware(['permission:users_read'])->only('index');
        $this->middleware(['permission:users_create'])->only('create');
        $this->middleware(['permission:users_update'])->only('update');
        $this->middleware(['permission:users_delete'])->only('destroy');
    }
    public function index (Request $request){
        $users = User::whereRoleIs('admin')->where(function ($q) use ($request) {

            return $q->when($request->search, function ($query) use ($request) {

                return $query->where('first_name', 'like', '%' . $request->search . '%')
                    ->orWhere('last_name', 'like', '%' . $request->search . '%');

            });
        })->latest()->paginate(5);
        return view('dashboard.users.index'  , ['users'=>$users]);
    }

    public function create(){
        return view('dashboard.users.create');
    }

    public function store(Request $request){
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'image' => 'image',
            'password' => 'required|confirmed',

        ]);

        $request_data = $request->except(['password' , 'password_confirmation' , 'permissions' , 'image']);
        $request_data['password'] = bcrypt($request->password);

        if($request->image){
            Image::make($request->image)->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save(public_path('uploads/user_images' . $request->image->hashName()) , 60);

            $request_data['image'] = $request->image->hashName();
        }

        $user = User::create($request_data);
        $user->attachRole('admin');
        $user->syncPermissions($request->permissions);

        session()->flash('success' , ('added successfuly'));
        return redirect()->route('dashboard.users.index');
    }

    public function update(Request $request , User $user){
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'image' => 'image'

        ]);

        $request_data = $request->except(['permissions']);
        $user->update($request_data);
        $user->syncPermissions($request->permessions);

        return view('dashboard.users.edit' , ['user' => $user]);
    }

    public function destroy(User $user)
    {
        if ($user->image != 'default.png') {

            Storage::disk('public_uploads')->delete('/user_images/' . $user->image);

        }//end of if

        $user->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.users.index');

    }
}
