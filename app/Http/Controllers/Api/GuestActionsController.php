<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;


/**
 * Controller for calls that can be performed for an unauthenticated user
 */
class GuestActionsController extends Controller
{
    public function getVersion()
    {
        return config('ipeer.version');
    }

    /**
     * Create a new user.
     *
     * TODO: disable later, as this is mostly just a demo method
     */
    public function registerUser(Request $request)
    {
        $data = $request->validate([
            'username' => 'required',
            'password' => 'required',
            'name' => 'nullable',
            'email' => 'nullable|email',
        ]);
        $user = new User;
        $user->username = $data['username'];
        $user->password = $data['password'];
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->save();
        return $user;
    }
}
