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
    protected $signature = 'renewal:reminder {date?} {now?}';

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

//var_dump($renewalDate);die();
        $ac = AC_Mediator::GetInstance();

        User::where('password', '<>', '')
            ->where('start_date', '<>', '')
            ->chunk(20, function($users) use($ac, $renewalDate, $now) {
            foreach($users as $user) {
echo "#{$user->id} {$user->name} {$user->email} processing started ...\r\n";

                $userSubscription = UserSubscription::where('user_id',$user->id)
                    ->where('status', '=', 'active')
                    ->first();
                if(!$userSubscription) {
echo "SKIP. No subscription.\r\n\r\n";
                    continue;
                }

                $nextDeliveryDate = $ac->GetNextDeliveryDate($user, $now);
                if(!$nextDeliveryDate) {
echo "SKIP. No next delivery date.\r\n\r\n";
                    continue;
                }
                if(new \DateTime($nextDeliveryDate) < new \DateTime($user->start_date)) {
echo "SKIP. Starts later.\r\n\r\n";
                    continue;
                }

                $ac->UpdateRenewalDate($user, $renewalDate, $now);
echo "DONE. Continue processing.\r\n\r\n";

            }
        });

//        foreach(User::all()->cursor() as $user) {
//            var_dump($user);
//        }

//        User::chu
        $this->comment("HEY");
    }
}
