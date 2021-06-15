<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    /**
     * undocumented function
     *
     * @return void
     */
    public function __invoke()
    {
        auth()->user()->tokens()->delete();
        return response([
            'message' => 'Anda berhasil logout'
        ]);
    }
}
