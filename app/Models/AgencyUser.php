<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgencyUser extends Model
{
    use HasFactory;

    protected $table = 'agency_users';

    protected $fillable = [
        'user_id',
        'sub_agency_id',
        'rate'
    ];

}
