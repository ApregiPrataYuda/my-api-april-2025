<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $table = 'ms_role';
    // Primary key
    protected $primaryKey = 'id_role';
    // Auto-increment
    public $incrementing = true;
    // Timestamps
    public $timestamps = true;
    protected $fillable = ['role'];
}
