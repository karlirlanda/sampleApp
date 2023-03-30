<?php

namespace App\Http\Requests;

use App\Helpers\EncryptDecrypt;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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

        $id = EncryptDecrypt::encryptDecrypt($this->input('id'), 'decrypt');

        return [
            'id' => 'required|exists:users,_id',
            'username' => 'string|unique:users,username,'.$id,
            'password' => 'string|min:6',
        ];
    }
}
