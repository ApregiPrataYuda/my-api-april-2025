<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class validationUserUpdate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
{
    $id = $this->route('id');

    return [
        'username' => [
            'nullable',
            'string',
            'regex:/^[A-Za-z0-9\s]+$/',
            Rule::unique('users', 'username')->ignore($id, 'id_user'),
        ],
        'password' => [
            'nullable',       // Boleh kosong
            'string',
            'min:6',
            'confirmed',
        ],
        'email' => [
            'nullable',
            'string',
            'email',
            'max:255',
            Rule::unique('users', 'email')->ignore($id, 'id_user'), // ← ini perbaikan penting
        ],
        'fullname' => [
            'nullable',
            'string',
        ],
        'role_id' => [
            'nullable',
            'integer',
        ],
        'id_group' => [
            'nullable',
            'integer',
        ],
        'divisi_id' => [
            'nullable',
            'integer',
        ],
        'image' => [
            'nullable',
            // 'mimes:jpeg,png,jpg,gif',
            'max:2048',
        ],
        'is_active' => [
            'nullable',
            'integer',
            'in:0,1',
        ],
    ];
}


    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
        ], 400));
    }
}
