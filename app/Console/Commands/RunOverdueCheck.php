<?php

namespace App\Console\Commands;

use App\Services\OverdueService;
use Illuminate\Console\Command;

/**
 * php artisan seapedia:check-overdue
 *
 * Can be wired to a scheduler (routes/console.php) or run manually /
 * via cron. Logic is identical to the Admin "Run Overdue Check" button
 * (both call OverdueService::run()), so behavior is consistent no
 * matter how it's triggered.
 */
class RunOverdueCheck extends Command
{
    protected $signature = 'seapedia:check-overdue';

    protected $description = 'Refund/return any order that has passed its delivery SLA (auto overdue handling).';

    public function handle(): int
    {
        $processed = OverdueService::run();

        if ($processed->isEmpty()) {
            $this->info('Tidak ada order overdue.');
        } else {
            $this->info("Diproses {$processed->count()} order: #".$processed->pluck('id')->implode(', #'));
        }

        return self::SUCCESS;
    }
}
