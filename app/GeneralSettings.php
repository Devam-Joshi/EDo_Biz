<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeneralSettings extends Model
{

    protected $guarded = [];

    public static function define_const()
    {
        $all_settings = GeneralSettings::where('status', 'active')->get(['unique_name', 'value', 'type']);
        foreach ($all_settings as $key => $value) {
            if (!defined($value['unique_name'])) {
                define($value['unique_name'], GeneralSettings::CheckType($value['type'], $value['value']));
            }
        }
    }

    public static function CheckType($type = "", $val = "")
    {
        return ($type == "file") ? asset($val) : $val;
    }

    public function getValueAttribute($val)
    {
        return ($this->type == "file") ? asset($val) : $val;
    }


}
