<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSellerProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'store_name' => ['required', 'string', 'max:100', Rule::unique('seller_profiles', 'store_name')],
            'seller_type' => ['required', 'in:individual,business'],
            'description' => ['required', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'store_name.unique' => 'Nama toko sudah dipakai. Pilih nama lain.',
        ];
    }
}