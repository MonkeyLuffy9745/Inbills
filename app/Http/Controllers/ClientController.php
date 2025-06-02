<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\Client;

class ClientController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $clients = $user->clients()->get();
        return $this->responseOk($clients);
        }


    public function show($id)
    {
        $client = Client::find($id);
        if($client->user_id !== Auth::user()->id){
            return $this->responseError('Unauthorized');
        }
        return $this->responseOk($client);
    }
    
    public function store(Request $request)
    {
        $requestData = $request->all();
        $validator = Validator::make($requestData, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return $this->responseError($validator->errors()->array());
        }
        
        $requestData['user_id'] = $request->user()->id;

        $client = Client::create($requestData);
        return $this->responseOk($client);
    }


    public function update(Request $request, $id)
    {
        $requestData = $request->all();
        $validator = Validator::make($requestData, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return $this->responseError($validator->errors()->array());
        }

        $requestData['user_id'] = $request->user()->id;

        $client = Client::find($id);
        if($client->user_id !== Auth::user()->id){
            return $this->responseError('Unauthorized');
        }
        $client->update($requestData);
        return $this->responseOk($client);
    }
    


    public function destroy($id)
    {
        $client = Client::find($id);
        if($client->user_id !== Auth::user()->id){
            return $this->responseError('Unauthorized');
        }
        $client->delete();
        return $this->responseOk($client);

    }

    
}

