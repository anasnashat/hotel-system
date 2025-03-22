<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow all users to make this request
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'capacity' => 'sometimes|integer|min:1', // Capacity must be a positive integer
            'price' => 'sometimes|numeric|min:0', // Price must be a non-negative number
            'description' => 'sometimes|string|max:1000', // Description must be a string with a maximum length of 1000 characters
            'floor_id' => 'sometimes|exists:floors,id', // Floor ID must exist in the floors table
            'images' => 'nullable|array', // Images are optional and must be an array
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Each image must be a valid image file (jpeg, png, jpg, gif) and less than 2MB
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'capacity.required' => 'The capacity field is required.',
            'capacity.integer' => 'The capacity must be an integer.',
            'capacity.min' => 'The capacity must be at least 1.',
            'price.required' => 'The price field is required.',
            'price.numeric' => 'The price must be a number.',
            'price.min' => 'The price must be a non-negative number.',
            'description.required' => 'The description field is required.',
            'description.string' => 'The description must be a string.',
            'description.max' => 'The description may not be greater than 1000 characters.',
            'floor_id.required' => 'The floor ID field is required.',
            'floor_id.exists' => 'The selected floor ID is invalid.',
            'images.array' => 'The images must be an array.',
            'images.*.image' => 'Each file must be an image.',
            'images.*.mimes' => 'Each image must be a file of type: jpeg, png, jpg, gif.',
            'images.*.max' => 'Each image may not be greater than 2MB.',
        ];
    }
}
