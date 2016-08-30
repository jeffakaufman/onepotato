<?php
/**
 * Created by PhpStorm.
 * User: aleksey
 * Date: 19/08/16
 * Time: 12:59
 */

namespace App;

use Exception;
use ActiveCampaign;

class AC_Mediator {

    const LIST_Welcome_To_One_Potato = 3;
    const LIST_Pre_Launch = 1;
    const LIST_One_Potato_Subscribers = 2;
    const LIST_Note_On_Fried_Chicken = 7;
    const LIST_Menu_Change = 4;

    public function UpdateRenewalDate(User $user, \DateTime $renewalDate) {
        $userSubscription = UserSubscription::where('user_id',$user->id)->first();

        if(!$userSubscription) {
            return false;
        }

        try {
            $ac = $this->_getConnection();
        } catch (Exception $e) {
            throw new Exception("Active Campaign Connection Error");
        }

        $contact = array(
            "email" => $user->email,
            "field[%RENEWAL_DATE%]" => $this->_formatDate($renewalDate),
        );

        $contact = array_merge(
            $contact,
            $this->_getNextDeliveryData($user)
        );


        $contact_sync = $ac->api("contact/sync", $contact);
        return $contact_sync;

    }

    public function GetCustomerData(User $user) {

        try {
            $ac = $this->_getConnection();
        } catch (Exception $e) {
            throw new Exception("Active Campaign Connection Error");
        }
        $params = [
            'email' => $user->email,
        ];

//var_dump($params);
        return $ac->api("contact/view?email={$user->email}");
    }

    public function Unsubscribe(User $user, $list) {
        try {
            $ac = $this->_getConnection();
        } catch (Exception $e) {
            throw new Exception("Active Campaign Connection Error");
        }

        $contact = array(
            "email" => $user->email,
        );
        foreach($list as $listId) {
            $contact["p[{$listId}]"] = $listId;
            $contact["status[{$listId}]"] = 2;
        }
//var_dump($contact);die();
        $contact_sync = $ac->api("contact/sync", $contact);

        return $contact_sync;

    }


    public function UpdateCustomerData(User $user, $listsToAdd = [], $listsToRemove = []) {
        $userSubscription = UserSubscription::where('user_id',$user->id)->first();

        if(!$userSubscription) {
            return false;
        }

        $product = Product::find($userSubscription->product_id);
        if(!$product) {
            return false;
        }

        try {
            $ac = $this->_getConnection();
        } catch (Exception $e) {
            throw new Exception("Active Campaign Connection Error");
        }

//        $listId = self::LIST_Welcome_To_One_Potato;

        $firstName = $user->first_name;
        $lastName = $user->last_name;

        try {
            @list($_firstName, $_lastName) = explode(' ', $user->name, 3);
        } catch (Exception $e) {
            $_firstName = '';
            $_lastName = '';
        }
        $firstName = $firstName ?: $_firstName;
        $lastName = $lastName ?: $_lastName;

        $contact = array(
            "email" => $user->email,
            "first_name" => $firstName,
            "last_name" => $lastName,
            'phone' => $user->phone,
        );

//            "p[{$listId}]" => $listId,
//            "status[{$listId}]" => 1, // "Active" status

        foreach($listsToAdd as $listId) {
            $contact["p[{$listId}]"] = $listId;
            $contact["status[{$listId}]"] = 1;
        }

        foreach($listsToRemove as $listId) {
            $contact["p[{$listId}]"] = $listId;
            $contact["status[{$listId}]"] = 2;
        }

//var_dump($user);die();
        $contact = array_merge(
            $contact,
            $this->_getCustomCustomerFields($user, $userSubscription, $product)
        );

//var_dump($contact);die();
        $contact_sync = $ac->api("contact/sync", $contact);

        return $contact_sync;

        if ((int)$contact_sync->success) {
            // successful request
            $contact_id = (int)$contact_sync->subscriber_id;
//            echo "<p>Contact synced successfully (ID {$contact_id})!</p>";
        } else {
            $contact_id = false;
        }

        return $contact_id;
    }


