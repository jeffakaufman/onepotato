<?php

namespace App\Console\Commands;

use App\AC_Mediator;
use App\Cancellation;
use App\Events\UserHasRegistered;
use App\Menu;
use App\MenuAssigner;
use App\Product;
use App\Referral;
use App\ReferralManager;
use App\StripeMediator;
use App\UserSubscription;
use Faker\Provider\DateTime;
use Illuminate\Console\Command;
use App\User;
use App\MenusUsers;

use Illuminate\Support\Facades\Mail;

use DB;

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

        $stripe = StripeMediator::GetInstance();


        foreach(User::whereIn('status', [User::STATUS_ACTIVE, User::STATUS_INACTIVE])->/*where('id', '>=', 3615)->*/get() as $u) {
            $this->comment("#{$u->id} [{$u->email}] {$u->first_name}");
            $subscription = UserSubscription::GetByUserId($u->id);
            if($subscription) {
                $product = Product::find($subscription->product_id);
                if($product) {
                    $this->comment("    {$subscription->stripe_id} DB Plan :: {$product->stripe_plan_id}");
                    try {
                        $ss = $stripe->RetrieveSubscription($subscription->stripe_id);
                        $this->comment("    Stripe Plan :: {$ss->plan->id}");

                        if($ss->plan->id == $product->stripe_plan_id) {
                            $this->comment('    OK!!!');
                        } else {
                            $this->error('    MISMATCH!!!');
                        }
                    } catch (\Exception $e) {
                        $this->error("    {$e->getMessage()}");
                    }
                } else {
                    $this->warn("WARN::Product not found");
                }
            } else {
                $this->warn("WARN::Subscription not found");
            }
        }
return;
        var_dump(ReferralManager::CreateShareLink(User::where('email', 'agedgouda@gmail.com')->first()));

        return;

//        $ac = AC_Mediator::GetInstance();
//        $ac->MenuShipped(User::where('email', 'agedgouda@gmail.com')->first(), "TEST_TRACKING_NUMBER");
//return;


        $ac = AC_Mediator::GetInstance();

        $referrals = Referral::where('id', '>=', 81)
            ->where('id', '<=', 86)
            ->get();

        foreach($referrals as $referral) {
            $user = User::find($referral->referrer_user_id);
            $ac->AddReferralUser($user, $referral);
        }
return;
        $users = User::all();

        foreach($users as $u) {
            /**
             * @var User $u
             */

            switch($u->status) {
                case User::STATUS_INCOMPLETE:
                case User::STATUS_ADMIN:
                case User::STATUS_INACTIVE_CANCELLED:
                    continue;
                    break;
            }

            $sub = UserSubscription::where('user_id', $u->id)->first();
            if(!$sub) {
                continue;
            }

            $p = Product::find($sub->product_id);
            /**
             * @var Product $p
             */
            if(!$p){
                continue;
            }

//            $this->comment("#{$u->id} {$u->email} {$u->name}");

            $logged = false;
            $supposed = MenuAssigner::GetAllFutureMenusForUser($u);

            foreach($supposed as $date => $dMenus) {

//                if($date != '2016-10-18') continue;

                $menus = MenusUsers::where('users_id', $u->id)
                ->where('delivery_date', $date)
                    ->get();

                $rids = [];
                $ridsT = [];
                foreach($menus as $_m) {
                    $menu = Menu::find($_m->menus_id);
                    /**
                     * @var Menu $menu
                     */
                    $rids[] = $_m->menus_id;
                    $ridsT[] = $_m->menus_id."(".($menu->isVegetarian && $menu->isOmnivore ? 'B' : ($menu->isOmnivore ? 'O' : 'V')).")";
                }

                sort($rids);

                $idsT = "Real : [".implode(',', $ridsT)."]";

                $tids = [];
                $tidsT = [];

                $_rep = '';

                foreach($dMenus as $_m) {
                    $tids[] = $_m->id;

                    if(count($_m->replacements)) {
                        $_rep .= " Replacements done :: ";
                        foreach($_m->replacements as $_r) {
                            $_rep .= sprintf("%06b ", $_r);
                        }
                    }
                    $tidsT[] = $_m->id."(".($_m->isVegetarian && $_m->isOmnivore ? 'B' : ($_m->isOmnivore ? 'O' : 'V')).")";
                }
                sort($tids);

                $idsT .= "      Theo : [".implode(',', $tidsT)."]".$_rep;

                if(true || $rids != $tids) {
                    if(!$logged) {
                        $this->comment("#{$u->id} {$u->email} {$u->name} ".($p->IsOmnivore() ? "OMNI" : "VEG")."          ".$sub->dietary_preferences);
                        $logged = true;
                    }
                    $this->comment("{$date} {$idsT}");
                }

//                var_dump($menus);
            }

            if($logged) {
                $this->comment('-----------------------------------------------------------------------------------------');
            }
//            $udp = $sub->getOriginal('dietary_preferences');

//            $m = $whatscooking->menus()->get();
        }

