<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Client;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::all();
        return $this->responseOk($clients);
        }


    public function show($id)
    {
        $client = Client::find($id);
        return $this->responseOk($client);
    }
    
    public function store(Request $request)
    {
        $requestData = $request->all();
        $validator = Validator::make($requestData, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->responseError($validator->errors()->array());
        }

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
            'address' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->responseError($validator->errors()->array());
        }

        $client = Client::find($id);
        $client->update($requestData);
        return $this->responseOk($client);
    }
    


    public function destroy($id)
    {
        $client = Client::find($id);
        $client->delete();
        return $this->responseOk($client);

    }

    
}

