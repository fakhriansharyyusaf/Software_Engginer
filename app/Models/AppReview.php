<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppReview extends Model
{
    protected $fillable = ['reviewer_name', 'rating', 'comment'];
}
