<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user= User::whereEmail($request->email)->first();
//            dd($request->all());
//                dd($user->toArray());
        // generamos el token
        $plainTextToken= $user->createToken($request->device_name)->plainTextToken;
        return response()->json([
            'plain-text-token'=> $plainTextToken
        ]);

    }
}
