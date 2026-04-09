<?php

namespace App\Console\Commands;

use App\Models\Loan;
use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckOverdueLoans extends Command
{
    protected $signature = 'loans:check-overdue';
    protected $description = 'Generate notifications for overdue loans';

    public function handle(): int
    {
        $overdueLoans = Loan::whereNull('returned_at')
            ->whereNotNull('due_at')
            ->where('due_at', '<', now())
            ->get();

        $count = 0;

        foreach ($overdueLoans as $loan) {
            if (!$loan->masjid_id) continue;

            // Check if we already notified about this loan today
            $existing = Notification::where('type', 'loan_overdue')
                ->where('link', route('loans.show', $loan))
                ->whereDate('created_at', today())
                ->exists();

            if ($existing) continue;

            Notification::notifyMasjidAdmins(
                $loan->masjid_id,
                'loan_overdue',
                'Peminjaman Jatuh Tempo',
                "Peminjaman {$loan->borrower_name} ({$loan->item->name}) sudah melewati tanggal jatuh tempo (" . $loan->due_at->format('d/m/Y') . ")",
                route('loans.show', $loan)
            );
            $count++;
        }

        $this->info("Sent overdue notifications for {$count} loans.");
        return Command::SUCCESS;
    }
}
