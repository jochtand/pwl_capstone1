<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ticket_category_id',
        'total_price',
        'payment_status',
        'ticket_status',
        'payment_proof',
    ];

    // Relasi: Transaksi ini milik siapa?
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: Transaksi ini membeli tiket apa?
    public function ticketCategory()
    {
        return $this->belongsTo(TicketCategory::class);
    }
}
