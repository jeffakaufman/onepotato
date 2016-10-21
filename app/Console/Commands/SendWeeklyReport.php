<?php

namespace App\Console\Commands;

use App\Product;
use App\ProductSku;
use App\UserSubscription;
use Illuminate\Console\Command;
use DB;
use Mail;
use App\Menu;

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
        $toDate = new \DateTime('+7 days');
        $fromDate = new \DateTime("today");

//        $today = new \DateTime('-7 days');
//        $fromDate = new \DateTime("-14 days");

        $dbData = DB::table('menus_users')
            ->whereDate('menus_users.delivery_date', '>=', $fromDate->format('Y-m-d'))
            ->whereDate('menus_users.delivery_date', '<=', $toDate->format('Y-m-d'))
            ->get();

        $byUserData = array();

        foreach($dbData as $dbRow) {
            $byUserData[$dbRow->users_id]['menuIds'][] = $dbRow->menus_id;
        }

        foreach($byUserData as &$_x) {
            sort($_x['menuIds']);
            $_x["hash"] = implode(',', $_x['menuIds']);

            foreach($_x['menuIds'] as $mId) {
                $_x['menus'][$mId] = $this->_getMenu($mId);
            }

            $_x['bigGroup'] = $this->_getBigGroup($_x);
//            $_x['groupTitle'] = $this->_getGroupTitle($_x);
        }


        $reportData = [];
        $reportData['bigGroups'] = [];
        $reportData['total'] = ['count' => 0];
        foreach($byUserData as $userId => $buRow) {
            if(!isset($reportData['bigGroups'][$buRow['bigGroup']])) {
                $bigGroup = [
                    'code' => $buRow['bigGroup'],
                    'name' => $this->_getBigGroupName($buRow['bigGroup']),
                    'groups' => [],
                ];
                $reportData['bigGroups'][$buRow['bigGroup']] = $bigGroup;
            }

            $bgPtr = &$reportData['bigGroups'][$buRow['bigGroup']];

            if(!isset($bgPtr['groups'][$buRow['hash']])) {
                $group = [
                    'code' => $buRow['hash'],
                    'name' => $this->_getGroupTitle($buRow),
                    'products' => [],
                ];

                $bgPtr['groups'][$buRow['hash']] = $group;
            }

            $gPtr = &$bgPtr['groups'][$buRow['hash']];

            $subscription = UserSubscription::GetByUserId($userId);
            $product = Product::find($subscription->product_id);

            $sku = ProductSku::BuildByText($product->sku);

            $productHash = $sku->GetNumAdults()."-".$sku->GetNumChildren()."-".(int)$sku->IsGlutenFree();

            if(!isset($gPtr['products'][$productHash])) {
                $gPtr['products'][$productHash] = [
                    'sku' => $product->sku,
                    'name' => $this->_getProductCaption($sku),
                    'count' => 0,
                ];
            }

            $pPtr = &$gPtr['products'][$productHash];
            ++$pPtr['count'];
            ++$reportData['total']['count'];
        }


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

        Mail::send('emails.weekly_report', [], function($message) use($csv){
            $message->to('ahhmed@mail.ru', "Aleksey Zagarov");
            $message->to('agedgouda@gmail.com', "Jeff Kauffman");
//            $message->to('chris@onepotato.com', "Chris Heyman");
//            $message->to('jenna@onepotato.com', "Jenna Stein");

            $message->subject("One Potato Weekly Report");
            $message->attachData($csv, "report.csv");
        });
    }

    private function _getProductCaption(ProductSku $sku) {
        $caption = "{$sku->GetNumAdults()} Adults";
        if($sku->GetNumChildren() > 0) {
            $caption .= " and {$sku->GetNumChildren()} Child".($sku->GetNumChildren() > 1 ? 'ren' : '');
        }

        if($sku->IsGlutenFree()) {
            $caption .= " - Gluten Free";
        }

        return $caption;
    }

    private function _getBigGroupName($code) {
        $name = "UNKNOWN";

        switch($code) {
            case 'otherOmnivore':
                $name = 'Other Omnivore Boxes';
                break;
            case 'standardOmnivore':
                $name = 'Standard Omnivore';
                break;
            case 'standardVegetarian':
                $name = 'Standard Vegetarian';
                break;
        }

        return $name;
    }

    private function _getGroupTitle($data) {
        if(!isset($this->_groupTitleCache[$data['hash']])) {
            $menuNames = [];
            foreach($data['menus'] as $m) {
                $menuNames[] = $m->menu_title;
            }

            $this->_groupTitleCache[$data['hash']] = implode(",", $menuNames);
        }

        return $this->_groupTitleCache[$data['hash']];
    }

    private $_groupTitleCache = [];

    private function _getMenu($id) {
        if(!isset($this->_menuCache[$id])) {
            $this->_menuCache[$id] = Menu::find($id);
        }
        return $this->_menuCache[$id];
    }

    private function _getBigGroup($data) {
        if(!isset($this->_bigGroupCache[$data['hash']])) {
            $code = '';
            foreach($data['menus'] as $m) {
                if($m->isVegetarian && $m->isOmnivore) {
                    $code .= 'B';
                } elseif ($m->isOmnivore) {
                    $code .= 'O';
                } else {
                    $code .= 'V';
                }
            }

            $bigGroup = 'otherOmnivore';
            switch($code) {
                case 'OOO':
                case 'OOB':
                case 'OBO':
                case 'BOO':
                case 'OBB':
                case 'BOB':
                case 'BBO':
                case 'BBB':
                    $bigGroup = 'standardOmnivore';
                    break;

                case 'VVV':
                case 'VVB':
                case 'VBV':
                case 'BVV':
                case 'VBB':
                case 'BVB':
                case 'BBV':
                    $bigGroup = 'standardVegetarian';
                    break;
            }

//var_dump($code);
//var_dump('==================================');
            $this->_bigGroupCache[$data['hash']] = $bigGroup;
        }
        return $this->_bigGroupCache[$data['hash']];
    }

    private $_bigGroupCache = [];

    private $_menuCache = [];
}
