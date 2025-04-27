<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class submenu extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'ms_submenu';
      // Primary key
      protected $primaryKey = 'id_submenu';
      // Auto-increment
      public $incrementing = true;
      // Timestamps
      public $timestamps = true;
      protected $fillable = ['id_menu','title','title','url','icon','noted','is_active','parent_id'];
}
