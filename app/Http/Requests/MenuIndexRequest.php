<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class MenuIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => 'nullable|string',
            'per_page' => 'nullable|integer|min:1|max:100',
            'sort_by' => 'nullable|in:menu,created_at,updated_at',
            'sort_dir' => 'nullable|in:asc,desc',
            'only_deleted' => 'nullable|boolean',
            'page'          => 'nullable|integer|min:1', 
        ];
    }

    protected function prepareForValidation(): void
    {
        $allowedFields = array_keys($this->rules());

        // Ambil semua query string
        $allFields = array_keys($this->all());

        // Cari field yang tidak ada di rules
        $unknownFields = array_diff($allFields, $allowedFields);

        if (count($unknownFields) > 0) {
            throw new HttpResponseException(response()->json([
                'message' => 'Field tidak dikenali',
                'errors' => collect($unknownFields)->mapWithKeys(fn ($field) => [$field => ['Field ini tidak valid']])
            ], 422));
        }
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validasi gagal',
            'errors' => $validator->errors()
        ], 422));
    }
}




