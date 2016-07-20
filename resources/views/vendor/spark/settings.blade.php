@extends('spark::layouts.app')

@section('scripts')
    @if (Spark::billsUsingStripe())
        <script src="https://js.stripe.com/v2/"></script>
    @else
        <script src="https://js.braintreegateway.com/v2/braintree.js"></script>
    @endif
@endsection

@section('content')
<spark-settings :user="user" :teams="teams" inline-template>
    <div class="spark-screen container">
        <div class="row">
            <div class="col-xs-12">
                <h1>Account Settings</h1>
            </div>
            <!-- Tabs -->
            <div class="sidebar col-md-4">
                <div class="panel panel-default panel-flush">

                    <div class="panel-body">
                        <div class="spark-settings-tabs">
                            <ul class="nav nav-sidebar spark-settings-stacked-tabs" role="tablist">

                                @if (Spark::hasPaidPlans())
                                    <!-- Plan Details Link -->
                                    <li role="presentation">
                                        <a href="#plan_details" aria-controls="plan_details" role="tab" data-toggle="tab">
                                            <i class="icon icon-silverware"></i>Plan Details
                                        </a>
                                    </li>
                                @endif

                                <!-- Delivery Information Link -->
                                <li role="presentation">
                                    <a href="#delivery_info" aria-controls="delivery_info" role="tab" data-toggle="tab">
                                        <i class="icon icon-truck"></i>Delivery Information
                                    </a>
                                </li>

                                <!-- Account Information Link -->
                                <li role="presentation">
                                    <a href="#account_info" aria-controls="account_info" role="tab" data-toggle="tab">
                                        <i class="icon icon-user"></i>Account Information
                                    </a>
                                </li>

                                @if (Spark::canBillCustomers())
                                    <!-- Payment Method Link -->
                                    <li role="presentation">
                                        <a href="#payment_info" aria-controls="payment_info" role="tab" data-toggle="tab">
                                            <i class="icon icon-creditcard"></i>Payment Information
                                        </a>
                                    </li>
                                @endif

                                <!-- Referrals Link -->
                                <li role="presentation">
                                    <a href="#referrals" aria-controls="referrals" role="tab" data-toggle="tab">
                                        <i class="icon icon-talkbubble"></i>Referrals
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Panels -->
            <div class="main col-md-8">
                <div class="tab-content">
                    <!-- Account Information -->
                    <div role="tabpanel" class="tab-pane active" id="account_info">

                        <h2>Account Information</h2>

                        @include('spark::settings.profile')

                        <!-- Teams -->
                        @if (Spark::usesTeams())
                            @include('spark::settings.teams')
                        @endif

                        <!-- Security -->
                        @include('spark::settings.security')

                        <!-- API -->
                        @if (Spark::usesApi())
                            @include('spark::settings.api')
                        @endif
                    </div>

                    <!-- Billing Tab Panes -->
                    @if (Spark::canBillCustomers())
                        @if (Spark::hasPaidPlans())
                            <!-- Plan Details -->
                            <div role="tabpanel" class="tab-pane" id="plan_details">

                                <h2>Plan Details</h2>
                                <a href="#" class="edit-link"><i class="fa fa-pencil"></i> Edit</a>
                                <div v-if="user">
                                    @include('spark::settings.subscription')
                                </div>
                            </div>
                        @endif

                        <!-- Delivery Information -->
                        <div role="tabpanel" class="tab-pane" id="delivery_info">
                            <div v-if="user">
                                @include('spark::settings.subscription')
                            </div>
                        </div>

                        <!-- Payment Information -->
                        <div role="tabpanel" class="tab-pane" id="payment_info">
                            <div v-if="user">
                                @include('spark::settings.payment-method')

                                @include('spark::settings.invoices')
                            </div>
                        </div>

                        <!-- Referrals -->
                        <div role="tabpanel" class="tab-pane" id="referrals">
                            <div v-if="user">
                                Referrals
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</spark-settings>
@endsection
