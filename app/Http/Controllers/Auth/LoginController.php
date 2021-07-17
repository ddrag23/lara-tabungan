<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'username' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Masukkan data dengan benar',
                'errors' => $validator->errors()
            ]);
        }
        $user = User::where('username',$request->username)->first();
        if (!$user || !Hash::check($request->password,$user->password)) {
            return response(['message' => 'Username atau password yang anda masukkan salah', 'success' => false],200);
        }

        $token = $user->createToken('ApiToken')->plainTextToken;

        return response([
            'success' => true,
            'user' => $user,
            'token' => $token
        ],201);
    }
}
