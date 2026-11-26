<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Happy: correct e-mail and password logs the user in and sends them to the dashboard.
     * Unhappy: wrong e-mail or password shows "Het e-mailadres of wachtwoord is onjuist.".
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Vul je e-mailadres in.',
            'email.email' => 'Vul een geldig e-mailadres in.',
            'password.required' => 'Vul je wachtwoord in.',
        ]);

        if (! Auth::attempt($credentials + ['is_active' => true], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Het e-mailadres of wachtwoord is onjuist.',
            ]);
        }

        $request->session()->regenerate();
        $request->user()->forceFill(['last_login_at' => now()])->save();

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Happy: the logout is saved in the audit log, the session ends and the user goes to the login page.
     * Unhappy: saving the logout fails because of a database error, the user stays logged in and sees a message.
     */
    public function destroy(Request $request): RedirectResponse
    {
        try {
            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'logout',
                'table_name' => 'users',
                'record_id' => $request->user()->id,
                'occurred_at' => now(),
                'ip_address' => $request->ip(),
            ]);
        } catch (QueryException $exception) {
            Log::error('Uitloggen kon niet worden opgeslagen.', ['exception' => $exception]);

            return back()->with('error', 'Uitloggen is niet gelukt door een databasefout. Probeer het opnieuw.');
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Je bent succesvol uitgelogd.');
    }
}
