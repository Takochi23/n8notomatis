<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'judul',
        'jumlah',
        'tipe',
        'tanggal',
        'kategori',
        'user_id',
    ];

    protected $casts = [
        'jumlah' => 'float',
        'tanggal' => 'date',
    ];
}
