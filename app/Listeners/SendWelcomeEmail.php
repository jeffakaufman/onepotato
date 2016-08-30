<?php

namespace App\Listeners;

use App\AC_Mediator;
use App\Events\UserHasRegistered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Exception;


class SendWelcomeEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserHasRegistered  $event
     * @return void
     */
    public function handle(UserHasRegistered $event)
    {

        $ac = AC_Mediator::GetInstance();
        try {

            $currentCustomer = $ac->GetCustomerData($event->user);

            if($currentCustomer) {
                $alreadySubscribed = false;
                foreach((array)$currentCustomer->lists as $listId => $list) {
                    switch ($listId) {
                        case AC_Mediator::LIST_One_Potato_Subscribers:
                            $alreadySubscribed = true;
                            break;

                        default:
                            // Do nothing
                            break;
                    }
                }
                if($alreadySubscribed) {
//                    $r = $ac->Unsubscribe($event->user, [AC_Mediator::LIST_One_Potato_Subscribers, ]);
//                    $r = $ac->RemoveFromAutomation($event->user, AC_Mediator::AUTOMATION_Welcome_Email);
//                    $r = $ac->SendMessage($event->user, 2, 23);
//var_dump($r);
                }
            }

//die();
            $r = $ac->UpdateCustomerData($event->user, [AC_Mediator::LIST_Welcome_To_One_Potato, AC_Mediator::LIST_One_Potato_Subscribers, ]);
var_dump($r);
        } catch (Exception $e) {
var_dump($e->getMessage());
        }
        return;

        $ac = new ActiveCampaign(
            config('services.activecampaign.api_url'),
            config('services.activecampaign.api_key')
        );

        if (!(int)$ac->credentials_test()) {
            return;
//            echo "<p>Access denied: Invalid credentials (URL and/or API key).</p>";
//            exit();
        }

        $user = $event->user;

        $listId = 3;


        /*
         * ADD OR EDIT CONTACT (TO THE NEW LIST CREATED ABOVE).
         */

        $firstName = $user->first_name;
        $lastName = $user->last_name;

        try {
            @list($_firstName, $_lastName) = explode(' ', $user->name, 2);
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

            "p[{$listId}]" => $listId,
            "status[{$listId}]" => 1, // "Active" status
        );

        $contact = array_merge($contact, $this->_getCustomFields($user));

        $contact_sync = $ac->api("contact/sync", $contact);
//var_dump($contact_sync);die();
        if ((int)$contact_sync->success) {
            // successful request
            $contact_id = (int)$contact_sync->subscriber_id;
//            echo "<p>Contact synced successfully (ID {$contact_id})!</p>";
        } else {
            $contact_id = false;
        }

//        var_dump($contact_id);

//        var_dump($event);die();
        //
    }


    private function _getCustomFields(User $user) {
/*
        $firstName = $user->first_name;
        $lastName = $user->last_name;

        try {
            @list($_firstName, $_lastName) = explode(' ', $user->name, 2);
        } catch (Exception $e) {
            $_firstName = '';
            $_lastName = '';
        }
        $firstName = $firstName ?: $_firstName;
        $lastName = $lastName ?: $_lastName;
*/
        $userSubscription = UserSubscription::where('user_id',$user->id)->first();

        $product = Product::find($userSubscription->product_id);
//        $product->
//        $nextDeliveryDate = date('Y-m-d', strtotime($user->start_date));

        $nextDeliveryDate = MenusUsers::where('users_id', $user->id)
            ->where('delivery_date', '>', $today->format('Y-m-d'))
            ->min('delivery_date');

        $nextDelivery = MenusUsers::where('users_id',$user->id)->where('delivery_date',$nextDeliveryDate)->get();
        $meal1 = $nextDelivery[0]->menus_id;
        $meal2 = $nextDelivery[1]->menus_id;
        $meal3 = $nextDelivery[2]->menus_id;

        $menu1 = Menu::find($meal1);
        $menu2 = Menu::find($meal2);
        $menu3 = Menu::find($meal3);

        $productInfo = $product ? $product->productDetails() : new stdClass();

        $arr = array();
        $arr['NEXT_DELIVERY_DATE'] = $nextDeliveryDate; //Next Delivery Date	Text Input	%NEXT_DELIVERY_DATE%	Next Delivery Date
        $arr['YOUR_MEAL_IMAGE'] = $menu1 ? $menu1->image : ''; //Your Meal Image	Text Input	%YOUR_MEAL_IMAGE%	URL to image
        $arr['YOUR_MEAL_IMAGE_2'] = $menu2 ? $menu2->image : ''; //Your Meal Image 2	Text Input	%YOUR_MEAL_IMAGE_2%	URL to image 2
        $arr['YOUR_MEAL_IMAGE_3'] = $menu3 ? $menu3->image : ''; //Your Meal Image 3	Text Input	%YOUR_MEAL_IMAGE_3%	URL to image 3
        $arr['YOUR_MEAL_NAME'] = $menu1 ? $menu1->menu_title : ''; //Your Meal Name	Text Input	"	%YOUR_MEAL_NAME%"	Your Meal Name
        $arr['YOUR_MEAL_NAME_2'] = $menu2 ? $menu2->menu_title : ''; //Your Meal Name 2	Text Input	%YOUR_MEAL_NAME_2%
        $arr['YOUR_MEAL_NAME_3'] = $menu3 ? $menu3->menu_title : ''; //        Your Meal Name 3	Text Input	%YOUR_MEAL_NAME_3%
        $arr['PRODUCT'] = $product ? $product->product_description : ''; //        Product	Text Input	%PRODUCT%	ex: One Potato Box, 2 Adults, 2 Children
        $arr['BOX_TYPE'] = $productInfo->BoxType; //Box Type	Text Input	%BOX_TYPE%
        $arr['DELIVERY_DAY'] = $nextDeliveryDate; //        Delivery Day	Text Input	%DELIVERY_DAY%	Delivery Day
        $arr['TERM'] = ''; //Term	Text Input	%TERM%
        $arr['PRICE'] = $product ? $product->cost : ''; //        Price	Text Input	%PRICE%
        $arr['STATUS'] = $user->status; //        Status	Text Input	%STATUS%
        $arr['REFERENCE_ID'] = $userSubscription->stripe_id; //        Reference ID	Text Input	%REFERENCE_ID%
        $arr['DELIVERY_SKIP_DATE'] = ''; //        Delivery Skip Date	Text Input	%DELIVERY_SKIP_DATE%	This should update to blank if they did not skip that week
        $arr['PAYMENT_FAIL_COUNT'] = ''; //Payment Fail Count	Text Input	%PAYMENT_FAIL_COUNT%	This should update to blank if they make a payment
//        $arr['GIFT_CARD_ISSUED'] = ''; //Gift Card Issued	Text Input	%GIFT_CARD_ISSUED%	Removed on 7/28/16 per conversation this is not good enough for data.
        $arr['REFERRAL_NAME'] = ''; //Referral Name	Text Input	%REFERRAL_NAME%	Added 7/28/16
        $arr['REFERRAL_EMAIL'] = ''; //Referral Email	Text Input	%REFERRAL_EMAIL%	Added 7/28/16
        $arr['GIFT_CARD_AMOUNT'] = ''; //Gift Card Amount	Text Input	%GIFT_CARD_AMOUNT%	Added 7/28/16
        $arr['GIFT_CARD_RECIPIENT_NAME'] = ''; //Gift Card Recipient Name	Text Input	%GIFT_CARD_RECIPIENT_NAME%	Added 7/28/16
        $arr['REFERRAL_FEE_AMOUNT'] = ''; //Referral Fee Amount	Text Input	%REFERRAL_FEE_AMOUNT%	Added 7/28/16
        $arr['GIFT_CARD_PURCHASER_NAME'] = ''; //Gift Card Purchaser Name	Text Input	%GIFT_CARD_PURCHASER_NAME%	Added 7/28/16
        $arr['PERSONALIZED_GIFT_CARD_LINK'] = ''; //Personalized Gift Card Link	Text Input	%PERSONALIZED_GIFT_CARD_LINK%	Added 7/28/16

//Standard AC Fields

//        $arr['FIRSTNAME'] = $firstName; //First Name	Text Input	%FIRSTNAME%
//        $arr['LASTNAME'] = $lastName; //Last Name	Text Input	%LASTNAME%
//        $arr['EMAIL'] = $user->email; //Email	Text Input	%EMAIL%
//        $arr['PHONE'] = $user->phone; //Phone	Text Input	%PHONE%
        $arr['ZIP_CODE'] = $user->billing_zip; //Zip Code	Text Input	%ZIP_CODE%
        $arr['LATITUDE'] = ''; //Latitude	Text Input	%LATITUDE%
        $arr['LONGITUDE'] = ''; //Longitude	Text Input	%LONGITUDE%
        $arr['MEMBERRATING'] = ''; //Member_Rating	Text Input	%MEMBERRATING%
        $arr['CONFIRMTIME'] = ''; //Confirm_Time	Text Input	%CONFIRMTIME%
        $arr['STATE'] = $user->billing_state; //State	Text Input	%STATE%
        $arr['TIMEZONE'] = ''; //TimeZone	Text Input	%TIMEZONE%
        $arr['LASTCHANGED'] = ''; //Last_Changed	Text Input	%LASTCHANGED%
        $arr['LEID'] = ''; //LEID	Text Input	%LEID%
        $arr['EUID'] = ''; //EUID	Text Input	%EUID%
        $arr['REGION'] = ''; //REGION	Text Input	%REGION%
        $arr['CC'] = ''; //CC	Text Input	%CC%
        $arr['NOTES'] = ''; //Notes	Text Input	%NOTES%
        $arr['DSTOFF'] = ''; //DSTOFF	Text Input	%DSTOFF%
        $arr['GMTOFF'] = ''; //GMTOFF	Text Input	%GMTOFF%
        $arr['OPTINIP'] = ''; //OPTIN_IP	Text Input	%OPTINIP%
        $arr['OPTINTIME'] = ''; //OPTIN_TIME	Text Input	%OPTINTIME%
        $arr['CONFIRMIP'] = ''; //Confirm_IP	Text Input	%CONFIRMIP%
        $arr['SUBSCRIPTION_STATUS'] = $userSubscription->status; //Subscription Status	Text Input	%SUBSCRIPTION_STATUS%
        $arr['CANCELLATION_DATE'] = ''; //Cancellation Date	Text Input	%CANCELLATION_DATE%


        $list = array();
        foreach($arr as $key => $value) {
            $list[urlencode("field[%{$key}%,0]")] = urlencode($value);
        }
        return $list;
    }
}


