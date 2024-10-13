<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class General_Setting_Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $keys = collect([
            'label',
            'unique_name',
            'type',
            'value',
            'options',
            'extra',
            'hint',
        ]);
        $values = [
            [
                'Site Name',
                'site_name',
                'text',
                'name',
                null,
                json_encode([
                    'required' => 'required',
                ]),
                'Please Enter Site name',
            ],
            [
                'Site Logo',
                'site_logo',
                'file',
                '',
                null,
                json_encode([
                    'accept' => "image/*",
                ]),
                'Site Logo Main'
            ],
            [
                'Small Site Logo',
                'small_site_logo',
                'file',
                '',
                null,
                json_encode([
                    'accept' => "image/*",
                ]),
                'Site Small Logo Main'
            ],
            [
                'Fav Icon',
                'Favicon',
                'file',
                '',
                null,
                json_encode([
                    'accept' => "image/*",
                ]),
                'Fav Icon for Site'
            ],
            [
                'Footer Text',
                'footer_text',
                'textarea',
                'Footer Text',
                null,
                json_encode([
                    'maxlength' => "255",
                    'required' => 'required',
                ]),
                'Please Enter Site footer text'
            ],
            [
                'Support Email',
                'ADMIN_EMAIL',
                'email',
                'admin@gmail.com',
                null,
                json_encode([
                    'maxlength' => "255",
                    'required' => 'required',
                ]),
                'Please Enter Email Address For Admin'
            ],
            [
                'Support Mobile Number',
                'mobile_number',
                'text',
                '+1 000 0000 000',
                null,
                null,
                'Please enter mobile number'
            ],
            [
                'Android Version',
                'Android_Version',
                'number',
                '1',
                null,
                json_encode([
                    'step' => "0.01",
                    'required' => 'required',
                    'min' => 1,
                ]),
                'Please Enter Android Current Version'
            ],
            [
                'Android Force Update',
                'Android_Force_Update',
                'select',
                '0',
                json_encode([
                    ['name' => 'Yes', 'value' => 1],
                    ['name' => 'No', 'value' => 0],
                ]),
                null,
                'is android update is forced'
            ],
            [
                'Ios Version',
                'IOS_Version',
                'number',
                '1',
                null,
                json_encode([
                    'step' => "0.01",
                    'required' => 'required',
                    'min' => 1,
                ]),
                'Please Enter Ios Current Version'
            ],
            [
                'Ios Force Update',
                'IOS_Force_Update',
                'select',
                '0',
                json_encode([
                    ['name' => 'Yes', 'value' => 1],
                    ['name' => 'No', 'value' => 0],
                ]),
                null,
                'is Ios update is forced'
            ],
            [
                'Daily Question Count',
                'Daily_Question_Count',
                'number',
                '5',
                null,
                null,
                'Please Enter Daily Question Count'
            ],
        ];
        foreach ($values as $value) {
            $data = $keys->combine($value);
            DB::table('general_settings')->insert($data->all());
        }

    }
}
