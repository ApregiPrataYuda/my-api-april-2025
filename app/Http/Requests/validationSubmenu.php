<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
class validationSubmenu extends FormRequest
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
            'id_menu' => [
                'required',
                'integer',
            ],
            'title' => [
                'required',
                'string',
                'regex:/^[A-Za-z0-9\s]+$/',
                Rule::unique('ms_submenu', 'title')->ignore($id, 'id_submenu'),
            ],
            'url' => [
                'required',
                'string',
            ],
            'icon' => [
                'nullable',
                'string',
            ],
            'noted' => [
                'nullable',
                'string',
            ],
            'is_active' => [
                'required',
                'integer',
                'in:0,1', // Harus 0 atau 1
            ],
            'parent_id' => [
                'nullable',
                'integer',
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
