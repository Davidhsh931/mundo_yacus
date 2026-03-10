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
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(GuineaPigImage::class)->orderBy('position');
    }

}