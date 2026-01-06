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
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validation is there
            'phone_number'  => 'nullable|string|max:20',
            'gender'        => 'nullable|in:Male,Female',
            'about'         => 'nullable|string|max:500',
            'address'       => 'nullable|string|max:500',
        ]);

        // === ADD THIS BLOCK ===
        if ($request->hasFile('profile_image')) {
            // 1. Delete old image if it exists (optional but recommended)
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            // 2. Store the new image in 'storage/app/public/profile_images'
            $path = $request->file('profile_image')->store('profile_images', 'public');

            // 3. Save the path to the user object
            $user->profile_image = $path;
        }
        // ======================

        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;
        $user->gender = $request->gender;
        $user->about = $request->about;
        $user->address = $request->address;

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