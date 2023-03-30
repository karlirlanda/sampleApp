<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'search'    => 'nullable|string|max:24',
            'status'    => 'nullable|in:0,1,2,3',
            'limit'     => 'integer|digits_between:1,50',
            'sort_column' => 'nullable',
            'sort_order' => 'in:asc,desc'
        ];
    }
}
