<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Permission;
use App\Models\Admin\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
//use Gate;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden |You cannot  Access this page because you do not have permission');

        // users data with order by id desc
        $users = User::orderBy('id', 'desc')->with('roles')->get();
         $activeUsers = User::active()->pluck('id')->toArray();

        return response()->view('admin.users.index', compact('users', 'activeUsers'));
    }

     public function ActiveUserindex()
    {
        //$users = User::all();
        //$activeUsers = User::active()->pluck('id')->toArray();
         $activeUsers = User::active()->get();

        return view('admin.users.active_user', compact('activeUsers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_if(Gate::denies('user_create'), Response::HTTP_FORBIDDEN, '403 Forbidden |You cannot  Access this page because you do not have permission');

        $roles = Role::all()->pluck('title', 'id');

        return response()->view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // assign role to user
        $user->roles()->sync($request->input('roles', []));

        // Return a JSON response
        //return response()->json(['message' => 'User created successfully'], 200);
        return redirect()->route('admin.users.index')->with('toast_success', 'User created successfully');
    }

    public function UserPwdChange(Request $request)
{
    $data = $request->input('users', []);

    try {
        foreach ($data as $userId => $userData) {
            if (isset($userData['password'])) {
                $user = User::findOrFail($userId);
                $user->update([
                    'password' => Hash::make($userData['password']),
                ]);
            }
        }

        // Assuming you are updating the password for a single user in the form
        $user = User::findOrFail(array_key_first($data));
        $password = $data[$user->id]['password'];

        return redirect()->route('admin.users.index')
            ->with('success', 'User password updated successfully.')
            ->with('username', $user->name)
            ->with('phone', $user->phone)
            ->with('password', $password);
    } catch (\Exception $e) {
        return redirect()->route('admin.users.index')->with('error', 'An error occurred while updating user password: ' . $e->getMessage());
    }
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        abort_if(Gate::denies('user_show'), Response::HTTP_FORBIDDEN, '403 Forbidden |You cannot  Access this page because you do not have permission');

        $user_detail = User::with(['roles', 'roles.permissions'])->findOrFail($id);
        $roles = Role::all();
        $permissions = Permission::all();

        return view('admin.users.show', compact('user_detail', 'roles', 'permissions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        abort_if(Gate::denies('user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden |You cannot  Access this page because you do not have permission');

        $user_edit = User::find($id);
        $roles = Role::all()->pluck('title', 'id');

        return response()->view('admin.users.edit', compact('user_edit', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        $user->update($request->all());
        $user->roles()->sync($request->input('roles', []));

        return redirect()->route('admin.users.index', $user->id)->with('toast_success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        abort_if(Gate::denies('user_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden |You cannot  Access this page because you do not have permission');

        $user = User::find($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
    }

    public function massDestroy(Request $request)
    {
        User::whereIn('id', request('ids'))->delete();

        return response(null, 204);
    }
}
