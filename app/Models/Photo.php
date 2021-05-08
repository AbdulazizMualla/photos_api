<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    use HasFactory , SoftDeletes;
    protected $fillable = ['user_id' , 'photo_name' , 'title' , 'description' ];
    protected $appends = ['photo_url'];


    public function user()
    {
        return $this->belongsTo(User::class , 'user_id');
    }

    public function getPhotoUrlAttribute()
    {
        return config('app.url').Storage::url('photos/'.$this->user_id.'/'.$this->photo_name);
    }
}
