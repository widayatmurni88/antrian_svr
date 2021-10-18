<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
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

    public function getUsers(){
        $users = MUser::get(['id', 'name', 'email', 'status']);
        return response()->json(compact('users'), 200);
    }

    public function addUser(Request $req){
        $valid = $req->validate([
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required'
        ]);
        
        $user = new MUser();
        $user->name = $req->name;
        $user->email = $req->email;
        $user->password = bcrypt($req->password);
        $user->save();

        $response = [
            'status' => true,
            'msg' => 'Success',
            'content' => $user
        ];
        return response()->json(compact('response'), 200);

    }

    public function editUser(Request $req){

        $valid = $req->validate([
            'name' => 'required',
            'email' => 'required',
        ]);

        DB::table('users')->where('id', $req->id)
            ->update(['name' => $req->name,
                        'email' => $req->email]);

        $user = MUser::find($req->id);

        if ($user) {

            $response = [
                'status' => true,
                'msg' => 'Success',
                'content' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'status' => $user->status
                ]
            ];

            return response()->json(compact('response'), 200);
        }
    }

    public function resetPassword(Request $req){
        $user = MUser::find($req->id);
        $user->password = bcrypt('1234567');
        $user->save();

        $response = [
            'status' => true,
            'msg' => 'Success',
        ];

        return response()->json(compact('response'), 200);
    }

    public function removeUser(Request $req){
        $user = MUser::find($req->id);
        $user->delete();
        $response = [
            'status' => true,
            'msg' => 'Success',
        ];
        return response()->json(compact(''), 200);
    }

    public function superLogin(Request $req){
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
            $cred = Arr::Add($cred, 'role', true);

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
}
