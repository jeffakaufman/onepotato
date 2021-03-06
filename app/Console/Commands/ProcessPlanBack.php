<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Plan_change;
use App\User;
use App\SubscriptionManager;

//Is supposed to be run @noon by thursdays (fridays for example)


class ProcessPlanBack extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plan:back';

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
        date_default_timezone_set("America/Los_Angeles");

        $dateToProcess = new \DateTime("tuesday");
//        $dateToProcess = new \DateTime("last tuesday");

        $plan_changes = Plan_change::where('status', Plan_change::STATUS_WAS_CHANGED)
            ->where('date_to_change', '<=', $dateToProcess->format('Y-m-d'))
            ->get();

        $this->comment("Start processing for {$dateToProcess->format('m/d/Y')}... ".count($plan_changes)." to process ...");

        foreach($plan_changes as $pCh) {
            $user = User::find($pCh->user_id);

            $this->comment($user->email." change back to {$pCh->old_sku} from {$pCh->sku_to_change}");
            try {
//                $this->comment("    Test Skipping.");
//                continue;
                SubscriptionManager::ProcessPlanChangeBack($user, $pCh);
                $pCh->status = Plan_change::STATUS_INACTIVE;
                $pCh->save();

                $this->comment("    OK.");
            } catch(\Exception $e) {
                $this->warn("    WARN:".$e->getMessage());
            }
        }

        $this->comment("Done.");
    }
}
