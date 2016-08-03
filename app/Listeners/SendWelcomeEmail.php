<?php

namespace App\Listeners;

use App\Events\UserHasRegistered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use \ActiveCampaign;
use Mockery\CountValidator\Exception;

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

        $listId = 2;


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
            "p[{$listId}]" => $listId,
            "status[{$listId}]" => 1, // "Active" status
        );

        $contact_sync = $ac->api("contact/sync", $contact);

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
}
