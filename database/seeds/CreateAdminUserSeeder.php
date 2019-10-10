<?php

use Illuminate\Database\Seeder;
use App\User;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::where('name', 'admin')->get();
        if (0 !== sizeof($admin)) {return;}
        
        User::create([
            'name' => 'admin',
            'email' => 'admin@laravel-api.test',
            'password' => \Illuminate\Support\Facades\Hash::make('admin')
        ]);
    }
}
