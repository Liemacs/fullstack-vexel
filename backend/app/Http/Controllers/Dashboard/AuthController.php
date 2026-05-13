<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function show(): View
    {
        return view('dashboard.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = (string) Config::get('dashboard.email');
        $password = (string) Config::get('dashboard.password');
        $passwordMatches = Str::startsWith($password, '$2y$')
            ? Hash::check($credentials['password'], $password)
            : hash_equals($password, $credentials['password']);

        if (! hash_equals($email, $credentials['email']) || ! $passwordMatches) {
            return back()
                ->withErrors(['email' => 'Неверный логин или пароль.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();
        $request->session()->put('dashboard_authenticated', true);

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('dashboard_authenticated');
        $request->session()->regenerateToken();

        return redirect()->route('dashboard.login');
    }
}
