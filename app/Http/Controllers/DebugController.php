<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;

class DebugController extends Controller
{
    public function testFactory()
    {
        $articles = factory(\App\Article::class, 30)->make();
        var_dump($articles);
        exit;
    }
    
    public function createAdminUser()
    {
        User::create([
            'name' => 'admin',
            'email' => 'admin@laravel-test.api',
            'password' => Hash::make('admin')
        ]);
    }
    
    public function middlewarePriorities(Request $request)
    {
        dump($request->all());
    }
}
