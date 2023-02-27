<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class SubAgency extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sub_agencies';

    protected $fillable = [
        'sub_agency_name',
        'markup_rate',
        'agency_id'
    ];

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

}
