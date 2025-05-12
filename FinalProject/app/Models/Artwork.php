<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artwork extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'artist_id', 'year_created', 'style_id',
        'image_path', 'description', 'views',
    ];

    public function artist()
    {
        return $this->belongsTo(Artist::class, 'artist_id');
    }

    public function style()
    {
        return $this->belongsTo(Style::class);
    }

    public function saves()
    {
        return $this->belongsToMany(User::class, 'artwork_user')->withTimestamps();
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'artwork_likes', 'artwork_id', 'user_id')->withTimestamps();
    }

    public function likesCount()
    {
        return $this->likes()->count();
    }
}
