<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class validationUser extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('id');
        return [
            'username' => [
                'required',
                'string',
                'regex:/^[A-Za-z0-9\s]+$/',
                Rule::unique('users', 'username')->ignore($id, 'id_user'),
            ],
            'password' => [
                'required',
                'string',
                'min:6',              
                'confirmed',          
            ],
            'email' => [
                'required',
                'string',
                'email',           
                'max:255',         
                'unique:users,email' 
            ],
            'fullname' => [
                'required',
                'string',
            ],
            'role_id' => [
                'required',
                'integer',
            ],
            'id_group' => [
                'required',
                'integer',
            ],
            'divisi_id' => [
                'required',
                'integer',
            ],
           'image' => [
            'nullable',     
            'mimes:jpeg,png,jpg,gif', 
            'max:2048',      
           ],
            'is_active' => [
                'required',
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
