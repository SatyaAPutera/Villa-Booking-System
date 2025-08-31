<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;


class UserController extends Controller
{

    /**
     * Display a listing of the user.
     */
    public function index()
    {
        $users = User::get();
        return $this->renderView('admin.user.index', compact('users'), 'Users');
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return $this->renderView('admin.user.create_user', [], 'Add New User');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|min:3|max:255|unique:users,username',
            'mobile' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'mobile' => $request->mobile,
            'password' => bcrypt($request->password),
        ];

        User::create($userData);

        return redirect()->route('admin.user.index')->with('success', 'User created successfully!');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        return $this->renderView('admin.user.show', compact('user'), 'User Details');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return $this->renderView('admin.user.edit', compact('user'), 'Edit User Details');
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'username' => 'required|min:6',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
        ]);

        return redirect()->route('admin.user.index')->with('success', 'User details updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return  redirect()->route('admin.user.index')->with('success', 'User deleted successfully!');
    }
}
