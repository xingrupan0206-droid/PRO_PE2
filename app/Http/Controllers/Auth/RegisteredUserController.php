<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Happy: a new account is created with the role patient and a confirmation is shown.
     * Unhappy: an e-mail address that already exists is rejected with a clear message.
     */
    public function store(RegisterUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        User::create([
            'role_id' => Role::where('name', 'patient')->value('id'),
            'name' => trim(implode(' ', array_filter([
                $validated['first_name'],
                $validated['prefix'] ?? null,
                $validated['last_name'],
            ]))),
            'first_name' => $validated['first_name'],
            'prefix' => $validated['prefix'] ?? null,
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'is_active' => true,
        ]);

        return redirect()->route('login')->with('status', 'Je account is succesvol aangemaakt.');
    }
}
