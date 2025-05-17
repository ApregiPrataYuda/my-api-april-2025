<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class users extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;
    protected $table = 'users';

    protected $primaryKey = 'id_user';

    public $incrementing = true;

    public $timestamps = true;

    protected $fillable = ['fullname','username','divisi_id','role_id','id_group','name', 'email', 'password', 'role','image'];

}
