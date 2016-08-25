<?php

namespace App\Console\Commands;

use App\AC_Mediator;
use App\User;
use Illuminate\Console\Command;

class RenewalReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'renewal:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Renewal Reminder';

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

        $renewalDate = new \DateTime("next Wednesday");
//var_dump($renewalDate);die();
        $ac = AC_Mediator::GetInstance();

        User::where('password', '<>', '')->chunk(20, function($users) use($ac, $renewalDate) {
            foreach($users as $user) {
                $ac->UpdateRenewalDate($user, $renewalDate);
            }
        });

//        foreach(User::all()->cursor() as $user) {
//            var_dump($user);
//        }

//        User::chu
        $this->comment("HEY");
    }
}