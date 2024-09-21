<?php

function IsRegistrationAllowed()
{
    $setting = Illuminate\Database\Capsule\Manager::table('tblconfiguration')->where('setting', 'AllowClientRegister')->first();

    if ($setting->value == 'on') {
        return true;
    } else {
        return false;
    }
}

function GetRealIP()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    return $ip;
}