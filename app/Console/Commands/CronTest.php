<?php

namespace App\Console\Commands;

use App\AC_Mediator;
use App\User;
use Illuminate\Console\Command;

class CronTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $ac = AC_Mediator::GetInstance();
        $ac->AddCustomerTag(User::where('email', 'azagarov@mail.ru')->first(), 'Cancellation');
        $this->comment("OK");
    }
}
