<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\User;
class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return $this->responseOk($users);
    }
    
    public function show($id)
    {
        $user = User::find($id);
        return $this->responseOk($user);
    }
    
    public function store(Request $request)
    {
        $requestData = $request->all();
        $validator = Validator::make($requestData, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:admin,user',
            'status' => 'required|string|in:active,inactive',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->responseError($validator->errors()->array());
        }

        $user = User::create($requestData);
        return $this->responseOk($user);
    }
    
    public function update(Request $request, $id)
    {
        $requestData = $request->all();
        $validator = Validator::make($requestData, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|string|in:admin,user',
            'status' => 'required|string|in:active,inactive',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]); 

        if ($validator->fails()) {
            return $this->responseError($validator->errors()->array());
        }

        $user = User::find($id);
        $user->update($requestData);
        return $this->responseOk($user);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        return $this->responseOk($user);
    }
}
