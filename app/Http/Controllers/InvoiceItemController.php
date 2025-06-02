<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\InvoiceItem;


class InvoiceItemController extends Controller
{
    public function index()
    {
        $invoiceItems = InvoiceItem::all();
        return $this->responseOk($invoiceItems);
    }

    public function show($id)
    {
        $invoiceItem = InvoiceItem::find($id);
        return $this->responseOk($invoiceItem);
    }
    
    public function store(Request $request){
        $requestData = $request->all();
        $validator = Validator::make($requestData, [
            'invoice_id' => 'required|exists:invoices,id',
            'item_name' => 'required|string|max:255',
            'item_quantity' => 'required|integer',
            'item_price' => 'required|numeric',
            'description' => 'required|string|max:255',
            'vat' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return $this->responseError($validator->errors()->toArray());
        }
        $invoiceItem = InvoiceItem::create($requestData);
        return $this->responseOk($invoiceItem);
    }

    public function update(Request $request, $id){
        $requestData = $request->all();
        $validator = Validator::make($requestData, [
            'invoice_id' => 'required|exists:invoices,id',
            'item_name' => 'required|string|max:255',
            'item_quantity' => 'required|integer',
            'item_price' => 'required|numeric',
            'description' => 'required|string|max:255',
            'vat' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return $this->responseError($validator->errors()->toArray());
        }
        $invoiceItem = InvoiceItem::find($id);
        $invoiceItem->update($requestData);
        return $this->responseOk($invoiceItem);
    }
    
    public function destroy($id){
        $invoiceItem = InvoiceItem::find($id);
        $invoiceItem->delete();
        return $this->responseOk($invoiceItem);
    }
}
