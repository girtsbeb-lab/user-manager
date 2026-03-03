<?php

namespace App\Console\Commands;

use App\Services\RandomUserService;
use Illuminate\Console\Command;

class ImportUsers extends Command
{
    protected $signature = 'users:import
                            {--results=50 : Number of users to import}
                            {--truncate   : Truncate existing data before import}';

    protected $description = 'Import users from RandomUser API into the database';

    public function __construct(private RandomUserService $service)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $results  = (int) $this->option('results');
        $truncate = (bool) $this->option('truncate');

        if ($truncate && !$this->confirm('This will DELETE all existing user data. Continue?', false)) {
            $this->info('Import cancelled.');
            return self::SUCCESS;
        }

        $this->info("Importing {$results} users from RandomUser API...");

        $bar = $this->output->createProgressBar(1);
        $bar->start();

        try {
            $stats = $this->service->import($results, $truncate);
            $bar->finish();
            $this->newLine(2);
            $this->table(
                ['Imported', 'Updated', 'Errors'],
                [[$stats['imported'], $stats['updated'], $stats['errors']]]
            );
            $this->info('✓ Import completed successfully!');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $bar->finish();
            $this->newLine();
            $this->error('Import failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
