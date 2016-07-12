<!-- Name -->
<div class="form-group" :class="{'has-error': registerForm.errors.has('name')}">

    <div class="col-md-6">
        <input type="name" class="form-control" name="name" v-model="registerForm.name" placeholder="Name" autofocus>

        <span class="help-block" v-show="registerForm.errors.has('name')">
            @{{ registerForm.errors.get('name') }}
        </span>
    </div>
</div>

<!-- Address -->
<div class="form-group" :class="{'has-error': registerForm.errors.has('address')}">

    <div class="col-sm-6">
        <input type="text" class="form-control" v-model="registerForm.address" lazy placeholder="Address">

        <span class="help-block" v-show="registerForm.errors.has('address')">
            @{{ registerForm.errors.get('address') }}
        </span>
    </div>
</div>

<!-- Address Line 2 -->
<div class="form-group" :class="{'has-error': registerForm.errors.has('address_line_2')}">

    <div class="col-sm-6">
        <input type="text" class="form-control" v-model="registerForm.address_line_2" lazy placeholder="Address Line 2">

        <span class="help-block" v-show="registerForm.errors.has('address_line_2')">
            @{{ registerForm.errors.get('address_line_2') }}
        </span>
    </div>
</div>

<!-- City -->
<div class="form-group" :class="{'has-error': registerForm.errors.has('city')}">

    <div class="col-sm-6">
        <input type="text" class="form-control" v-model="registerForm.city" lazy placeholder="City">

        <span class="help-block" v-show="registerForm.errors.has('city')">
            @{{ registerForm.errors.get('city') }}
        </span>
    </div>
</div>

<!-- State & ZIP Code -->
<div class="form-group" :class="{'has-error': registerForm.errors.has('state')}">

    <!-- State -->
    <div class="col-sm-3">
        <input type="text" class="form-control" placeholder="State" v-model="registerForm.state" lazy>

        <span class="help-block" v-show="registerForm.errors.has('state')">
            @{{ registerForm.errors.get('state') }}
        </span>
    </div>

    <!-- Zip Code -->
    <div class="col-sm-3">
        <input type="text" class="form-control" placeholder="Postal Code" v-model="registerForm.zip" lazy>

        <span class="help-block" v-show="registerForm.errors.has('zip')">
            @{{ registerForm.errors.get('zip') }}
        </span>
    </div>
</div>

<!-- Country -->
<div class="form-group" :class="{'has-error': registerForm.errors.has('country')}">

    <div class="col-sm-6">
        <select class="form-control" v-model="registerForm.country" lazy placeholder="Country">
            @foreach (app(Laravel\Spark\Repositories\Geography\CountryRepository::class)->all() as $key => $country)
                <option value="{{ $key }}">{{ $country }}</option>
            @endforeach
        </select>

        <span class="help-block" v-show="registerForm.errors.has('country')">
            @{{ registerForm.errors.get('country') }}
        </span>
    </div>
</div>

<!-- European VAT ID -->
<div class="form-group" :class="{'has-error': registerForm.errors.has('vat_id')}" v-if="countryCollectsVat">

    <div class="col-sm-6">
        <input type="text" class="form-control" v-model="registerForm.vat_id" lazy placeholder="VAT ID">

        <span class="help-block" v-show="registerForm.errors.has('vat_id')">
            @{{ registerForm.errors.get('vat_id') }}
        </span>
    </div>
</div>
