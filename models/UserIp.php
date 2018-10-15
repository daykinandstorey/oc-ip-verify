<?php

namespace Daykin\Ipverify\Models;

use Backend\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use October\Rain\Database\Model;

class UserIp extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    public $table = 'daykin_ipverify_user_ips';

    public $belongsTo = [
        'backend_user' => User::class,
    ];

    /**
     * Lookup by token
     * @param $token
     * @return UserIp|false
     */
    public static function byToken($token)
    {
        return UserIp::where('token', '=', $token)->first();
    }

    /**
     * Create a new token assigned to a backend user then email the verification request to that user
     * @param User $user
     * @param null $ip
     */
    public static function sendVerificationRequest(User $user, $ip = null)
    {
        // Update or create the record with a new token
        $ip = UserIp::updateOrCreate([
            'backend_user_id' => $user->id,
            'ip' => $ip ?? request()->ip(),
        ], [
            'token' => self::generateToken()
        ]);

        $name = $user->full_name;
        $link = url('_verify-ip', [$ip->token]);
        $template = 'daykin.ipverify::mail.verification';

        Mail::send($template, compact('name', 'link'), function ($message) use ($user) {
            $message->to($user->email);
        });
    }

    /**
     * Verify an unused token
     * @param $token
     * @return bool
     */
    public static function verifyToken($token)
    {
        return !! UserIp::whereNotNull('token')->where('token', '=', $token)->whereNull('verified_at')->count();
    }

    /**
     * Set the record as verified
     */
    public function verify()
    {
        $this->update(['verified_at' => $this->freshTimestamp()]);

        Event::fire('daykin.ipverify.ip_verified');
    }

    /**
     * Return a new token
     * @return string
     */
    public static function generateToken()
    {
        return Str::random(128);
    }
}
