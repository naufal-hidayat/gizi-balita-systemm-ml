<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_type', 'period_start', 'period_end', 
        'generated_by', 'data', 'file_path'
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'data' => 'array'
    ];
}