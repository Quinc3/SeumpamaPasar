<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWholesaleTierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'min_quantity' => ['required', 'integer', 'min:1', 'max:99999'],
            'price' => ['required', 'numeric', 'min:100'],
        ];
    }
}