<?php

namespace App;

use Laravel\Spark\User as SparkUser;

class User extends SparkUser
{

    public function userSubscription() {
        return $this->hasOne('App\UserSubscription', 'user_id');
    }
    
    public function userSubInvoices() {
        return $this->hasMany('App\Subinvoice', 'user_id');
    }

    public function IsSubscribed() {
        $subscription = UserSubscription::where('user_id', '=', $this->id)
            ->where('status', '=', 'active')
            ->where('stripe_id', '<>', '')
            ->where('stripe_id', '<>', '0')
            ->first();

        if(!$subscription) return false;

        return true;
//var_dump($subscription);die();
    }

    public function GetNextDeliveryDate($after = 'now') {
        $after = new \DateTime($after);

        $startDate = new \DateTime($this->start_date);

        if($startDate > $after) {
            $nextDeliveryDate = $startDate->format('Y-m-d');
        } else {
            $nextDeliveryDate = MenusUsers::where('users_id', $this->id)
                ->where('delivery_date', '>', $after->format('Y-m-d'))
                ->min('delivery_date');
        }

        return $nextDeliveryDate;

    } 
   
   public function getSkips()
    {
        return $this->hasMany('App\Shippingholds');
    }
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'authy_id',
        'country_code',
        'phone',
        'card_brand',
        'card_last_four',
        'card_country',
        'billing_address',
        'billing_address_line_2',
        'billing_city',
        'billing_zip',
        'billing_country',
        'extra_billing_information',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'trial_ends_at' => 'date',
        'uses_two_factor_auth' => 'boolean',
    ];
}
