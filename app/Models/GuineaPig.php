<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuineaPig extends Model
{
    protected $fillable = [
    'user_id', 
    'name', 
    'species', 
    'price', 
    'product_state', 
    'stock',          // <--- ASEGÚRATE DE QUE ESTÉ AQUÍ
    'specifications',   // <--- ASEGÚRATE DE QUE ESTÉ AQUÍ
    'ia_verification', 
    'active'
];

    protected $casts = [
        'specifications' => 'array', 
        'ia_verification' => 'array',
    ];

    // El Habitante que vende el animal
    public function seller()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(GuineaPigImage::class)->orderBy('position');
    }
}