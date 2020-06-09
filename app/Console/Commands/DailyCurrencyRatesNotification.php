<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DailyCurrencyRatesNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily currency rates notification';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Log::info('Notify users');
    }
}
