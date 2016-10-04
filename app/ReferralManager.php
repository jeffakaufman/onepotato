<?php
/**
 * Created by PhpStorm.
 * User: aleksey
 * Date: 30/09/16
 * Time: 17:29
 */

namespace App;


class ReferralManager {

    public static function TestReferral(User $user, $email) {
        $existingUser = User::where('email', $email)
            ->first();

        if($existingUser) {
            return 'user_exists';
        }

        $existingReferral = Referral::where('referral_email', $email)
            ->where('referrer_user_id', $user->id)
            ->first();

        if($existingReferral) {
            return 'referral_exists';
        }

        return false;
    }

    public static function ReferralIsSent(User $user) {

    }

    public static function GetReferral($referralId) {
        return Referral::find($referralId);
    }

    public static function CheckIfReferralEmailIsCorrect($referralId, $email) {
        $r = self::GetReferral($referralId);
        if($r) {
            return ($r->referral_email == $email);
        } else {
            return false;
        }
    }

    public static function ProcessReferralApplied($referralId, User $user) {
        $referral = self::GetReferral($referralId);
        if(!$referral) return;

        $now = new \DateTime('now');
        $referral->did_subscribe = 1;
        $referral->redeemed_by_user_id = $user->id;
        $referral->referral_applied = $now->format('Y-m-d');
        $referral->save();


        $appliedCount = Referral::where('referrer_user_id', $userId)
            ->where('did_subscribe', '1')
            ->count();

        if(0 == $appliedCount) {
            $skip = true;
        } else {
            $skip = (($appliedCount % 3) != 0);
        }
        if($skip) return;

        try {
            self::ProcessCrediting($referral->referrer_user_id);
        } catch (\Exception $e) {

        }
    }


    public static function ProcessCrediting($userId) {
        $user = User::find($userId);
        if(!$user) throw new \Exception("User Not Found");

        switch($user->status) {
            case User::STATUS_ACTIVE:
            case User::STATUS_INACTIVE:
                //Do Nothing - Everything is OK
                break;

            default:
                throw new \Exception("User is not Active at this moment", 1);
                break;
        }

        $sub = UserSubscription::where('user_id',$id)
            ->where('status', 'active')
            ->first();

        if(!$sub) throw new \Exception("Active subscription not found", 2);

        $productId = $sub->product_id;
        $product = Product::find($productId);

        if(!$product) throw new \Exception("Product not found", 3);

        $price = $product->cost;

        $creditAmount = -1 * abs($price*100);

        //issue credit
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        \Stripe\InvoiceItem::create(array(
            "customer" => $user->stripe_id,
            "amount" => $creditAmount,
            "currency" => "usd",
            "description" => "Referral system credit",
        ));

        //record credit in database
        $credit = new Credit;
        $credit->user_id = $userId;
        $credit->credit_amount = abs($creditAmount);
        $credit->credit_description = $credit_description;
        $credit->credit_status = "applied_to_stripe";
        $credit->save();

    }
}

// 1. User sends link out from /account. If a link is resent, a second record should not be added to referrals.
//    Users shouldn’t be allowed to refer the same email more than once,
//    or refer an email address that is already in the system.

// 2. The user receiving the email goes to the site,
//    and the referral table is checked to make sure that email address was referred by that users.id.
//    If not, the order processes like any other one, there shouldn’t be an error.
//    If the email DOES match the ID sent out, then referrals should track that the user started a sign up.

// 3. Once the user completes their sign up, their users.id should be added to redeemed_by_user_id.

// 4. If that is the third (or 6th, or 9th, it is EVERY 3rd person) sign up, then call Matt’s credit application code.
//    He is still working on it, so for now have the system send him and me an email with the name of the person
//    who should be credited so we can do it manually.
