<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class menu extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'ms_menu';
      // Primary key
      protected $primaryKey = 'id_menu';
      // Auto-increment
      public $incrementing = true;
      // Timestamps
      public $timestamps = true;
      protected $fillable = ['menu'];
}
