<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaitingList extends Model
{
    use HasFactory;

    // Tambahkan baris ini untuk mengizinkan insert data
    protected $fillable = [
        'user_id',
        'ticket_category_id',
        'event_id',
    ];

    // (Opsional) Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // (Opsional) Relasi ke TicketCategory
    public function ticketCategory()
    {
        return $this->belongsTo(TicketCategory::class);
    }
}
