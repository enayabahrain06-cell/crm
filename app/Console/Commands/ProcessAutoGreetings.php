<?php

namespace App\Console\Commands;

use App\Services\AutoGreetingService;
use Illuminate\Console\Command;

class ProcessAutoGreetings extends Command
{
    protected $signature = 'auto-greetings:process {--dry-run : Run without actually sending}';
    protected $description = 'Process active auto-greeting rules and send greetings';

    public function __construct(
        protected AutoGreetingService $autoGreetingService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        if ($this->option('dry-run')) {
            $this->info('Dry run - no greetings will be sent.');
        }

        $results = $this->autoGreetingService->processActiveRules(null);

        $this->info("Processed: {$results['processed']} rules");
        $this->info("Sent: {$results['sent']}");
        $this->info("Failed: {$results['failed']}");

        if (!empty($results['errors'])) {
            $this->warn('Errors:');
            foreach ($results['errors'] as $error) {
                $this->error(json_encode($error));
            }
        }

        return $results['failed'] > 0 ? 1 : 0;
    }
}

