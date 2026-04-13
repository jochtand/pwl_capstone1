<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    protected $fillable = [

        'organizer_id',
        'title',
        'description',
        'image',
        'category',
        'start_date',
        'end_date',
        'location',
        'banner_path',
    ];
    // Relasi One-to-Many: 1 Event punya banyak Kategori Tiket
    public function ticketCategories()
    {
        return $this->hasMany(TicketCategory::class);
    }
    // Relasi ke User (Panitia)
    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }
}
