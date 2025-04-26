<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class users extends Model
{
    use HasFactory;
    protected $table = 'users';

    protected $primaryKey = 'id_user';

    public $incrementing = true;

    public $timestamps = true;

    protected $fillable = ['name', 'email', 'password', 'role'];

}
