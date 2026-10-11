```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Display the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle the login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => [
                    'The provided credentials do not match our records.',
                ],
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Reject inactive accounts.
        if (!$user->is_active) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => [
                    'Your account has been deactivated. Please contact administrator.',
                ],
            ]);
        }

        // Redirect directly to the dashboard for the user's role.
        // Do not use redirect()->intended() here.
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');

            case 'highboard':
                return redirect()->route('highboard.dashboard');

            case 'board':
                return redirect()->route('board.dashboard');

            default:
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                throw ValidationException::withMessages([
                    'email' => [
                        'Your account does not have a valid role. Please contact administrator.',
                    ],
                ]);
        }
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
```