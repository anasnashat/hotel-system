<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateFloorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only allow users with the 'admin' or 'manager' role to update floors
        return Auth::check() && (Auth::user()->hasRole('admin') || Auth::user()->hasRole('manager'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
            'name' => 'required|string|min:3|max:100',
        ];
    }

    /**
     * Custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.string' => 'The floor name must be a string.',
            'name.max' => 'The floor name must not exceed 255 characters.',
            'number.integer' => 'The floor number must be an integer.',
            'number.unique' => 'The floor number must be unique.',
        ];
    }
}
