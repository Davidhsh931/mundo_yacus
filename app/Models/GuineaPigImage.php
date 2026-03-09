<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuineaPigImage extends Model
{

    protected $fillable = [
        'guinea_pig_id',
        'image_path'
    ];

    public function guineaPig()
    {
        return $this->belongsTo(GuineaPig::class);
    }
}