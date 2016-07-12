<spark-subscribe-stripe :user="user" :team="team"
                :plans="plans" :billable-type="billableType" inline-template>

    <!-- Common Subscribe Form Contents -->
    @include('spark::settings.subscription.subscribe-common')

    <!-- Billing Information -->
    <div class="panel panel-default panel-form">
        <div class="panel-heading">Billing Information</div>

        <div class="panel-body">
            <!-- Generic 500 Level Error Message / Stripe Threw Exception -->
            <div class="alert alert-danger" v-if="form.errors.has('form')">
                We had trouble validating your card. It's possible your card provider is preventing
                us from charging the card. Please contact your card provider or customer support.
            </div>

            <form class="form-horizontal" role="form">
                <!-- Billing Address Fields -->
                @if (Spark::collectsBillingAddress())

                    <input type="checkbox" name="same_as_delivery"> Same as Delivery
                    
                    <h2><i class="fa fa-btn fa-map-marker"></i>Billing Address</h2>

                    @include('spark::settings.subscription.subscribe-address')

                    <h2><i class="fa fa-btn fa-credit-card"></i>Credit Card</h2>
                @endif

                <!-- Cardholder's Name -->
                <div class="form-group">

                    <div class="col-md-6">
                        <input type="text" class="form-control" name="name" v-model="cardForm.name" placeholder="Cardholder's Name">
                    </div>
                </div>

                <!-- Card Number -->
                <div class="form-group" :class="{'has-error': cardForm.errors.has('number')}">

                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="number" data-stripe="number" v-model="cardForm.number" placeholder="Card Number">

                        <span class="help-block" v-show="cardForm.errors.has('number')">
                            @{{ cardForm.errors.get('number') }}
                        </span>
                    </div>
                </div>

                <!-- Security Code -->
                <div class="form-group">

                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="cvc" data-stripe="cvc" v-model="cardForm.cvc" placeholder="Security Code">
                    </div>
                </div>

                <!-- Expiration -->
                <div class="form-group">

                    <!-- Month -->
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="month" maxlength="2" data-stripe="exp-month" v-model="cardForm.month" placeholder="Expiration MM">
                    </div>

                    <!-- Year -->
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="year" maxlength="4" data-stripe="exp-year" v-model="cardForm.year" placeholder="Expiration YYYY">
                    </div>
                </div>

                <!-- ZIP Code -->
                <div class="form-group" v-if=" ! spark.collectsBillingAddress">

                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="zip" v-model="cardForm.zip" placeholder="Zip">
                    </div>
                </div>

                <!-- Coupon -->
                <div class="form-group" :class="{'has-error': form.errors.has('coupon')}">

                    <div class="col-sm-6">
                        <input type="text" class="form-control" v-model="form.coupon" placeholder="Coupon">

                        <span class="help-block" v-show="form.errors.has('coupon')">
                            @{{ form.errors.get('coupon') }}
                        </span>
                    </div>
                </div>

                <!-- Tax / Price Information -->
                <div class="form-group" v-if="spark.collectsEuropeanVat && countryCollectsVat && selectedPlan">

                    <div class="col-md-6">
                        <div class="alert alert-info" style="margin: 0;">
                            <strong>Tax:</strong> @{{ taxAmount(selectedPlan) | currency spark.currencySymbol }}
                            <br><br>
                            <strong>Total Price Including Tax:</strong>
                            @{{ priceWithTax(selectedPlan) | currency spark.currencySymbol }} / @{{ selectedPlan.interval | capitalize }}
                        </div>
                    </div>
                </div>

                <!-- Subscribe Button -->
                <div class="form-group">
                    <div class="col-sm-6 col-sm-offset-4">
                        <button type="submit" class="btn btn-primary" @click.prevent="subscribe" :disabled="form.busy">
                            <span v-if="form.busy">
                                <i class="fa fa-btn fa-spinner fa-spin"></i>Subscribing
                            </span>

                            <span v-else>
                                Subscribe
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</spark-subscribe-stripe>
