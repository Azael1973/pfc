<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ListingImage extends Model
{
    use HasFactory;

    protected $fillable = ['listing_id', 'path', 'thumb_path', 'order'];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}