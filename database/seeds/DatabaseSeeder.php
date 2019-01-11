<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * ApiPapaya182182
     * @return void
     */
    public function run()
    {
        \App\Models\User::create([
            'id' => 1,
            'role' => 'super',
            'name' => 'Super Administrador',
            'email' => 'super@gmail.com',
            'username' => 'super',
            'password' => bcrypt('123456'),
            'remember_token' => str_random(10),
        ]);
        $this->call([ErrorTableSeeder::class]);
    }
}
