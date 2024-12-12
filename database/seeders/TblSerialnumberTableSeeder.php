<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TblSerialnumberTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('tbl_serialnumber')->delete();
        
        \DB::table('tbl_serialnumber')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'sale_invoice',
                'prefix' => 'INV',
                'length' => '7',
                'financialYear' => '2324',
                'next_number' => 3079,
                'type' => 'transaction',
                'created_at' => '2020-02-27 05:30:00',
                'updated_at' => '2024-10-12 15:58:28',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'product_code',
                'prefix' => 'PD',
                'length' => '4',
                'financialYear' => '',
                'next_number' => 5001,
                'type' => 'master',
                'created_at' => '2020-02-27 05:30:00',
                'updated_at' => '2024-10-10 14:50:09',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'purchase_invoice',
                'prefix' => 'PUR',
                'length' => '7',
                'financialYear' => '2324',
                'next_number' => 2152,
                'type' => 'transaction',
                'created_at' => '2020-02-27 05:30:00',
                'updated_at' => '2024-10-11 15:54:16',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'account_code',
                'prefix' => 'AC',
                'length' => '4',
                'financialYear' => '',
                'next_number' => 2996,
                'type' => 'master',
                'created_at' => '2020-02-27 05:30:00',
                'updated_at' => '2024-10-12 15:22:10',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'category_code',
                'prefix' => 'PC',
                'length' => '3',
                'financialYear' => '',
                'next_number' => 375,
                'type' => 'master',
                'created_at' => '2020-02-27 05:30:00',
                'updated_at' => '2024-10-10 11:52:46',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'payment_receipt',
                'prefix' => 'FRC',
                'length' => '7',
                'financialYear' => '2324',
                'next_number' => 6086,
                'type' => 'transaction',
                'created_at' => '2020-02-27 05:30:00',
                'updated_at' => '2024-10-11 15:34:45',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'sale_return',
                'prefix' => 'SRT',
                'length' => '5',
                'financialYear' => '2324',
                'next_number' => 27,
                'type' => 'transaction',
                'created_at' => '2020-02-27 05:30:00',
                'updated_at' => '2024-10-11 15:15:06',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'purchase_return',
                'prefix' => 'PRT',
                'length' => '5',
                'financialYear' => '2324',
                'next_number' => 37,
                'type' => 'transaction',
                'created_at' => '2020-02-27 05:30:00',
                'updated_at' => '2024-10-11 15:21:47',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'color_code',
                'prefix' => 'CL',
                'length' => '3',
                'financialYear' => '',
                'next_number' => 139,
                'type' => 'master',
                'created_at' => '2020-02-27 05:30:00',
                'updated_at' => '2024-07-23 06:10:18',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'sale_order',
                'prefix' => 'SOD',
                'length' => '7',
                'financialYear' => '2324',
                'next_number' => 2507,
                'type' => 'transaction',
                'created_at' => '2020-02-27 05:30:00',
                'updated_at' => '2024-10-12 15:38:36',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'purchase_order',
                'prefix' => 'POD',
                'length' => '7',
                'financialYear' => '2324',
                'next_number' => 1927,
                'type' => 'transaction',
                'created_at' => '2020-02-27 05:30:00',
                'updated_at' => '2024-10-10 18:23:32',
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'account_group',
                'prefix' => 'AG',
                'length' => '3',
                'financialYear' => '',
                'next_number' => 9,
                'type' => 'master',
                'created_at' => '2020-02-27 05:30:00',
                'updated_at' => '2020-07-31 03:22:57',
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'transfer_receipt',
                'prefix' => 'TFR',
                'length' => '7',
                'financialYear' => '2324',
                'next_number' => 1072,
                'type' => 'transaction',
                'created_at' => '2020-02-27 05:30:00',
                'updated_at' => '2024-10-11 17:12:59',
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'sale_inquery',
                'prefix' => 'SIQ',
                'length' => '7',
                'financialYear' => '2324',
                'next_number' => 1923,
                'type' => 'transaction',
                'created_at' => '2020-02-27 05:30:00',
                'updated_at' => '2024-10-12 15:24:24',
            ),
            14 => 
            array (
                'id' => 15,
                'name' => 'sale_requisition',
                'prefix' => 'RSO',
                'length' => '7',
                'financialYear' => '2324',
                'next_number' => 213,
                'type' => 'transaction',
                'created_at' => '2020-02-27 05:30:00',
                'updated_at' => '2024-10-02 13:07:34',
            ),
            15 => 
            array (
                'id' => 16,
                'name' => 'purchase_requisition',
                'prefix' => 'RPO',
                'length' => '7',
                'financialYear' => '2324',
                'next_number' => 258,
                'type' => 'transaction',
                'created_at' => '2020-02-27 05:30:00',
                'updated_at' => '2024-10-11 00:34:56',
            ),
            16 => 
            array (
                'id' => 17,
                'name' => 'Cart',
                'prefix' => 'CRT',
                'length' => '7',
                'financialYear' => '2324',
                'next_number' => 4392,
                'type' => 'transaction',
                'created_at' => '2020-02-27 05:30:00',
                'updated_at' => '2024-10-12 15:33:49',
            ),
            17 => 
            array (
                'id' => 18,
                'name' => 'gift_code',
                'prefix' => 'GFT',
                'length' => '4',
                'financialYear' => '',
                'next_number' => 9,
                'type' => 'master',
                'created_at' => '2020-02-27 05:30:00',
                'updated_at' => '2023-12-25 07:23:27',
            ),
        ));
        
        
    }
}