<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'direction',
        'is_default',
        'status',
        'progress',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'progress' => 'integer',
        ];
    }
}
