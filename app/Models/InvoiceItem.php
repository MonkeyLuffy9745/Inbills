<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'item_name',
        'item_quantity',
        'item_price',
        'description',
        'vat',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
