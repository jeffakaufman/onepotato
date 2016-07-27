<?php

namespace Laravel\Spark\Configuration;

use Illuminate\Support\HtmlString;

trait ManagesAppDetails
{
    /**
     * The application / product details.
     *
     * @var array
     */
    public static $details = [];

    /**
     * The e-mail addresses of all of the application's developers.
     *
     * @var array
     */
    public static $developers = [];

    /**
     * The e-mail addresses of all of the application's admins.
     *
     * @var array
     */
    public static $admins = [];

    /**
     * Define the application information.
     *
     * @param  array  $details
     * @return void
     */
    public static function details(array $details)
    {
        static::$details = $details;
    }

    /**
     * Get the product name from the application information.
     *
     * @return string
     */
    public static function product()
    {
        return static::$details['product'];
    }

    /**
     * Get the invoice meta information, such as product, etc.
     *
     * @return array
     */
    public static function generateInvoicesWith()
    {
        return array_merge([
            'vendor' => '',
            'product' => '',
            'street' => '',
            'location' => '',
            'phone' => '',
        ], static::$details);
    }

    /**
     * Get the invoice data payload for the given billable entity.
     *
     * @param  mixed  $billable
     * @return array
     */
    public static function invoiceDataFor($billable)
    {
        return array_merge([
            'vendor' => 'Vendor',
            'product' => 'Product',
            'vat' => new HtmlString(nl2br(e($billable->extra_billing_information))),
        ], static::generateInvoicesWith());
    }

    /**
     * Determine if the given e-mail address belongs to a developer.
     *
     * @param  string  $email
     * @return bool
     */
    public static function developer($email)
    {
        return in_array($email, static::$developers);
    }

    /**
     * Set the e-mail addresses that are registered to developers.
     *
     * @param  array  $developers
     * @return void
     */
    public static function developers(array $developers)
    {
        static::$developers = $developers;
    }

    /**
     * Determine if the given e-mail address belongs to a admin.
     *
     * @param  string  $email
     * @return bool
     */
    public static function admin($email)
    {
        return in_array($email, static::$admins);
    }

    /**
     * Set the e-mail addresses that are registered to admins.
     *
     * @param  array  $admins
     * @return void
     */
    public static function admins(array $admins)
    {
        static::$admins = $admins;
    }
}
