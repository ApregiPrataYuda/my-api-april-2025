<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return [
        //     'id_user' => $this->id_user,
        //     'fullname' => $this->fullname,
        //     'username' => $this->username,
        //     'email' => $this->email,
        //     'image' => $this->image,
        //     'password' => $this->password,
        //     'role' => $this->role,
        //     'name_group' => $this->name_group,
        //     'is_active' => $this->is_active,
        //     'nama_divisi' => $this->nama_divisi,
        //     'remember_token' => $this->remember_token,
        //     'created_at' => $this->created_at->toDateTimeString(),
        //     'updated_at' => $this->updated_at->toDateTimeString(),
        //     // 'deleted_at' => $this->updated_at->toDateTimeString(),
        // ];
        return [
            'id_user' => $this->id_user,
            'fullname' => $this->fullname,
            'username' => $this->username,
            'email' => $this->email,
            'image' => $this->image,
            // 'password' => $this->password, // JANGAN ditampilkan
            'role' => $this->role ?? null,
            'name_group' => $this->name_group ?? null,
            'is_active' => $this->is_active,
            'nama_divisi' => $this->nama_divisi ?? null,
            'remember_token' => $this->remember_token,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
