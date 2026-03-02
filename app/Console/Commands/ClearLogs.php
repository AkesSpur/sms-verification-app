<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearLogs extends Command
{
    protected $signature = 'logs:clear
                            {--keep-days=0 : Keep log files modified within this many days (0 = clear all)}';

    protected $description = 'Clear all application log files in the storage/logs directory';

    public function handle(): int
    {
        $keepDays = (int) $this->option('keep-days');
        $logDir   = storage_path('logs');

        if (!is_dir($logDir)) {
            $this->warn("Log directory does not exist: {$logDir}");
            return Command::SUCCESS;
        }

        $files   = glob($logDir . '/*.log') ?: [];
        $cleared = 0;
        $skipped = 0;

        foreach ($files as $path) {
            if ($keepDays > 0 && filemtime($path) >= strtotime("-{$keepDays} days")) {
                $this->line("Skipped (recent): " . basename($path));
                $skipped++;
                continue;
            }

            file_put_contents($path, '');
            $this->line("Cleared: " . basename($path));
            $cleared++;
        }

        $this->info("Done. Cleared: {$cleared}, Skipped: {$skipped}.");

        return Command::SUCCESS;
    }
}
