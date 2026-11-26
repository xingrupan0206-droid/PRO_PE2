<?php

namespace App\Providers\Afspraak;

use Carbon\Carbon;
use Illuminate\Database\ConnectionException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Read
{
    public const FOUTMELDING = 'Momenteel kunnen er geen afspraken worden gevonden. Probeer het later opnieuw.';

    public function __construct(private readonly ?string $verbinding = null) {}

    /** @var array<string, string> */
    private const STATUS_LABELS = [
        'requested' => 'aangevraagd',
        'confirmed' => 'bevestigd',
        'rescheduled' => 'herpland',
        'cancelled' => 'geannuleerd',
        'completed' => 'afgerond',
    ];

    /**
     * @return array{afspraken: Collection<int, array{datum: string, tijdstip: string, medewerker: string, klant: string, status: string}>, foutmelding: string|null, statusFilter: string|null, statusLabels: array<string, string>}
     */
    public function overzicht(Request $request): array
    {
        $statusFilter = $request->string('status')->toString();

        if (! array_key_exists($statusFilter, self::STATUS_LABELS)) {
            $statusFilter = null;
        }

        try {
            $query = DB::connection($this->verbinding)
                ->table('appointments')
                ->join('employees', 'appointments.employee_id', '=', 'employees.id')
                ->join('users as employee_users', 'employees.user_id', '=', 'employee_users.id')
                ->join('patients', 'appointments.patient_id', '=', 'patients.id')
                ->join('users as patient_users', 'patients.user_id', '=', 'patient_users.id')
                ->select([
                    'appointments.id',
                    'appointments.starts_at',
                    'appointments.status',
                    'employee_users.name as employee_name',
                    'patient_users.name as patient_name',
                ])
                ->orderBy('appointments.starts_at')
                ->orderBy('appointments.id');

            if ($statusFilter !== null) {
                $query->where('appointments.status', $statusFilter);
            }

            $afspraken = $query->get()
                ->map(fn (object $afspraak): array => $this->mapAfspraak($afspraak));

            return [
                'afspraken' => $afspraken,
                'foutmelding' => null,
                'statusFilter' => $statusFilter,
                'statusLabels' => self::STATUS_LABELS,
            ];
        } catch (QueryException|ConnectionException $exception) {
            report($exception);

            return self::foutData();
        }
    }

    /**
     * @return array{afspraken: Collection<int, array{datum: string, tijdstip: string, medewerker: string, klant: string, status: string}>, foutmelding: string, statusFilter: null, statusLabels: array<string, string>}
     */
    public static function foutData(): array
    {
        return [
            'afspraken' => collect(),
            'foutmelding' => self::FOUTMELDING,
            'statusFilter' => null,
            'statusLabels' => self::STATUS_LABELS,
        ];
    }

    /**
     * @return array{datum: string, tijdstip: string, medewerker: string, klant: string, status: string}
     */
    private function mapAfspraak(object $afspraak): array
    {
        $startsAt = Carbon::parse($afspraak->starts_at);

        return [
            'datum' => $startsAt->format('d-m-Y'),
            'tijdstip' => $startsAt->format('H:i'),
            'medewerker' => (string) $afspraak->employee_name,
            'klant' => (string) $afspraak->patient_name,
            'status' => self::STATUS_LABELS[$afspraak->status] ?? (string) $afspraak->status,
        ];
    }
}
