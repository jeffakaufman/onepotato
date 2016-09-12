<?php

namespace Laravel\Spark\Http\Controllers\Auth;

use App\Product;

use Laravel\Spark\Spark;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

use Laravel\Spark\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Laravel\Spark\Contracts\Interactions\Settings\Security\VerifyTwoFactorAuthToken as Verify;

class LoginController extends Controller
{
    use AuthenticatesUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new login controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {

        return view('spark::auth.login');
    }

    /**
     * Handle a successful authentication attempt.
     *
     * @param  Request  $request
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return Response
     */
    public function authenticated(Request $request, $user)
    {
        if (Spark::usesTwoFactorAuth() && $user->uses_two_factor_auth) {
            return $this->redirectForTwoFactorAuth($request, $user);
        }
//var_dump($request->session());
//        die();


        $redirectPath = $this->redirectPath();
        if(Spark::admin($user->email)) {
            $redirectPath = '/admin/dashboard';
            return redirect($redirectPath);
        } else {

            if($user->IsSubscribed()) {
                return redirect()->intended($redirectPath);
            } else {
                Auth::guard($this->getGuard())->logout();

                $productAdult = Product::where('sku','0202000000')->first();
                $productFamily1 = Product::where('sku','0202010000')->first();

                $adultDiscount = 0;
                $familyDiscount = 0;

var_dump("XXX");
                $request->session()->put('step1', true);
                $request->session()->put('firstname', $user->first_name);
                $request->session()->put('lastname', $user->last_name);
                $request->session()->put('user_id', $user->id);
                $request->session()->put('zip', $user->billing_zip);
                $request->session()->put('adult_price', $productAdult->cost - $adultDiscount);
                $request->session()->put('family1_price', $productFamily1->cost - $familyDiscount);
var_dump("YYY");

                Redirect::route('register.select_plan');//, array('user' => $user, 'zip' => $user->zip));
var_dump("ZZZ");

                return redirect("/register/select_plan");
            }

        }
    }

    /**
     * Redirect the user for two-factor authentication.
     *
     * @param  Request  $request
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return Response
     */
    protected function redirectForTwoFactorAuth(Request $request, $user)
    {
        Auth::logout();

        // Before we redirect the user to the two-factor token verification screen we will
        // store this user's ID and "remember me" choice in the session so that we will
        // be able to get it back out and log in the correct user after verification.
        $request->session()->put([
            'spark:auth:id' => $user->id,
            'spark:auth:remember' => $request->remember,
        ]);

        return redirect('login/token');
    }

    /**
     * Show the two-factor authentication token form.
     *
     * @param  Request  $request
     * @return Response
     */
    public function showTokenForm(Request $request)
    {
        return $request->session()->has('spark:auth:id')
                        ? view('spark::auth.token') : redirect('login');
    }

    /**
     * Verify the given authentication token.
     *
     * @param  Request  $request
     * @return Response
     */
    public function verifyToken(Request $request)
    {
        $this->validate($request, ['token' => 'required']);

        // If there is no authentication ID stored in the session, it means that the user
        // hasn't made it through the login screen so we'll just redirect them back to
        // the login view. They must have hit the route manually via a specific URL.
        if (! $request->session()->has('spark:auth:id')) {
            return redirect('login');
        }

        $user = Spark::user()->findOrFail(
            $request->session()->pull('spark:auth:id')
        );

        // Next, we'll verify the actual token with our two-factor authentication service
        // to see if the token is valid. If it is, we can login the user and send them
        // to their intended location within the protected part of this application.
        if (Spark::interact(Verify::class, [$user, $request->token])) {
            Auth::login($user, $request->session()->pull(
                'spark:auth:remember', false
            ));

            return redirect()->intended($this->redirectPath());
        } else {
            return back();
        }
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        Auth::guard($this->getGuard())->logout();

        session()->flush();

        return redirect(
            property_exists($this, 'redirectAfterLogout')
                    ? $this->redirectAfterLogout : '/'
        );
    }

    protected $redirectAfterLogout = '/home';
}
