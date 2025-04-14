<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validatedData = $request->validate([
            "email"=> "required|email",
            "password"=> "required",
        ]);

        if (Auth::attempt($validatedData)) {
            $user = Auth::user();

            if($user) {
                return redirect()->route('dashboard')->with('loginSuccess','loginSuccess');
            }

            return redirect()->back()->with('loginError','loginError');
        }

        return redirect()->back()->with('loginError', 'loginError');
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('login')->with('logoutSuccess','logoutSuccess');
    }
}
