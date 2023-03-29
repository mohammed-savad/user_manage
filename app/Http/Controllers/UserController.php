<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\returnSelf;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $rules = [
            'name' => 'required',
            'email'    => 'unique:users|required',
            'phone' => 'required|digits:10',
            'address' => 'required',
            'password' => 'required|min:6',
            'confirm_password' => 'same:password|min:6'
        ];
    
        $input     = $request->only('name', 'email','password','phone', 'address','city','state','country');
        $validator = Validator::make($input, $rules);
    
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()]);
        }
       
        $data = [
            "name" => $request->name,
            "email"    => $request->email,
            "password" => Hash::make($request->password),
            "phone" => $request->phone,
            "address" => $request->address,
            "city" => $request->city,
            "state" => $request->state,
            "country" => $request->country,
        ];

        User::create($data);
        return($data);
    
    }

    public function login (Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|min:6',
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $data = User::first();
                return response($data, 200);
            } else {
                $response = ["message" => "Password mismatch"];
                return response($response, 422);
            }
        } else {
            $response = ["message" =>'User does not exist'];
            return response($response, 422);
            
        }
    }

}
