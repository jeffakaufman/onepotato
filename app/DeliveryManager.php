<?php
/**
 * Created by PhpStorm.
 * User: aleksey
 * Date: 12/10/16
 * Time: 11:14
 */

namespace App;

class DeliveryManager {

    const STATUS_HOLD = 'hold';
    const STATUS_HELD = 'held';
    const STATUS_RELEASED = 'released';

    public function SkipDelivery(User $user, \DateTime $deliveryDate) {

        $currentHold = Shippingholds::where('user_id', $user->id)
            ->where('date_to_hold', $deliveryDate->format('Y-m-d'))
            ->where('hold_status', self::STATUS_RELEASED)
            ->first();

        if($currentHold) {
            $currentHold->hold_status = self::STATUS_HOLD;
            return $currentHold->save();
        } else {
            $hold = new Shippingholds();
            $hold->user_id = $user->id;
            $hold->date_to_hold = $deliveryDate->format('Y-m-d');
            $hold->hold_status = self::STATUS_HOLD;
            return $hold->save();
        }
    }

    public function UnskipDelivery(User $user, \DateTime $deliveryDate) {
        $hold = Shippingholds::where('user_id', $user->id)
            ->where('date_to_hold', $deliveryDate->format('Y-m-d'))
            ->where('hold_status', self::STATUS_HOLD)
            ->first();

        //if there is a hold
        if ($hold) {
            $hold->hold_status = self::STATUS_RELEASED;
            return $hold->save();

            //get the custoemr
            /*
            $user = User::where('id', $id)->first();
            $user->status = User::STATUS_ACTIVE;
            $customer_stripe_id = $user->stripe_id;

            //retrieve stripe ID from subscriptions table
            $userSubscription = UserSubscription::where('user_id',$id)->first();

            $userSubscription->status = "active";
            $plan_id = $userSubscription->product_id;

            $product = Product::where('id', $plan_id)->first();
            $stripe_plan_id = $product->stripe_plan_id;

            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            $subscription = \Stripe\Subscription::create(array(
              "customer" => $customer_stripe_id,
              "plan" => $stripe_plan_id
            ));

            $userSubscription->stripe_id = $subscription->id;
            $userSubscription->save();
            $user->save();
            */

        } else {
            return false;
        }
    }


    /**
     * @return DeliveryManager
     */
    public static function GetInstance() {
        if(!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @var DeliveryManager
     */
    private static $instance;
}