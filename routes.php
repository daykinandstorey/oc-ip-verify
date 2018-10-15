<?php

use Daykin\Ipverify\Models\UserIp;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use October\Rain\Support\Facades\Config;
use October\Rain\Support\Facades\Flash;

Route::get('/_verify-ip/{token}', function ($token) {
    $redirectUrl = Config::get('cms.backendUri', 'backend');

    if (!UserIp::verifyToken($token)) {
        Flash::error(Lang::get('daykin.ipverify::lang.errors.token'));

        return Redirect::to($redirectUrl);
    }

    UserIp::byToken($token)->verify();

    Flash::success(Lang::get('daykin.ipverify::lang.flash.verified'));

    return Redirect::to($redirectUrl);
})->middleware('web');