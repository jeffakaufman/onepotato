<!-- Coupon -->
<div class="row" v-if="coupon">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-success">
            <div class="panel-heading">Discount</div>

            <div class="panel-body">
                The coupon's @{{ discount }} discount will be applied to your subscription!
            </div>
        </div>
    </div>
</div>

<!-- Invalid Coupon -->
<div class="row" v-if="invalidCoupon">
    <div class="col-md-8 col-md-offset-2">
        <div class="alert alert-danger">
            Whoops! This coupon code is invalid.
        </div>
    </div>
</div>

<!-- Invitation -->
<div class="row" v-if="invitation">
    <div class="col-md-8 col-md-offset-2">
        <div class="alert alert-success">
            We found your invitation to the <strong>@{{ invitation.team.name }}</strong> team!
        </div>
    </div>
</div>

<!-- Invalid Invitation -->
<div class="row" v-if="invalidInvitation">
    <div class="col-md-8 col-md-offset-2">
        <div class="alert alert-danger">
            Whoops! This invitation code is invalid.
        </div>
    </div>
</div>

<!-- Plan Selection -->
<div class="row" v-if="paidPlans.length > 0">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="pull-left" :class="{'btn-table-align': hasMonthlyAndYearlyPlans}">
                    Subscription
                </div>

                <!-- Interval Selector Button Group -->
                <div class="pull-right">
                    <div class="btn-group" v-if="hasMonthlyAndYearlyPlans" style="padding-top: 2px;">
                        <!-- Monthly Plans -->
                        <button type="button" class="btn btn-default"
                                @click="showMonthlyPlans"
                                :class="{'active': showingMonthlyPlans}">

                            Monthly
                        </button>

                        <!-- Yearly Plans -->
                        <button type="button" class="btn btn-default"
                                @click="showYearlyPlans"
                                :class="{'active': showingYearlyPlans}">

                            Yearly
                        </button>
                    </div>
                </div>

                <div class="clearfix"></div>
            </div>

            <div class="panel-body spark-row-list">
                <!-- Plan Error Message - In General Will Never Be Shown -->
                <div class="alert alert-danger" v-if="registerForm.errors.has('plan')">
                    @{{ registerForm.errors.get('plan') }}
                </div>

                <!-- European VAT Notice -->
                @if (Spark::collectsEuropeanVat())
                    <p class="p-b-md">
                        All subscription plan prices are excluding applicable VAT.
                    </p>
                @endif

                <table class="table table-borderless m-b-none">
                    <thead></thead>
                    <tbody>
                        <tr v-for="plan in plansForActiveInterval">
                            <!-- Plan Name -->
                            <td>
                                <div class="btn-table-align" @click="showPlanDetails(plan)">
                                    <span style="cursor: pointer;">
                                        <strong>@{{ plan.name }}</strong>
                                    </span>
                                </div>
                            </td>

                            <!-- Plan Features Button -->
                            <td>
                                <button class="btn btn-default m-l-sm" @click="showPlanDetails(plan)">
                                    <i class="fa fa-btn fa-star-o"></i>Plan Features
                                </button>
                            </td>

                            <!-- Plan Price -->
                            <td>
                                <div class="btn-table-align">
                                    <span v-if="plan.price == 0">
                                        Free
                                    </span>

                                    <span v-else>
                                        @{{ plan.price | currency spark.currencySymbol }} / @{{ plan.interval | capitalize }}
                                    </span>
                                </div>
                            </td>

                            <!-- Trial Days -->
                            <td>
                                <div class="btn-table-align" v-if="plan.trialDays">
                                    @{{ plan.trialDays}} Day Trial
                                </div>
                            </td>

                            <!-- Plan Select Button -->
                            <td class="text-right">
                                <button class="btn btn-primary btn-plan" v-if="isSelected(plan)" disabled>
                                    <i class="fa fa-btn fa-check"></i>Selected
                                </button>

                                <button class="btn btn-primary-outline btn-plan" @click="selectPlan(plan)" v-else>
                                    Select
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Basic Profile -->
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h1 v-if="paidPlans.length > 0">
                    Profile
                </h1>

                <h1 v-else>
                    Let’s get started!
                    <div class="panel-subtitle">Everything you need to make organic & delicious dinners
the whole family will love delivered straight to your door each week.</div>
                </h1>
            </div>

            <div class="panel-body nopadding">
                <!-- Generic Error Message -->
                <div class="alert alert-danger" v-if="registerForm.errors.has('form')">
                    @{{ registerForm.errors.get('form') }}
                </div>

                <!-- Invitation Code Error -->
                <div class="alert alert-danger" v-if="registerForm.errors.has('invitation')">
                    @{{ registerForm.errors.get('invitation') }}
                </div>

                <!-- Registration Form -->
                @include('spark::auth.register-common-form')

            </div>
        </div>
    </div>
</div>

<div class="row" v-if="paidPlans.length == 0">
    <div class="col-md-4 col-md-offset-2">
        <h5>NO COMMITMENT</h5>
        <p>Skip weeks, change your family size, or cancel anytime. </p>

        <h5>CUSTOMIZED TO YOUR FAMILY</h5>
        <p>Customize to your family’s size and take advantage of special kid’s pricing. We’ll personalize your menus based on your dietary preferences.</p> 

        <h5>CONVENIENT DELIVERY</h5>
        <p>Meals arrive in a recyclable insulated box so food stays fresh until you open it.</p>
    </div>
    <div class="col-md-4">
        <div style="position: absolute; top: -5px; right: 0;"><img src="/img/badge_free_shipping.png"></div>
        <img src="/img/p_register.jpg">
    </div>
</div>
<div class="row" v-if="paidPlans.length == 0">
    <div class="footnote pad col-md-8 col-md-offset-2">One Potato meals feature organic ingredients whenever possible. All organic ingredients are clearly labeled upon delivery.</div>
</div>
