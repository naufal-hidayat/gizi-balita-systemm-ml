<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuzzyRule extends Model
{
    use HasFactory;

    protected $table = 'fuzzy_rules';

    protected $fillable = [
        'rule_name', 'conditions', 'conclusion', 'weight', 'is_active'
    ];

    protected $casts = [
        'conditions' => 'array',
        'weight' => 'decimal:2',
        'is_active' => 'boolean'
    ];
}