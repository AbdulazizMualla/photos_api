<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Profile extends Model
{
    use HasFactory;
    protected $fillable = ['file_name' , 'description' , 'user_id'];
    protected $appends = ['file_url'];

    public function user()
    {
        return $this->belongsTo(User::class , 'user_id');
    }

    public function getFileUrlAttribute()
    {
        if ($this->file_name){
            return config('app.url').Storage::url('users/'.$this->user_id.'/'.$this->file_name);
        }
    }
}
