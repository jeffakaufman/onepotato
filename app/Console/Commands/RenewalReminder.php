<?php

namespace App\Console\Commands;

use App\AC_Mediator;
use App\User;
use App\UserSubscription;
use Faker\Provider\DateTime;
use Illuminate\Console\Command;

class RenewalReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'renewal:reminder {date?} {now?} {only?}';

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

        if($this->argument('date')) {
            list($_dummy, $nextDeliveryDate) = explode('=', $this->argument('date'), 2);
            try {
                $renewalDate = new \DateTime($nextDeliveryDate);
            } catch (\Exception $e) {
                echo "Wrong Date";
                return;
            }
        } else {
            $renewalDate = new \DateTime("next Wednesday");
        }

        if($this->argument('now')) {
            list($_dummy, $now) = explode('=', $this->argument('now'), 2);
        } else {
            $now = 'now';
        }

        if($this->argument('only')) {
            list($_dummy, $only) = explode('=', $this->argument('only'), 2);
        } else {
            $now = false;
        }

//var_dump($renewalDate);die();
        $ac = AC_Mediator::GetInstance();

        $condition = User::where('password', '<>', '')
            ->where('start_date', '<>', '');

        if($only) {
            $condition->where('email', '=', $only);
        }

        $condition->chunk(20, function($users) use($ac, $renewalDate, $now) {
            foreach($users as $user) {

                $this->comment("#{$user->id} {$user->name} {$user->email} processing started ...\r\n");

                $userSubscription = UserSubscription::where('user_id',$user->id)
                    ->where('status', '=', 'active')
                    ->first();
                if(!$userSubscription) {
                    $this->comment("SKIP. No subscription.\r\n\r\n");
                    continue;
                }

                $nextDeliveryDate = $ac->GetNextDeliveryDate($user, $now);
                if(!$nextDeliveryDate) {
                    $this->comment("SKIP. No next delivery date.\r\n\r\n");
                    continue;
                }
                if(new \DateTime($nextDeliveryDate) < new \DateTime($user->start_date)) {
                    $this->comment("SKIP. Starts later.\r\n\r\n");
                    continue;
                }

                try {
                    $ac->UpdateRenewalDate($user, $renewalDate, $now);
                    $this->comment("DONE. Continue processing.\r\n\r\n");
                } catch (\Exception $e) {
                    $this->comment("FAILED. ".$e->getMessage()."\r\n\r\n");
                }

            }
        });

//        foreach(User::all()->cursor() as $user) {
//            var_dump($user);
//        }

    }
}
