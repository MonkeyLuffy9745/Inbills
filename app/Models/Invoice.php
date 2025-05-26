<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'invoice_amount',
        'invoice_status',
        'invoice_due_date',
        'payement_method',
        'payement_date',
        'vat',
        'client_id',
        'user_id',
    ];

    public function client()
    {   
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }
    
}
