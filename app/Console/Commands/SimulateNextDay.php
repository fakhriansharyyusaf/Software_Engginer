<?php

namespace App\Console\Commands;

use App\Services\TimeService;
use Illuminate\Console\Command;

/**
 * php artisan seapedia:simulate-next-day
 */
class SimulateNextDay extends Command
{
    protected $signature = 'seapedia:simulate-next-day';

    protected $description = 'Advance SEAPEDIA\'s simulated clock by one day (for demoing SLA/overdue behavior).';

    public function handle(): int
    {
        $offset = TimeService::simulateNextDay();
        $this->info("Waktu simulasi dimajukan. Offset: {$offset} hari. Sekarang: ".TimeService::now()->toDateTimeString());

        return self::SUCCESS;
    }
}
