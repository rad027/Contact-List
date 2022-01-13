<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PhoneNumber extends Model
{

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'value'
    ];

    public function contact(){
        return $this->belongsTo('App\Models\Contact');
    }

}
