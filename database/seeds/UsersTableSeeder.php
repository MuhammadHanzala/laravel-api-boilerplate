<?php

use Illuminate\Database\Seeder;

use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        User::create([
            "name" => "ahab",
            "email" => "abdul@y.com",
            "password" => bcrypt("123456"),
            "isVerified" => 1
        ]);
        User::create([
            "name" => "Aqeel Ahmed",
            "email" => "aqi@yopmail.com",
            "password" => bcrypt("123456"),
            "isVerified" => 1
        ]);
        User::create([
            "name" => "Kumail Abbas",
            "email" => "kumail.abbas@mujadidia.com",
            "password" => bcrypt("supernova"),
            "isVerified" => 1
        ]);
    }
}
