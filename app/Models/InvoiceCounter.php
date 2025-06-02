<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceCounter extends Model
{
    protected $fillable = ['user_id', 'year', 'counter'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
