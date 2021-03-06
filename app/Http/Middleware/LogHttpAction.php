<?php

namespace App\Http\Middleware;

use Closure;
use \App\SimpleLogger;
use \App\User;

class LogHttpAction
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

//        echo $request->path();
//        return $response;

        $now = new \DateTime('now');

        $fileName = "register_{$now->format('Ymd')}.log";

        $logger = new SimpleLogger($fileName, false);
        $logger->Log("[{$now->format('m/d/Y H:i:s')}] [{$request->ip()}] [{$request->header('User-Agent')}]");

        if($request->user_id && ($user = User::find($request->user_id))) {
            $logger->Log("    #{$user->id} [{$user->email}] {$user->first_name} {$user->last_name}");
        }

        $logger->Log("    {$request->method()} /{$request->path()} [{$this->_getActionDescription($request->method(), $request->path())}]");
        $logger->Log("");

        return $response;
    }

    private function _getActionDescription($method, $path) {

        $response = '';

        $explodedPath = explode('/', $path);
        if('refer' == $explodedPath[0]) {
            $path = 'refer';
        } elseif('register' == $explodedPath[0]) {
            if(isset($explodedPath[1])) {
                if(is_numeric($explodedPath[1])) {
                    $path = "register";
                }
            }
        }




        switch(strtoupper($method)) {
            case 'GET':
                switch($path) {
                    case 'join':
                    case 'register':
                        $response = "Display register form (step 1)";
                        break;

                    case 'refer':
                        $response = "Read Refer link";
                        break;

                    case 'register/select_plan':
                        $response = "Display Select Plan form (step 2)";
                        break;

                    case 'register/preferences':
                        $response = "Display Preferences form (step 3)";
                        break;

                    case 'register/delivery':
                        $response = "Display Delivery form (step 4)";
                        break;

                    case 'register/payment':
                        $response = "Display Payment form (step 5)";
                        break;

                    case 'congrats':
                        $response = "Display Congratulations Form (FINAL step)";
                        break;

                    default:
                        //Do Nothing
                        break;
                }
                break;

            case 'POST':

                switch($path) {
                    case 'register':
                        $response = "Initial form submitted (step 1)";
                        break;

                    case 'register/select_plan':
                        $response = "Select Plan form submitted (step 2)";
                        break;

                    case 'register/preferences':
                        $response = "Select Preferences form submitted (step 3)";
                        break;

                    case 'register/delivery':
                        $response = "Select Delivery form submitted (step 4)";
                        break;

                    case 'register/payment':
                        $response = "Select Payment form submitted (step 5)";
                        break;

                    case 'register/waiting_list':
                        $response = "Submit for waiting list (unsupported zip code)";
                        break;

                    default:
                        //Do Nothing
                        break;

                }
                break;

            default:
                //Do nothing
                break;
        }

        return $response;
    }
}


/*
    Route::get('/join', 'NewUserController@DisplayUserForm');
    Route::get('/register', 'NewUserController@DisplayUserForm');
    Route::get('/register/{referralId}', 'NewUserController@DisplayUserForm');
    Route::get('/refer/{hash}', array('uses' => 'NewUserController@ReadReferralHash', 'as' => 'shared.referral.link'));
    Route::post('/register', 'NewUserController@RecordNewuser');
    Route::post('/register/select_plan', 'NewUserController@RecordPlan');
    Route::post('/register/preferences', 'NewUserController@RecordPlanPreferences');
    Route::post('/register/delivery', 'NewUserController@RecordDeliveryPreferences');
    Route::post('/register/payment', 'NewUserController@RecordPayment');
    Route::post('/register/waiting_list', 'NewUserController@SubscribeToWaitingList');

    Route::get('/register/select_plan', array('as' => 'register.select_plan', function () {
        return view('register.select_plan');
    }));
    Route::get('/register/preferences', array('as' => 'register.preferences', function () {
        return view('register.preferences');
    }));
    Route::get('/register/delivery', array('as' => 'register.delivery', function () {
        return view('register.delivery');
    }));
    Route::get('/register/payment', array('as' => 'register.payment', function () {
        return view('register.payment');
    }));
    Route::get('/congrats', array('as' => 'register.congrats', function () {
        return view('register.congrats');
    }));

 */