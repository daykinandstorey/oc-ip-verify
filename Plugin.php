<?php

namespace Daykin\Ipverify;

use Backend\Facades\BackendAuth;
use Backend\Models\User;
use Daykin\Ipverify\Models\Settings;
use Daykin\Ipverify\Models\UserIp;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use October\Rain\Support\Facades\Flash;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public $elevated = true;

    public function pluginDetails()
    {
        return [
            'name' => Lang::get('daykin.ipverify::lang.plugin.name'),
            'description' => Lang::get('daykin.ipverify::lang.plugin.description'),
            'author' => 'Daykin & Storey',
            'icon' => 'icon-lock',
        ];
    }

    public function registerSettings()
    {
        return [
            'ip_verification' => [
                'label' => Lang::get('daykin.ipverify::lang.plugin.name'),
                'description' => Lang::get('daykin.ipverify::lang.plugin.menu_description'),
                'category' => 'system::lang.system.categories.system',
                'icon' => 'icon-lock',
                'class' => '\Daykin\Ipverify\Models\Settings',
                'order' => 100,
                'keywords' => 'ip'
            ]
        ];
    }

    public function boot()
    {
        if (Settings::get('on', false)) {
            $this->checkIpIsVerifiedBeforeLogin();
        }

        // Extend the backend user
        User::extend(function (User $user) {
            // Declare the relationship between backend users and verified IPs
            $user->hasMany += [
                'verifiedIps' => [UserIp::class, 'key' => 'backend_user_id'],
            ];

            // Check to see if an optionally passed or the current IP address has been verified
            $user->addDynamicMethod('ipIsVerified', function ($ip = null) use ($user) {
                $ip = $ip ?? request()->ip();

                return !! $user->verifiedIps()->where('ip', '=', $ip)->whereNotNull('verified_at')->count();
            });
        });
    }

    public function registerMailTemplates()
    {
        return [
            'daykin.ipverify::mail.verification' => 'Email verification sent to users logging in from a new IP',
        ];
    }

    /**
     * Use the backend user login event to inject functionality
     */
    protected function checkIpIsVerifiedBeforeLogin()
    {
        Event::listen('backend.user.login', function (User $user) {
            // Skip if already verified against the current IP
            if ($user->ipIsVerified()) {
                return true;
            }

            // Send the verification email
            UserIp::sendVerificationRequest($user);

            // Flash an error
            Flash::error(Lang::get('daykin.ipverify::lang.errors.not_verified'));

            // Logout the user
            BackendAuth::logout();

            // Redirect back
            return Redirect::back();
        });
    }
}
