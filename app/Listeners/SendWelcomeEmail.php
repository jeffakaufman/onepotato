<?php

namespace App\Listeners;

use App\AC_Mediator;
use App\Events\UserHasRegistered;
use App\Product;
use App\Shipping_address;
use App\UserSubscription;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Exception;
use App\User;
use Illuminate\Support\Facades\Mail;

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
        $this->_sendAc($event->user);
       // $this->_sendEmail($event->user);
    }

    private function _sendAc(User $user) {
        $ac = AC_Mediator::GetInstance();
        $r = false;
        try {

            $currentCustomer = $ac->GetCustomerData($user);
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
                    $r = $ac->Unsubscribe($user, [AC_Mediator::LIST_One_Potato_Subscribers, ]);
//                    $r = $ac->RemoveFromAutomation($event->user, AC_Mediator::AUTOMATION_Welcome_Email);
//                    $r = $ac->SendMessage($event->user, 23, 23);
//var_dump($r);
                }
            }

//die();
            $r = $ac->UpdateCustomerData($user, [AC_Mediator::LIST_Welcome_To_One_Potato, AC_Mediator::LIST_One_Potato_Subscribers, ]);

//var_dump($r);
        } catch (Exception $e) {
//var_dump($e->getMessage());
        }

        return $r;
    }

    private function _sendEmail(User $user) {

        $subscription = UserSubscription::where('user_id', $user->id)->first();
        if(!$subscription) return;
        $product = Product::find($subscription->product_id);
        if(!$product) return;
        $shippingAddress = Shipping_address::where('user_id', $user->id)->first();

        $orderDate = new \DateTime('now');
        $params = [
            'orderId' => $subscription->id,
            'orderDate' => $orderDate->format('M d, Y'),
            'lines' => [
                (object)[
                    'name' => $product->product_description,
                    'qty' => 1,
                    'price' => number_format($product->cost, 2),
                ],
            ],

            'subtotal' => number_format($product->cost, 2),
            'shipping' => '0.00',
            'tax' => '0.00',
            'total' => number_format($product->cost, 2),

            'name' => $user->name,
            'address' => $shippingAddress ? $shippingAddress->shipping_address.' '.$shippingAddress->shipping_address_2 : '',
            'city' => $shippingAddress ? $shippingAddress->shipping_city : '',
            'state' => $shippingAddress ? $shippingAddress->shipping_state : '',
            'zip' => $shippingAddress ? $shippingAddress->shipping_zip : '',
            'country' => $shippingAddress ? $shippingAddress->shipping_country : '',
        ];

        $r = Mail::send('emails.order_details', $params, function($m) use ($user, $subscription) {
//            $m->from('ahhmed@mail.ru', 'Aleksey Zagarov');
            $m->to('jenna@onepotato.com', 'Jenna');
            $m->to('azagarov@mail.ru', 'Jenna');
            $m->to('agedgouda@gmail.com', 'Jenna')->subject("New Order #{$subscription->id} Created");
        });

        return $r;
    }

}
