<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientController extends Controller
{
    /**
     * Happy: a staff member sees all patients with name, birth date and phone number and can search them.
     * Unhappy: users without a staff role are stopped by the role middleware with a 403 message.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('zoek', ''));

        $patients = Patient::query()
            ->with('user')
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->whereHas('user', function (Builder $userQuery) use ($search): void {
                    $userQuery->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->join('users', 'users.id', '=', 'patients.user_id')
            ->orderBy('users.last_name')
            ->orderBy('users.first_name')
            ->select('patients.*')
            ->paginate(15)
            ->withQueryString();

        return view('patients.index', [
            'patients' => $patients,
            'search' => $search,
        ]);
    }
}
