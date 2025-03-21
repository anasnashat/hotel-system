<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreFloorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            'name' => 'required|string|max:255', // Name is required, must be a string, and max 255 characters
            'number' => 'required|integer|unique:floors,number', // Number is required, must be an integer, and unique in the `floors` table
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
            'name.required' => 'The floor name is required.',
            'name.string' => 'The floor name must be a string.',
            'name.max' => 'The floor name must not exceed 255 characters.',
            'number.required' => 'The floor number is required.',
            'number.integer' => 'The floor number must be an integer.',
            'number.unique' => 'The floor number must be unique.',
        ];
    }
}
