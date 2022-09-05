<?php

namespace App\Token;
use Slim\Csrf\Guard;


class GenToken
{
    public function genCSRFTkn()
    {
        /* Init Response */
        $token = ["csrf_name" => "", "csrf_value" => ""];

        $slimGuard = new Guard;

        // $slimGuard->validateStorage();
        $csrfNameKey = $slimGuard->getTokenNameKey();
        $csrfValueKey = $slimGuard->getTokenValueKey();
        $keyPair = $slimGuard->generateToken();
        $token["csrf_name"] = $keyPair[$csrfNameKey];
        $token["csrf_value"] = $keyPair[$csrfValueKey];

        // print_r($token);
        // print_r($token['csrf_value']);
        // return $token;
        return $token['csrf_value'];
    }
}


?>