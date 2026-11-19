<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $monthExpression = DB::getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', starts_at)"
            : "DATE_FORMAT(starts_at, '%Y-%m')";

        $invoiceMonthExpression = DB::getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', invoice_date)"
            : "DATE_FORMAT(invoice_date, '%Y-%m')";

        DB::statement("CREATE VIEW v_dashboard_afspraken_per_maand AS
            SELECT {$monthExpression} AS maand,
                   COUNT(*) AS aantal_afspraken,
                   SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS geannuleerd
            FROM appointments
            GROUP BY {$monthExpression}");

        DB::statement("CREATE VIEW v_dashboard_omzet_per_maand AS
            SELECT {$invoiceMonthExpression} AS maand,
                   SUM(total_amount) AS omzet
            FROM invoices
            WHERE status IN ('outstanding', 'paid')
            GROUP BY {$invoiceMonthExpression}");

        DB::statement("CREATE VIEW v_dashboard_populaire_behandelingen AS
            SELECT treatments.name AS naam,
                   COUNT(appointments.id) AS aantal
            FROM treatments
            LEFT JOIN appointments
                ON appointments.treatment_id = treatments.id
                AND appointments.status <> 'cancelled'
            GROUP BY treatments.id, treatments.name");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_dashboard_populaire_behandelingen');
        DB::statement('DROP VIEW IF EXISTS v_dashboard_omzet_per_maand');
        DB::statement('DROP VIEW IF EXISTS v_dashboard_afspraken_per_maand');
    }
};
