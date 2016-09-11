<?php

namespace App\Console\Commands;

use App\AC_Mediator;
use App\Events\UserHasRegistered;
use Faker\Provider\DateTime;
use Illuminate\Console\Command;
use App\User;

use Illuminate\Support\Facades\Mail;

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
//        event(new UserHasRegistered(User::where('email', 'agedgouda@gmail.com')->first()));
        event(new UserHasRegistered(User::where('email', 'jclmeek@gmail.com')->first()));
        event(new UserHasRegistered(User::where('email', 'vweber871@sbcglobal.net')->first()));
        event(new UserHasRegistered(User::where('email', 'tess.rosenfeld@gmail.com')->first()));
        event(new UserHasRegistered(User::where('email', 'karen.schach@gmail.com')->first()));
        event(new UserHasRegistered(User::where('email', 'sarahstrayer@me.com')->first()));
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
