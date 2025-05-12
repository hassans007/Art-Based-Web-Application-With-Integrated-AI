<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicArtwork extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'user_id',
        'style_id',
        'artist_name',
        'description',
        'image_path',
        'is_approved',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function style()
    {
        return $this->belongsTo(Style::class);
    }


    public function comments()
    {
        return $this->hasMany(PublicArtworkComment::class, 'public_artwork_id');
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'public_artwork_likes', 'public_artwork_id', 'user_id')->withTimestamps();
    }


}
