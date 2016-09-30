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

    }

    public static function ReferralIsSent(User $user) {

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