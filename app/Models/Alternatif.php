<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Alternatif extends Model
{
    use HasFactory;
    use Uuid;

    public $timestamps = true;
    protected $table = "alternatif";
    protected $fillable = [
        'id','nama'
    ];
}
