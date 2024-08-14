<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;

class AuthController extends Controller
{
    public function loginPage(string $error_message = null)
    {
        return view('login', [
            'error_message' => $error_message
        ]);
    }

    public function attemptLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'password' => 'required|string|max:255'
        ]);

        if ($validator->fails())
        {
            return $this->loginPage( $validator->errors()->first() );
        }

        if (Auth::attempt([
            'name' => $request->name,
            'password' => $request->password
        ]))
        {
            $request->session()->regenerate();

            return redirect('/dashboard');
        }

        return $this->loginPage('Wrong credentials!');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
