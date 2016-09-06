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
//var_dump($currentCustomer);
            if($currentCustomer && $currentCustomer->success && $currentCustomer->lists) {
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
                    $r = $ac->Unsubscribe($event->user, [AC_Mediator::LIST_One_Potato_Subscribers, ]);
//                    $r = $ac->RemoveFromAutomation($event->user, AC_Mediator::AUTOMATION_Welcome_Email);
//                    $r = $ac->SendMessage($event->user, 23, 23);
//var_dump($r);
                }
            }

//die();
            $r = $ac->UpdateCustomerData($event->user, [AC_Mediator::LIST_Welcome_To_One_Potato, AC_Mediator::LIST_One_Potato_Subscribers, ]);

//var_dump($r);
        } catch (Exception $e) {
//var_dump($e->getMessage());
        }
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