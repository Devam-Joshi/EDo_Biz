<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Users_Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $keys = collect([
            'name',
            'username',
            'password',
            'email',
            'type',
        ]);
        $values = [
            [
                'admin',
                'admin',
                '$2y$10$xSE0bKpmdgEHEe0rjpudB.3NiTFBnHqOW/0/EvvKpaY7Fx5nldgfq',
                'admin@gmail.com',
                'admin'
            ]
        ];


        foreach ($values as $key => $value) {
            $data = $keys->combine($value);
            DB::table('users')->insert($data->all());
        }

    }
}
