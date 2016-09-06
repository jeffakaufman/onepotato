<?php

namespace App\Console\Commands;

use App\AC_Mediator;
use App\Events\UserHasRegistered;
use Illuminate\Console\Command;
use App\User;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:command';

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
//        event(new UserHasRegistered(User::where('email', 'ahhmed@mail.ru')->first()));

        $ac = AC_Mediator::GetInstance();
//        $ac->PaymentFailed(User::where('email', 'ahhmed@mail.ru')->first());
//        $ac->UpdateRenewalDate(User::where('email', 'ahhmed@mail.ru')->first(), new \DateTime("+3 days"), "+5 days");

        $ac->TestLog();
    }
}
