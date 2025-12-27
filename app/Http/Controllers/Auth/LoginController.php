<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
            'status'   => 'active',
        ];

       if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        $role = Auth::user()->role;

        if ($role === 'admin') {
            return redirect('/admin/dashboard');
        } elseif ($role === 'supervisor') {
            return redirect('/supervisor/dashboard');
        } else {
            return redirect('/employee/dashboard');
        }
    }

    return back()->with('error', 'Invalid username or password');

        return back()->with('error', 'Invalid username or password');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
