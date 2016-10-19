<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
class SendWeeklyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:weekly:report';

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
        $today = new \DateTime('today');
        $fromDate = new \DateTime("-7 days");

        $data = DB::table('menus_users')
            ->whereDate('menus_users.delivery_date', '>=', $fromDate->format('Y-m-d'))
            ->whereDate('menus_users.delivery_date', '<=', $today->format('Y-m-d'))
            ->get();

var_dump($data);
    }
}
