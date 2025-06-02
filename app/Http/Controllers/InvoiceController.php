<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\User;
use App\Models\InvoiceCounter;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $invoices = $user->invoices()->with('client', 'invoiceItems')->get();
        return $this->responseOk($invoices);
    }
    
    
    public function show($id)
    {
        $invoice = Invoice::with('invoiceItems')->find($id);
        if($invoice->user_id !== Auth::user()->id){
            return $this->responseError('Unauthorized');
        }
        return $this->responseOk($invoice);
    }
    
    protected function generateInvoiceNumber($userId)
    {
        $year = now()->year;
    
        $counter = InvoiceCounter::firstOrCreate(
            ['user_id' => $userId, 'year' => $year],
            ['counter' => 0]
        );
    
        $counter->increment('counter');
        $counter->refresh();
    
        $padded = str_pad($counter->counter, 4, '0', STR_PAD_LEFT);
    
        return "INV-{$year}-{$padded}";
    }
    
    public function store(Request $request)
{
    $requestData = $request->all();

    $validator = Validator::make($requestData, [
        'invoice_date' => 'required|date_format:d/m/Y',
        'invoice_amount' => 'required|numeric',
        'invoice_status' => 'required|string|max:255',
        'invoice_due_date' => 'required|date_format:d/m/Y',
        'payement_method' => 'required|string|max:255',
        'payement_date' => 'required|date_format:d/m/Y',
        'vat' => 'required|numeric',
        'client_id' => 'required|exists:clients,id',
        'currency' => 'required|string|max:3',
        'notes' => 'nullable|string',
    ]);

    
    
    if ($validator->fails()) {
        return $this->responseError($validator->errors()->toArray());
    }
    $userId = $request->user()->id;
    $invoiceNumber = $this->generateInvoiceNumber($userId);
    $requestData['user_id'] = $userId;
    
    $client = \App\Models\Client::where('id', $requestData['client_id'])
        ->where('user_id', $userId)
        ->first();

    if (! $client) {
        return $this->responseError(['client_id' => ['Client invalide ou non autorisé']], 403);
    }

    $requestData['invoice_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $requestData['invoice_date'])->format('Y-m-d');
    $requestData['invoice_due_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $requestData['invoice_due_date'])->format('Y-m-d');
    $requestData['payement_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $requestData['payement_date'])->format('Y-m-d');

    $requestData['invoice_number'] = $invoiceNumber;

    $invoice = Invoice::create($requestData);
    return $this->responseOk($invoice);
}


public function update(Request $request, $id)
{
    $requestData = $request->all();

    $validator = Validator::make($requestData, [
        'invoice_number' => 'required|string|max:255',
        'invoice_date' => 'required|date_format:d/m/Y',
        'invoice_amount' => 'required|numeric',
        'invoice_status' => 'required|string|max:255',
        'invoice_due_date' => 'required|date_format:d/m/Y',
        'payement_method' => 'required|string|max:255',
        'payement_date' => 'required|date_format:d/m/Y',
        'vat' => 'required|numeric',
        'client_id' => 'required|exists:clients,id',
        'currency' => 'required|string|max:3',
        'notes' => 'nullable|string',
    ]);

    
    
    if ($validator->fails()) {
        return $this->responseError($validator->errors()->toArray());
    }
    
    $userId = $request->user()->id;
    $invoiceNumber = $this->generateInvoiceNumber($userId);
    $requestData['user_id'] = $userId;

    $client = \App\Models\Client::where('id', $requestData['client_id'])
        ->where('user_id', $userId)
        ->first();

    if (! $client) {
        return $this->responseError(['client_id' => ['Client invalide ou non autorisé']], 403);
    }

    $requestData['invoice_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $requestData['invoice_date'])->format('Y-m-d');
    $requestData['invoice_due_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $requestData['invoice_due_date'])->format('Y-m-d');
    $requestData['payement_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $requestData['payement_date'])->format('Y-m-d');

    $requestData['invoice_number'] = $invoiceNumber;

    $invoice = Invoice::find($id);
    if($invoice->user_id !== Auth::user()->id){
        return $this->responseError('Unauthorized');
    }
    $invoice->update($requestData);
    return $this->responseOk($invoice);
}

    
    
    public function destroy($id)
    {
        $invoice = Invoice::find($id);
        if($invoice->user_id !== Auth::user()->id){
            return $this->responseError('Unauthorized');
        }
        $invoice->delete();
        return $this->responseOk($invoice);
    }
    

}
