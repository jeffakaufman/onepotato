<?php

namespace Laravel\Spark;

class Spark
{
    use Configuration\CallsInteractions,
        Configuration\ManagesApiOptions,
        Configuration\ManagesAppDetails,
        Configuration\ManagesAvailablePlans,
        Configuration\ManagesAvailableRoles,
        Configuration\ManagesBillingProviders,
        Configuration\ManagesModelOptions,
        Configuration\ManagesSupportOptions,
        Configuration\ManagesTwoFactorOptions,
        Configuration\ProvidesScriptVariables;

    /**
     * The Spark version.
     */
    public static $version = '0.1.9';
}
