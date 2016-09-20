<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cancellation extends Model {

	protected $table = 'cancellations';
	public $timestamps = true;

    const CRYPT_KEY = "OneP0T@T0_YAMMY!";

    public static function GenerateCancelLink(User $user, $validTo = "+1 day") {
        $validToDate = new \DateTime($validTo);

        $domain = self::_getAppDomain();

        $uri = "/account/cancel/";


        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
//        $cryptString = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, self::CRYPT_KEY, $jsonString, MCRYPT_MODE_ECB, $iv);
//
//        $encodedString = base64_encode($cryptString);

        $data = new \stdClass();
        $data->junk1 = rand(1000, 1000000);
        $data->userId = $user->id;
        $data->junk2 = rand(1000, 1000000);
        $data->userEmail = $user->email;
        $data->junk3 = rand(1000, 1000000);
        $data->validTo = $validToDate->format("Y-m-d H:i:s");
        $data->junk4 = rand(1000, 1000000);

        $code = rawurlencode(
            base64_encode(
                mcrypt_encrypt(
                    MCRYPT_RIJNDAEL_256,
                    self::CRYPT_KEY,
                    json_encode($data),
                    MCRYPT_MODE_ECB,
                    $iv
                )
            )
        );

        return route("cancel.account.link", ['code'=>$code]);

    }

    private static function _getAppDomain() {

        $parsed = parse_url( url()->current() );

        return $parsed['scheme'].'://'.$parsed['host'];

    }

}