<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'facebook',
        'address'
    ];

    protected $with = [
        'phone_numbers'
    ];

    //phone numbers
    public function phone_numbers(){
        return $this->hasMany('App\Models\PhoneNumber');
    }

}