return;

        //Somehow user id 2384 was assigned menus.id 64, which has beef although they don’t have beef as a dietary preference.




        $user = User::find(2384);

        $menus = MenuAssigner::GetAllFutureMenusForUser($user);

var_dump($menus);
return;
        $ac = AC_Mediator::GetInstance();
        $ac->MenuShipped(User::where('email', 'agedgouda@gmail.com')->first(), "D10010997424018");
//        $ac->MenuShipped(User::where('email', 'roshannabaron@yahoo.com')->first(), "TEST_TRACKING_NUMBER");

return;
        $emailsList = array(
            '2nadarskis@cox.net',
            'alecrucci@hotmail.com',
            'aliwoodsrn@gmail.com',
            'amyamsterdam@gmail.com',
            'carley.preble@gmail.com',
            'chloelmyers@yahoo.com',
            'ejcavalcanti@yahoo.com',
            'hali.pickett@gmail.com',
            'hollysoliday@gmail.com',
            'hstobo2@gmail.com',
            'ilanit927@gmail.com',
            'jenniferkconnolly@gmail.com',
            'jes.leggett@gmail.com',
            'jessicakwatts@gmail.com',
            'kacollins2@att.net',
            'kelly.j.mclaughlin@gmail.com',
            'ksbokenkamp@yahoo.com',
            'lafamiliaburke@gmail.com',
            'laura.mitchelle27@gmail.com',
            'lauraclark@ymail.com',
            'lilmommagould@hotmail.com',
            'marc.berkman@gmail.com',
            'mis1217@sbcglobal.net',
            'missbee1234@gmail.com',
            'mparlapanides@gmail.com',
            'tami@skookumh2o.com',
            'tmrado@yahoo.com',
            'tonjatav@hotmail.com',
            'weetchey@yahoo.com',
            'wendybabramson@yahoo.com',
        );

        $insertArray = [];

        foreach($emailsList as $email) {
            echo "Start processing {$email}:\r\n";

            $user = User::where('email', $email)->first();

            if(!$user) {
                echo "Not found!!!\r\n\r\n";
                continue;
            }
            echo "Current Start Date : {$user->start_date} \r\n";

            $menus = MenuAssigner::GetAllFutureMenusForUser($user);
            foreach($menus as $date => $dMenus) {
                foreach($dMenus as $m) {
                    echo "{$date}: {$m->menu_title} {$m->menu_description}\r\n";
                }
            }

            echo "Setting start_date to 10/04/2016\r\n";
            $user->start_date = "2016-10-04";
            $user->save();

            DB::table('menus_users')->where('users_id', '=', $user->id)->delete();

            $menus = MenuAssigner::GetAllFutureMenusForUser($user);
            foreach($menus as $date => $dMenus) {
                foreach($dMenus as $m) {
                    echo "{$date}: {$m->menu_title} {$m->menu_description}\r\n";

                    $insertArray[] = [
                        'menus_id' => $m->id,
                        'users_id' => $user->id,
                        'delivery_date' => $date,
                    ];

                }
            }

            echo "\r\n";
        }
        DB::table("menus_users")->insert($insertArray);

