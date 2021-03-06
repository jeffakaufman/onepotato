<?php

namespace App\Console\Commands;

use App\AC_Mediator;
use App\ZipcodeStates;
use Illuminate\Console\Command;

use App\User;
class CheckAbandoned extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:abandoned';

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

    const ABANDONED_TAG = "AbandonedCart";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $highLimit = new \DateTime('-1 hour');
        $lowLimit = new \DateTime("-24 hours");

        $users = User::where('status', User::STATUS_INCOMPLETE)
//            ->where('email', 'azagarov@mail.ru') //TODO: remove this testing filter
            ->where('updated_at', '<', $highLimit->format('Y-m-d H:i:s'))
            ->where('updated_at', '>', $lowLimit->format('Y-m-d H:i:s'))
            ->get();

        $ac = AC_Mediator::GetInstance();

        $this->comment("Users to process :: ".count($users));

/*

1. customer is not existing in AC - add and add the tag, so run automation
2. customer is in AC system but within waiting list - ignore
3. customer is in AC system but within other list(s) — I need an answer from CJ
4. customer is in AC system AND tag is already set - ignore

AbandonedCart
 */

        foreach($users as $u) {
            /**
             * @var User $u
             */

            $this->comment($u->email . ' '.($u->billing_zip ? $u->billing_zip : "no_zip"));

            $zipCode = $u->billing_zip;

            try {
                $acUser = $ac->GetCustomerData($u);
            } catch (\Exception $e) {
                $this->comment($e->getMessage());
                continue;
            }

            $action = "add_user";

            if($acUser->success) { //AC record found
                $this->comment("FOUND!!!");

                $tagIsAlreadySet = in_array(self::ABANDONED_TAG, (array)$acUser->tags);

                if($tagIsAlreadySet) {
                    $this->comment("The Tag is already set :: Skipping");
                    $action = "skip";
                } else {
                    $inWaitingList = false;
                    $inActiveList = false;

                    if(!$zipCode) {
                        foreach((array)$acUser->fields as $fId => $f) {
                            switch($f->perstag) {
                                case 'ZIP_CODE':
                                    $zipCode = $f->val;
                                    break;

                                default:
                                    //Do Nothing
                                    break;
                            }
                        }
                        $this->comment("AC ZipCode = {$zipCode}");
                    }
                    foreach((array)$acUser->lists as $listId => $list) {
                        switch ($listId) {
                            case AC_Mediator::LIST_Waiting_List:
                                $inWaitingList = true;
                                break;

                            case AC_Mediator::LIST_One_Potato_Subscribers:
                                $inActiveList = true;
                                break;

                            default:
                                // Do nothing
                                break;
                        }
                    }

                    if($inActiveList) {
                        $this->comment("Is in Active List :: Skipping");
                        $action = "skip";
                    } else {

                        if($zipCode) {
                            $zipRow = ZipcodeStates::where('zipcode', $zipCode)->first();
                            if($zipRow) {
                                $this->comment("Zip is in the sending area :: Adding Tag");
                                $action = "add_tag";
                            } else {
                                $this->comment("Zip is out of the sending area :: Skipping");
                                $action = "skip";
                            }
                        } else {
                            $this->comment("No Zip Code :: Adding Tag");
                            $action = "add_tag";
                        }
                    }


//                    $this->comment($inWaitingList ? "Is in waiting List" : "Not in Waiting List");

                }
            }

            $this->comment("Action to do: ".$action);

            switch($action) {
                case 'add_tag':
                    try {
                        $ac->AddCustomerTag($u, self::ABANDONED_TAG);
                        $this->comment("Tag added");
                    } catch (\Exception $e) {
                        $this->warn($e->getMessage());
                    }
                    break;

                case 'add_user':
                    try {
                        $ac->AddUser($u, [], [], [self::ABANDONED_TAG]);
                        $this->comment("The user added");
                    } catch (\Exception $e) {
                        $this->warn($e->getMessage());
                    }
                    break;

                case 'skip':
                default:
                    continue;
                    break;
            }

            $this->comment("--------------------------------------------");
//            var_dump($acUser);
//            print_r($acUser);
//            echo "{$u->id} {$u->email} {$u->status} {$u->updated_at}\r\n";
        }
    }
}
