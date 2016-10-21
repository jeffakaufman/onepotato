<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mail;
use App\ReportsBuilder;


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
        $fromDate = new \DateTime("today");
        $toDate = new \DateTime('+7 days');

//        $today = new \DateTime('-7 days');
//        $fromDate = new \DateTime("-14 days");


        $reportBuilder = new ReportsBuilder();
        $reportData = $reportBuilder->GetWeeklyKitchenReport($fromDate, $toDate);



        $csv = '';

        foreach($reportData['bigGroups'] as $bgData) {
            $csv .= "\"".$bgData['name']."\"\r\n";
            foreach($bgData['groups'] as $gData) {
                $csv .= "\"".$gData['name']."\"\r\n";
                foreach($gData['products'] as $pData) {
                    $csv .= "\"".$pData['name']."\",".$pData['count']."\r\n";
                }
            }
        }
        $csv .= ",".$reportData['total']['count']."\r\n";

echo $csv;
/*
        Mail::send('emails.weekly_report', [], function($message) use($csv){
            $message->to('ahhmed@mail.ru', "Aleksey Zagarov");
            $message->to('agedgouda@gmail.com', "Jeff Kauffman");
//            $message->to('chris@onepotato.com', "Chris Heyman");
//            $message->to('jenna@onepotato.com', "Jenna Stein");

            $message->subject("One Potato Weekly Report");
            $message->attachData($csv, "report.csv");
        });
*/
    }

}