/*
Name	Type	Personalized Tag (Email Use)	Notes
Custom AC fields
Next Delivery Date	Text Input	%NEXT_DELIVERY_DATE%	Next Delivery Date
Your Meal Image	Text Input	%YOUR_MEAL_IMAGE%	URL to image
Your Meal Image 2	Text Input	%YOUR_MEAL_IMAGE_2%	URL to image 2
Your Meal Image 3	Text Input	%YOUR_MEAL_IMAGE_3%	URL to image 3
Your Meal Name	Text Input	"	%YOUR_MEAL_NAME%"	Your Meal Name
Your Meal Name 2	Text Input	%YOUR_MEAL_NAME_2%
Your Meal Name 3	Text Input	%YOUR_MEAL_NAME_3%
Product	Text Input	%PRODUCT%	ex: One Potato Box, 2 Adults, 2 Children
Box Type	Text Input	%BOX_TYPE%
Delivery Day	Text Input	%DELIVERY_DAY%	Delivery Day
Term	Text Input	%TERM%
Price	Text Input	%PRICE%
Status	Text Input	%STATUS%
Reference ID	Text Input	%REFERENCE_ID%
Delivery Skip Date	Text Input	%DELIVERY_SKIP_DATE%	This should update to blank if they did not skip that week
Payment Fail Count	Text Input	%PAYMENT_FAIL_COUNT%	This should update to blank if they make a payment
Gift Card Issued	Text Input	%GIFT_CARD_ISSUED%	Removed on 7/28/16 per conversation this is not good enough for data.
Referral Name	Text Input	%REFERRAL_NAME%	Added 7/28/16
Referral Email	Text Input	%REFERRAL_EMAIL%	Added 7/28/16
Gift Card Amount	Text Input	%GIFT_CARD_AMOUNT%	Added 7/28/16
Gift Card Recipient Name	Text Input	%GIFT_CARD_RECIPIENT_NAME%	Added 7/28/16
Referral Fee Amount	Text Input	%REFERRAL_FEE_AMOUNT%	Added 7/28/16
Gift Card Purchaser Name	Text Input	%GIFT_CARD_PURCHASER_NAME%	Added 7/28/16
Personalized Gift Card Link	Text Input	%PERSONALIZED_GIFT_CARD_LINK%	Added 7/28/16

Standard AC Fields
First Name	Text Input	%FIRSTNAME%
Last Name	Text Input	%LASTNAME%
Email	Text Input	%EMAIL%
Phone	Text Input	%PHONE%
Zip Code	Text Input	%ZIP_CODE%
Latitude	Text Input	%LATITUDE%
Longitude	Text Input	%LONGITUDE%
Member_Rating	Text Input	%MEMBERRATING%
Confirm_Time	Text Input	%CONFIRMTIME%
State	Text Input	%STATE%
TimeZone	Text Input	%TIMEZONE%
Last_Changed	Text Input	%LASTCHANGED%
LEID	Text Input	%LEID%
EUID	Text Input	%EUID%
REGION	Text Input	%REGION%
CC	Text Input	%CC%
Notes	Text Input	%NOTES%
DSTOFF	Text Input	%DSTOFF%
GMTOFF	Text Input	%GMTOFF%
OPTIN_IP	Text Input	%OPTINIP%
OPTIN_TIME	Text Input	%OPTINTIME%
Confirm_IP	Text Input	%CONFIRMIP%
Subscription Status	Text Input	%SUBSCRIPTION_STATUS%
Cancellation Date	Text Input	%CANCELLATION_DATE%
 */