return;
        $shipDay = 'tuesday';
        $dayLimit = 'wednesday';
        $timeLimit = '9:00';

        $now = new \DateTime('now');
        $today = new \DateTime('today');



//        $dayLimit = 'wednesday';
//        $timeLimit = '9:00';

//        $dayLimit = 'wednesday';
//        $dayLimit = 'saturday';
//        $timeLimit = '5:42';


        $theDay = new \DateTime($dayLimit);
        $limit = new \DateTime("{$dayLimit} {$timeLimit}");

        if(($today == $theDay) && ($now > $limit)) {
            $limit->modify("+1 week");
        }

        $firstDate = (clone $limit)->modify("next {$shipDay}");
        $lastDate = (new \DateTime("next {$shipDay}"))->modify("+6 weeks");

//        var_dump($now);
//        var_dump($today);
//        var_dump($theDay);
//        var_dump($limit);
//        if($now > $limit) {
//            $firstDate->modify("+1 week");
//        }

//var_dump($now);
//var_dump($limit);

echo $now->format("m/d/Y H:i:s")."\r\n";
echo $limit->format("m/d/Y H:i:s")."\r\n";
echo $firstDate->format("m/d/Y")." - ".$lastDate->format("m/d/Y")."\r\n\r\n";
        //Before 9AM on Wednesday, the start date should be the next Tuesday.
        // After 9AM on wednesday, it should be a week from that Tuesday.
        // For example, if you sign up on 9/21 before 9AM Pacific. the earliest start date should be 9/27.
        // If you sign up after 9:00AM it should be 10/4.
        // I can’ t get the date to work right, can you fix my mess?


return;

        $user = User::where('email', 'agedgouda@gmail.com')->first();
//        $user = User::where('email', 'ahhmed@mail.ru')->first();

        echo Cancellation::GenerateCancelLink($user);

return;

//        event(new UserHasRegistered(User::where('email', 'agedgouda@gmail.com')->first()));
//        event(new UserHasRegistered(User::where('email', 'jclmeek@gmail.com')->first()));
//        event(new UserHasRegistered(User::where('email', 'vweber871@sbcglobal.net')->first()));
//        event(new UserHasRegistered(User::where('email', 'tess.rosenfeld@gmail.com')->first()));
//        event(new UserHasRegistered(User::where('email', 'karen.schach@gmail.com')->first()));
//        event(new UserHasRegistered(User::where('email', 'sarahstrayer@me.com')->first()));
return;
        $orderDate = new \DateTime('-1 day');
        $params = [
            'orderId' => '345346456',
            'orderDate' => $orderDate->format('j D, Y'),
            'lines' => [
                (object)[
                    'name' => 'The line',
                    'qty' => 1,
                    'price' => '45.56',
                ],
            ],

            'subtotal' => '45.56',
            'shipping' => '0.00',
            'tax' => '0.00',
            'total' => '45.56',

            'name' => 'Alexey Zagarov',
            'address' => '30th Dianova st. apt. 137',
            'city' => 'Omsk',
            'state' => 'CA',
            'zip' => '95678',
            'country' => 'US',
        ];

        $r = Mail::send('emails.order_details', $params, function($m) {
            $m->from('ahhmed@mail.ru', 'Aleksey Zagarov');
            $m->to('ahhmed@mail.ru', 'Super User')->subject('Test Message');
        });

var_dump($r);

//        event(new UserHasRegistered(User::where('email', 'ira.napoliello@gmail.com')->first()));
//        event(new UserHasRegistered(User::where('email', 'agedgouda@gmail.com')->first()));

//        $ac = AC_Mediator::GetInstance();
//        $ac->PaymentFailed(User::where('email', 'ahhmed@mail.ru')->first());
//        $ac->UpdateRenewalDate(User::where('email', 'ahhmed@mail.ru')->first(), new \DateTime("+3 days"), "+5 days");

//        $ac->TestLog();
    }
}
