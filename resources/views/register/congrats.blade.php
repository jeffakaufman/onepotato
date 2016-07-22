@extends('spark::layouts.app')

@section('content')
<delivery :user="user" inline-template>
    <div class="container">
        <!-- Application Dashboard -->
	
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading with-subtitle">
                        <h1>Congratulations!
                            <div class="panel-subtitle">Your first box will arrive on May 18.</div>
                        </h1>
                        <!-- Login Button -->
                        <button type="submit" class="btn btn-primary" onclick="location.href='/whats-cooking';">
                            See What's Cooking
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default panel-form">

                    <div class="panel-heading text-left extrapadding">Login Information
                        <div class="panel-subtitle">Please use your email address to log into our site to manage your account. You will also need to create a password.</div>
                    </div>
                    <div class="panel-body font16 extrapadding text-center">

                            

                    </div>

                </div>

            </div>
        </div> -->
    </div>
</delivery>
@endsection
