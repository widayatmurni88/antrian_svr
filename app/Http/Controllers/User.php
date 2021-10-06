<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use App\Models\User as MUser;

class User extends Controller
{
    public function postCekLogin(Request $req){
        $validate=\Validator::make($req->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validate->fails()) {
            $res = [
                'status' => false,
                'msg' => 'Validator Error!',
                'error' => $validate->errors(),
                'content' => null
            ];
            return response()->json($res, 200);

        } else {
            $cred = request(['email', 'password']);
            $cred = Arr::Add($cred, 'status', 'aktif');

            if (!Auth::attempt($cred)) {
                $res = [
                    'status' => 'error',
                    'msg' => 'Unathorized! Username or Password mismatch',
                    'errors' => null,
                    'content' => null,
                ];
                return response()->json($res, 401);
            }

            $user = MUser::where('email', $req->email)->first();
            if (! \Hash::check($req->password, $user->password)) {
                throw new \Exception("Error in login");
                
            }

            $tokenResult = $user->createToken('token-auth')->plainTextToken;
            $res = [
                'status' => 'success',
                'msg' => 'Login successfully',
                'errors' => null,
                'content' => [
                    'status_code' => 200,
                    'user_id' => $user->id,
                    'access_token' => $tokenResult,
                    'token_type' => 'Bearer',
                ]
            ];
            return response()->json($res, 200);
        }

    }

    public function logout(Request $req){
        $user = $req->user();
        $user->currentAccessToken()->delete();
        $respon = [
            'status' => 'success',
            'msg' => 'Logout successfully',
            'errors' => null,
            'content' => null,
        ];
        return response()->json($respon, 200);
    }

    public function getProfile($id){
        $user = MUser::find($id)->first();
        return response()->json(compact('user'), 200);
    }
}
