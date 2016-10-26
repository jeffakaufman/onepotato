<?php
/**
 * Created by PhpStorm.
 * User: aleksey
 * Date: 13/10/16
 * Time: 14:29
 */

namespace App;

use Stripe\Stripe;
use Stripe\Subscription;

class StripeMediator {

    /**
     * @param $subscriptionId
     * @return Subscription
     */
    public function RetrieveSubscription($subscriptionId) {
        return Subscription::retrieve($subscriptionId);
    }


    public function UpdateSubscription($subscriptionId, $planId, $prorate = null, $trialEnd = null) {
        $subscription = $this->RetrieveSubscription($subscriptionId);
        $subscription->plan = $planId;

        if(!is_null($prorate)) {
            $subscription->prorate = $prorate;
        }

        if(!is_null($trialEnd)) {
            $subscription->trial_end = $trialEnd;
        }
        return $subscription->save();
    }



    /**
     * @return StripeMediator
     */
    public static function GetInstance() {
        if(!(self::$instance instanceof self)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }


    /**
     * @var StripeMediator
     */
    private static $instance;
}