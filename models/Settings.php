<?php

namespace Daykin\Ipverify\Models;

use October\Rain\Database\Model;

class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    public $settingsCode = 'ip_verification';

    public $settingsFields = 'fields.yaml';
}