    private function _getNextDeliveryData(User $user) {
        $today = new \DateTime();
//var_dump($today);
        $nextDeliveryDate = MenusUsers::where('users_id', $user->id)
            ->where('delivery_date', '>', $today->format('Y-m-d'))
            ->min('delivery_date');
//var_dump($nextDeliveryDate);die();

        $nextDelivery = MenusUsers::where('users_id',$user->id)->where('delivery_date',$nextDeliveryDate)->get();
        $meal1 = $nextDelivery[0]->menus_id;
        $meal2 = $nextDelivery[1]->menus_id;
        $meal3 = $nextDelivery[2]->menus_id;

        $menu1 = Menu::find($meal1);
        $menu2 = Menu::find($meal2);
        $menu3 = Menu::find($meal3);

        $arr = array();
        $arr['NEXT_DELIVERY_DATE'] = $this->_formatDate($nextDeliveryDate); //Next Delivery Date	Text Input	%NEXT_DELIVERY_DATE%	Next Delivery Date
        $arr['YOUR_MEAL_IMAGE'] = $menu1 ? $menu1->image : ''; //Your Meal Image	Text Input	%YOUR_MEAL_IMAGE%	URL to image
        $arr['YOUR_MEAL_IMAGE_2'] = $menu2 ? $menu2->image : ''; //Your Meal Image 2	Text Input	%YOUR_MEAL_IMAGE_2%	URL to image 2
        $arr['YOUR_MEAL_IMAGE_3'] = $menu3 ? $menu3->image : ''; //Your Meal Image 3	Text Input	%YOUR_MEAL_IMAGE_3%	URL to image 3
        $arr['YOUR_MEAL_NAME'] = $menu1 ? $menu1->menu_title : ''; //Your Meal Name	Text Input	"	%YOUR_MEAL_NAME%"	Your Meal Name
        $arr['YOUR_MEAL_NAME_2'] = $menu2 ? $menu2->menu_title : ''; //Your Meal Name 2	Text Input	%YOUR_MEAL_NAME_2%
        $arr['YOUR_MEAL_NAME_3'] = $menu3 ? $menu3->menu_title : ''; //        Your Meal Name 3	Text Input	%YOUR_MEAL_NAME_3%
        $arr['DELIVERY_DAY'] = $this->_formatDate($nextDeliveryDate); //        Delivery Day	Text Input	%DELIVERY_DAY%	Delivery Day

        $list = array();
        foreach($arr as $key => $value) {
            $list[urlencode("field[%{$key}%,0]")] = $value;
        }
        return $list;

    }

    /**
     * @param $input \DateTime|string
     * @return string
     */
    private function _formatDate($input) {
        if(!($input instanceof \DateTime)) {
            $input = new \DateTime($input);
        }

        return $input->format('F jS, Y'); //August 26th, 2016
    }


