<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicArtworkComment extends Model
{
    use HasFactory;

    protected $fillable = ['public_artwork_id', 'user_id', 'content'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function artwork()
    {
        return $this->belongsTo(PublicArtwork::class, 'public_artwork_id');
    }
}
