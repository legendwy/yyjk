<?php

use App\User;
use Illuminate\Database\Seeder;

class AdminsTableSeeder extends Seeder
{
    /**
     *
     * 前台用户填充数据（不是后台！！！！注意注意）
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'test',
            'phone' => '10000000000',
            'email' => 'si@gmail.com',
            'password' => bcrypt('123456')
        ]);
        User::create([
            'name' => 'test1',
            'phone' => '10000000001',
            'email' => 'si1@gmail.com',
            'password' => bcrypt('123456')
        ]);
        User::create([
            'name' => 'test2',
            'phone' => '10000000002',
            'email' => 'si2@gmail.com',
            'password' => bcrypt('123456')
        ]);
        User::create([
            'name' => 'test3',
            'phone' => '10000000003',
            'email' => 'si3@gmail.com',
            'password' => bcrypt('123456')
        ]);
        User::create([
            'name' => 'test4',
            'phone' => '10000000004',
            'email' => 'si4@gmail.com',
            'password' => bcrypt('123456')
        ]);
    }
}
