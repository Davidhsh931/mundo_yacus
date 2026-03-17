<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuineaPig extends Model
{
    protected $fillable = [
        'name', 
        'description', 
        'price', 
        'user_id', 
        'species',        // Nuevo: cuy, oveja, etc.
        'product_state', 
        'specifications',  // Antes custom_attributes, ahora más técnico
        'ia_verification'  // El sello de garantía de Cuy-Vision
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