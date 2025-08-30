<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AhpCriteria extends Model
{
    use HasFactory;

    protected $table = 'ahp_criteria';

    protected $fillable = [
        'code', 'name', 'description', 'weight', 'is_active'
    ];

    protected $casts = [
        'weight' => 'decimal:4',
        'is_active' => 'boolean'
    ];
}