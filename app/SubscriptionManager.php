<?php
/**
 * Created by PhpStorm.
 * User: aleksey
 * Date: 13/10/16
 * Time: 15:00
 */

namespace App;

class SubscriptionManager {

    public static function UpdateUserProduct(User $user, Product $newProduct, $prorate = null, $trialEnd = null) {
        $userSubscription = UserSubscription::GetByUserId($user->id);

        if(!$userSubscription) {
            throw new \Exception("User has no active subscription", 100);
        }

        $userSubscription->product_id = $newProduct->id;
        $userSubscription->save();

        $stripeMediator = StripeMediator::GetInstance();
        $stripeMediator->UpdateSubscription($userSubscription->stripe_id, $newProduct->stripe_plan_id, $prorate, $trialEnd);

        $logger = new SimpleLogger("ProductChanges.log");
        $logger->Log("#{$user->id} [{$user->email}] {$user->first_name} {$user->last_name} Product changed to #{$newProduct->id} {$newProduct->sku} {$newProduct->product_descritpion} \${$newProduct->cost} BY Temporary Plan Change");

    }

    /**
     * @param User $user
     * @param \DateTime $deliveryDate
     * @param $numChildren
     * @return Plan_change
     */
    public static function RegisterPlanChange(User $user, \DateTime $deliveryDate, $numChildren) {

        //record $id, $num_children, $weekof in the plan_changes table with status "to_change"
        $plan_change = new Plan_change();
        $plan_change->user_id = $user->id;
        $plan_change->date_to_change = $deliveryDate->format('Y-m-d');
        $plan_change->status = Plan_change::STATUS_TO_CHANGE;

        //look up the product ID

        $userSubscription = UserSubscription::GetByUserId($user->id);
        $current_product_id = $userSubscription->product_id;

        //lookup the SKU

        $currentProduct = Product::find($current_product_id);
        $current_sku = $currentProduct->sku;

        $plan_change->old_sku = $current_sku;



        //update the sku -

        $skuObj = ProductSku::BuildByText($current_sku);
        $skuObj->SetNumChildren($numChildren);

        $change_exists = Plan_change::where('user_id',$user->id)
            ->where('date_to_change',$deliveryDate->format('Y-m-d'))
            ->where('status','to_change')
            ->first();

        $plan_change->sku_to_change = $skuObj->GetAsString();

        if ($change_exists) {
            $change_exists->old_sku = $change_exists->sku_to_change;
            $change_exists->sku_to_change = $skuObj->GetAsString();
            $change_exists->save();
            return $change_exists;
        } else {
            $plan_change->save();
            return $plan_change;
        }
    }


    public static function ProcessPlanChange(User $user, Plan_change $planChange) {
        $userSubscription = UserSubscription::GetByUserId($user->id);
        $current_product_id = $userSubscription->product_id;

        //lookup the SKU

        $currentProduct = Product::find($current_product_id);
        $current_sku = $currentProduct->sku;

        $skuToChange = $planChange->sku_to_change;

        if($current_sku == $skuToChange) {
            throw new \Exception("SKU is the same is current, no need to change", 101);
        }

        $newProduct = Product::GetBySku($skuToChange);

        self::UpdateUserProduct($user, $newProduct, 'false');
    }



    public static function ProcessPlanChangeBack(User $user, Plan_change $planChange) {
        $userSubscription = UserSubscription::GetByUserId($user->id);
        $current_product_id = $userSubscription->product_id;

        //lookup the SKU

        $currentProduct = Product::find($current_product_id);
        $current_sku = $currentProduct->sku;

        $skuToChange = $planChange->old_sku;

        if($current_sku == $skuToChange) {
            throw new \Exception("SKU is the same is current, no need to change", 101);
        }

        $newProduct = Product::GetBySku($skuToChange);

        self::UpdateUserProduct($user, $newProduct, 'false');
    }
}