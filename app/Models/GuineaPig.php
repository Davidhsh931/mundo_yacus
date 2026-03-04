<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuineaPig extends Model
{
    protected $fillable = [
        'name',
        'breed',
        'average_weight',
        'price',
        'stock',
        'description',
        'category_id',
        'active'
    ];
}