    private function _getCustomCustomerFields(User $user, UserSubscription $userSubscription, Product $product) {

//        $product->
//        $deliveryDate = date('Y-m-d', strtotime($user->start_date));

//        $today = new \DateTime();
//var_dump($today);
//        $nextDeliveryDate = MenusUsers::where('users_id', $user->id)
//            ->where('delivery_date', '>', $today->format('Y-m-d'))
//            ->min('delivery_date');
//var_dump($nextDeliveryDate);die();

//        $nextDelivery = MenusUsers::where('users_id',$user->id)->where('delivery_date',$nextDeliveryDate)->get();
//        $meal1 = $nextDelivery[0]->menus_id;
//        $meal2 = $nextDelivery[1]->menus_id;
//        $meal3 = $nextDelivery[2]->menus_id;

//        $menu1 = Menu::find($meal1);
//        $menu2 = Menu::find($meal2);
//        $menu3 = Menu::find($meal3);

        $productInfo = $product ? $product->productDetails() : new stdClass();
//var_dump($productInfo);
        $arr = array();
//        $arr['NEXT_DELIVERY_DATE'] = $nextDeliveryDate; //              [YES] Next Delivery Date	Text Input	%NEXT_DELIVERY_DATE%	Next Delivery Date
//        $arr['YOUR_MEAL_IMAGE'] = $menu1 ? $menu1->image : ''; //       [YES] Your Meal Image	Text Input	%YOUR_MEAL_IMAGE%	URL to image
//        $arr['YOUR_MEAL_IMAGE_2'] = $menu2 ? $menu2->image : ''; //     [YES] Your Meal Image 2	Text Input	%YOUR_MEAL_IMAGE_2%	URL to image 2
//        $arr['YOUR_MEAL_IMAGE_3'] = $menu3 ? $menu3->image : ''; //     [YES] Your Meal Image 3	Text Input	%YOUR_MEAL_IMAGE_3%	URL to image 3
//        $arr['YOUR_MEAL_NAME'] = $menu1 ? $menu1->menu_title : ''; //   [YES] Your Meal Name	Text Input	%YOUR_MEAL_NAME%	Your Meal Name
//        $arr['YOUR_MEAL_NAME_2'] = $menu2 ? $menu2->menu_title : ''; // [YES] Your Meal Name 2	Text Input	%YOUR_MEAL_NAME_2%
//        $arr['YOUR_MEAL_NAME_3'] = $menu3 ? $menu3->menu_title : ''; // [YES] Your Meal Name 3	Text Input	%YOUR_MEAL_NAME_3%
//        $arr['DELIVERY_DAY'] = $nextDeliveryDate; //                    [YES] Delivery Day	Text Input	%DELIVERY_DAY%	Delivery Day

        $arr['PRODUCT'] = $product ? $product->product_description : ''; //        Product	Text Input	%PRODUCT%	ex: One Potato Box, 2 Adults, 2 Children
        $arr['BOX_TYPE'] = $productInfo->BoxType; //Box Type	Text Input	%BOX_TYPE%

//        $arr['TERM'] = ''; //Term	Text Input	%TERM%
        $arr['PRICE'] = $product ? $product->cost : ''; //        Price	Text Input	%PRICE%
        $arr['STATUS'] = $user->status; //        Status	Text Input	%STATUS%
        $arr['REFERENCE_ID'] = $userSubscription->stripe_id; //        Reference ID	Text Input	%REFERENCE_ID%
//        $arr['DELIVERY_SKIP_DATE'] = ''; //        Delivery Skip Date	Text Input	%DELIVERY_SKIP_DATE%	This should update to blank if they did not skip that week
//        $arr['PAYMENT_FAIL_COUNT'] = ''; //Payment Fail Count	Text Input	%PAYMENT_FAIL_COUNT%	This should update to blank if they make a payment
        // REMOVED        $arr['GIFT_CARD_ISSUED'] = ''; //Gift Card Issued	Text Input	%GIFT_CARD_ISSUED%	Removed on 7/28/16 per conversation this is not good enough for data.
//        $arr['REFERRAL_NAME'] = ''; //Referral Name	Text Input	%REFERRAL_NAME%	Added 7/28/16
//        $arr['REFERRAL_EMAIL'] = ''; //Referral Email	Text Input	%REFERRAL_EMAIL%	Added 7/28/16
//        $arr['GIFT_CARD_AMOUNT'] = ''; //Gift Card Amount	Text Input	%GIFT_CARD_AMOUNT%	Added 7/28/16
//        $arr['GIFT_CARD_RECIPIENT_NAME'] = ''; //Gift Card Recipient Name	Text Input	%GIFT_CARD_RECIPIENT_NAME%	Added 7/28/16
//        $arr['REFERRAL_FEE_AMOUNT'] = ''; //Referral Fee Amount	Text Input	%REFERRAL_FEE_AMOUNT%	Added 7/28/16
//        $arr['GIFT_CARD_PURCHASER_NAME'] = ''; //Gift Card Purchaser Name	Text Input	%GIFT_CARD_PURCHASER_NAME%	Added 7/28/16
//        $arr['PERSONALIZED_GIFT_CARD_LINK'] = ''; //Personalized Gift Card Link	Text Input	%PERSONALIZED_GIFT_CARD_LINK%	Added 7/28/16
//Standard AC Fields
//  NO NEED TO UPDATE              $arr['FIRSTNAME'] = $firstName; //First Name	Text Input	%FIRSTNAME%
//  NO NEED TO UPDATE              $arr['LASTNAME'] = $lastName; //Last Name	Text Input	%LASTNAME%
//  NO NEED TO UPDATE              $arr['EMAIL'] = $user->email; //Email	Text Input	%EMAIL%
//  NO NEED TO UPDATE              $arr['PHONE'] = $user->phone; //Phone	Text Input	%PHONE%
        $arr['ZIP_CODE'] = $user->billing_zip; //Zip Code	Text Input	%ZIP_CODE%
//        $arr['LATITUDE'] = ''; //Latitude	Text Input	%LATITUDE%
//        $arr['LONGITUDE'] = ''; //Longitude	Text Input	%LONGITUDE%
//        $arr['MEMBERRATING'] = ''; //Member_Rating	Text Input	%MEMBERRATING%
//        $arr['CONFIRMTIME'] = ''; //Confirm_Time	Text Input	%CONFIRMTIME%
        $arr['STATE'] = $user->billing_state; //State	Text Input	%STATE%
//        $arr['TIMEZONE'] = ''; //TimeZone	Text Input	%TIMEZONE%
//        $arr['LASTCHANGED'] = ''; //Last_Changed	Text Input	%LASTCHANGED%
//        $arr['LEID'] = ''; //LEID	Text Input	%LEID%
//        $arr['EUID'] = ''; //EUID	Text Input	%EUID%
//        $arr['REGION'] = ''; //REGION	Text Input	%REGION%
//        $arr['CC'] = ''; //CC	Text Input	%CC%
//        $arr['NOTES'] = ''; //Notes	Text Input	%NOTES%
//        $arr['DSTOFF'] = ''; //DSTOFF	Text Input	%DSTOFF%
//        $arr['GMTOFF'] = ''; //GMTOFF	Text Input	%GMTOFF%
//        $arr['OPTINIP'] = ''; //OPTIN_IP	Text Input	%OPTINIP%
//        $arr['OPTINTIME'] = ''; //OPTIN_TIME	Text Input	%OPTINTIME%
//        $arr['CONFIRMIP'] = ''; //Confirm_IP	Text Input	%CONFIRMIP%
        $arr['SUBSCRIPTION_STATUS'] = $userSubscription->status; //Subscription Status	Text Input	%SUBSCRIPTION_STATUS%
//        $arr['CANCELLATION_DATE'] = ''; //Cancellation Date	Text Input	%CANCELLATION_DATE%
        $list = array();
        foreach($arr as $key => $value) {
            $list[urlencode("field[%{$key}%,0]")] = $value;
//            $list[urlencode("field[%{$key}%,0]")] = urlencode($value);
        }

        $list = array_merge($list, $this->_getNextDeliveryData($user));

//var_dump($list);
        return $list;
    }


    /**
     * @return ActiveCampaign
     */
    private function _getConnection() {
        if($this->connection instanceof ActiveCampaign && $this->connectionIsOk) {
            return $this->connection;
        } else {
            return $this->_connect();
        }
    }

    /**
     * @return ActiveCampaign
     * @throws \Exception
     */
    private function _connect() {
        $this->connection = new ActiveCampaign(
            config('services.activecampaign.api_url'),
            config('services.activecampaign.api_key')
        );
//var_dump($this->connection->credentials_test());
        if ($this->connection->credentials_test()) {
            return $this->connection;
        } else {
            throw new \Exception("Can't connect to Active Campaign");
        }
    }

    /**
     * @var ActiveCampaign
     */
    private $connection;
    private $connectionIsOk = false;

    public static function GetInstance() {
        if(!(self::$instance instanceof self)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {

    }

    /**
     * @var AC_Mediator
     */
    private static $instance;



}