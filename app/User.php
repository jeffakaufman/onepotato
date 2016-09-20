<?php

namespace App;

use Laravel\Spark\User as SparkUser;

class User extends SparkUser
{

    const STATUS_ADMIN = 'admin';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_ACTIVE = 'active';
    const STATUS_INCOMPLETE = 'incomplete';
    const STATUS_INACTIVE_CANCELLED = 'inactive-cancelled';

    public function userSubscription() {
        return $this->hasOne('App\UserSubscription', 'user_id');
    }
    
    public function userSubInvoices() {
        return $this->hasMany('App\Subinvoice', 'user_id');
    }

    public function IsSubscribed() {

        switch($this->status) {
            case self::STATUS_ACTIVE:
            case self::STATUS_INACTIVE:
                return true;
                break;

            case self::STATUS_INACTIVE_CANCELLED:
            case self::STATUS_ADMIN:
            case self::STATUS_INCOMPLETE:
                return false;
                break;

            default: // Unknown or empty status
                $subscription = UserSubscription::where('user_id', '=', $this->id)
                    ->where('status', '=', 'active')
                    ->where('stripe_id', '<>', '')
                    ->where('stripe_id', '<>', '0')
                    ->first();

                if($subscription) {
                    return true;
                } else {
                    return false;
                }
                break;
        }

    }

    public function IsCancelled() {
        switch ($this->status) {
            case self::STATUS_INACTIVE_CANCELLED:
                return true;

            default:
                return false;
                break;
        }
    }

    public function IsIncomplete() {
        switch($this->status) {
            case self::STATUS_ACTIVE:
            case self::STATUS_INACTIVE:
            case self::STATUS_INACTIVE_CANCELLED:
            case self::STATUS_ADMIN:
                return false;
                break;

            case self::STATUS_INCOMPLETE:
                return true;
                break;

            default: // Unknown or empty status
                $subscription = UserSubscription::where('user_id', '=', $this->id)
                    ->where('status', '=', 'active')
                    ->where('stripe_id', '<>', '')
                    ->where('stripe_id', '<>', '0')
                    ->first();

                if($subscription) {
                    return false;
                } else {
                    return true;
                }

                break;
        }
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
