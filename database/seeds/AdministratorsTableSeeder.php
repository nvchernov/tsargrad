<?php

namespace Database\Seeders;

use Hash;
use Illuminate\Database\Seeder;
use SleepingOwl\AdminAuth\Entities\Administrator;

class AdministratorsTableSeeder extends Seeder
{

    public function run()
    {
        Administrator::truncate();

        $default = [
            'username' => 'admin',
            'password' => 'admin',
            'name'     => 'Tsargrad Administrator'
        ];

        try
        {
            Administrator::create($default);
        } catch (\Exception $e)
        {
        }
    }

}