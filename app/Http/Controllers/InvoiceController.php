<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    /**
     * Dutch labels for the invoice statuses.
     *
     * @var array<string, string>
     */
    public const STATUS_LABELS = [
        'draft' => 'Concept',
        'outstanding' => 'Openstaand',
        'paid' => 'Betaald',
        'overdue' => 'Verlopen',
    ];

    /**
     * Happy: a staff member sees all invoices with number, patient name, date, amount and payment status.
     * Unhappy: when the invoices cannot be loaded from the database a clear message is shown instead.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('zoek', ''));
        $status = (string) $request->query('status', '');

        if (! array_key_exists($status, self::STATUS_LABELS)) {
            $status = '';
        }

        try {
            $invoices = Invoice::query()
                ->with('patient.user')
                ->when($status !== '', fn (Builder $query) => $query->where('status', $status))
                ->when($search !== '', function (Builder $query) use ($search): void {
                    $query->where(function (Builder $searchQuery) use ($search): void {
                        $searchQuery->where('invoice_number', 'like', "%{$search}%")
                            ->orWhereHas('patient.user', function (Builder $userQuery) use ($search): void {
                                $userQuery->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                                    ->orWhere('name', 'like', "%{$search}%");
                            });
                    });
                })
                ->orderByDesc('invoice_date')
                ->orderByDesc('invoice_number')
                ->paginate(15)
                ->withQueryString();
        } catch (QueryException $exception) {
            Log::error('Facturatieoverzicht kon niet worden geladen.', ['exception' => $exception]);

            return view('invoices.index', [
                'invoices' => null,
                'search' => $search,
                'status' => $status,
                'statusLabels' => self::STATUS_LABELS,
                'loadError' => 'Het facturatieoverzicht kan niet worden geladen. Probeer het later opnieuw.',
            ]);
        }

        return view('invoices.index', [
            'invoices' => $invoices,
            'search' => $search,
            'status' => $status,
            'statusLabels' => self::STATUS_LABELS,
            'loadError' => null,
        ]);
    }
}
