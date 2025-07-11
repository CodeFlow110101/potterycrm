<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    static function upsert($phoneno, $credentials)
    {
        $user = User::firstOrCreate(['phoneno' => $phoneno], $credentials->all());
        $user->wasRecentlyCreated || $user->update($credentials->except(['role_id', 'password'])->all());

        return $user;
    }
}
