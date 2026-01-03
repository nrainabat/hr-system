<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('profile', compact('user'));
    }

    public function update(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'name'          => 'required|string|max:255',
        'username'      => 'required|string|max:255|unique:users,username,' . $user->id,
        'email'         => 'required|email|max:255|unique:users,email,' . $user->id,
        'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'phone_number'  => 'nullable|string|max:20',
        'gender'        => 'nullable|in:Male,Female',
        'about'         => 'nullable|string|max:500',
        'address'       => 'nullable|string|max:500', // <--- Validation
    ]);

    // ... (image handling)

    $user->name = $request->name;
    $user->username = $request->username;
    $user->email = $request->email;
    $user->phone_number = $request->phone_number;
    $user->gender = $request->gender;
    $user->about = $request->about;
    $user->address = $request->address; // <--- Update

    $user->save();

    return redirect()->route('profile.show')->with('success', 'Profile updated successfully!');
}

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Current password does not match!']);
        }

        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password changed successfully!');
    }
}