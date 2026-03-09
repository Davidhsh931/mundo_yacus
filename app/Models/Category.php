<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'description'
    ];

    public function guineaPigs()
    {
        return $this->hasMany(GuineaPig::class);
    }
}
