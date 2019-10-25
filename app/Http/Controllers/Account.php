<?php

namespace App\Http\Controllers;

use App\Models\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class Account extends Controller
{
    public function __construct() {
      parent::__construct();
    }

    // public function register(Request $req) {
    //     $req->merge(['password' => Hash::make($req->password)]);
    //     $account = Account::create($req->only('username','password'));
    //     return $this->response->sendToken($account);
    // }

    public function login(Request $req) {
        $account = Auth::where('username', $req->username)->first();

        if ($account) {
            return $this->response->success($account);
        }

        return $this->response->forbidden('username/password salah');
    }

}
