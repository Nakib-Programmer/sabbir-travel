<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserManagmentController extends Controller
{
    public function index()
    {
        $data = User::where('type',0)->get(); // Fetch all users
        return view('backend.user.index', compact('data'));
    }

    public function create()
    {
        return view('backend.user.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password), // Hash the password
            ]);
            return redirect()->route('user-list.index')->with('success', 'User created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while storing data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id); // Fetch user by ID
        return view('backend.user.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8', // Optional password update
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $request->only(['name', 'email']);
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password); // Hash new password
            }

            User::where('id', $id)->update($data);
            return redirect()->route('user-list.index')->with('success', 'User updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while updating data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        User::where('id', $id)->delete(); // Delete user by ID
        return redirect()->route('user-list.index')->with('success', 'User deleted successfully!');
    }
}
