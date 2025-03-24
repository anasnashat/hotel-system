<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReceptionistRequest extends FormRequest
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
            return [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8',
                'national_id' => 'required|string|max:255',
                'avatar_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ];
    
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'email.unique' => 'Email is already taken',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'national_id.required' => 'National ID is required',
            'avatar_image.image' => 'Avatar must be an image',
            'avatar_image.mimes' => 'Avatar must be a jpeg, png, jpg, or gif',
            'avatar_image.max' => 'Avatar must be less than 2MB',
        ];
    }
}
