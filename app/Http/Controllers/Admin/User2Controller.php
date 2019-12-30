<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Facades\App\Helpers\Json;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class User2Controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $dropdown = $request->input('dropdown') ?? 'name';
        $search = '%' . $request->input('search') . '%';
        if ($dropdown == "1"){
            $users = User::orderBy('name', 'DESC')
                ->where('name', 'like', $search)
                ->where('email', 'like', $search)
                ->paginate(9);
            $active = compact('dropdown');
        }
        elseif ($dropdown == "2"){
            $users = User::orderBy('email', 'DESC')
                ->where('name', 'like', $search)
                ->where('email', 'like', $search)
                ->paginate(9)
            ;
            $active = compact('dropdown');
        }
        elseif ($dropdown == "3"){
            $users = User::orderBy('active')
                ->where('name', 'like', $search)
                ->where('email', 'like', $search)
                ->paginate(9)
            ;
            $active = compact('dropdown');
        }
        elseif ($dropdown == "4"){
            $users = User::orderBy('admin', 'DESC')
                ->where('name', 'like', $search)
                ->where('email', 'like', $search)
                ->paginate(9)
            ;
            $active = compact('dropdown');
        }
        else{
            $users = User::orderBy($dropdown)
                ->where('name', 'like', $search)
                ->where('email', 'like', $search)
                ->paginate(9)
            ;
            $active = compact('dropdown');
        }
        $result = compact('users');

        Json::dump($result);
        Json::dump($active);
        return view('admin.users2.index', $result, $active);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect('admin/users2');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return redirect('admin/users2');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return redirect('admin/users2');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $this->validate($request,[
            'name' => 'required|min:3|max:255',
            'email' => 'required|email|unique:users,email'
        ]);
        $name = $request->input('name');
        $email = $request->input('email');
        $admin = (int)$request->input('admin');
        $active = (int)$request->input('active');
        $user->name = $name;
        $user->email = $email;
        $user->admin = $admin;
        $user->active = $active;
        $user->save();

        return response()->json([
            'type' => 'success',
            'text' => "The user <b>$user->name</b> has been updated"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ($user->id == auth()->user()->id){
            return response()->json([
                'type' => 'error',
                'text' => "You can not delete your own profile!"
            ]);
        }else{
            $user->delete();
            return response()->json([
                'type' => 'success',
                'text' => "The user <b>$user->name</b> has been deleted!"
            ]);
        }
    }
}
