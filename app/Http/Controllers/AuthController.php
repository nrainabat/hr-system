<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('home'); // Ensure this points to your actual login view file
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt([
            'username' => $request->username,
            'password' => $request->password,
            'status'   => 'active'
        ])) {
            $request->session()->regenerate();

            // === FIX IS HERE ===
            return match (Auth::user()->role) {
                'admin'      => redirect('/admin/dashboard'),
                'supervisor' => redirect('/supervisor/dashboard'), // Redirect to Supervisor Controller
                'employee', 'intern' => redirect('/employee/dashboard'),
                default => redirect('/'),
            };
        }

        return back()->with('error', 'Invalid username or password');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}