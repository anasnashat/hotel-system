<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
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
            'capacity' => 'required|integer',
            'price' => 'required|numeric',
            'description' => 'required|string',
            'floor_id' => 'required|exists:floors,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
    public function messages(): array
    {
        return [
            'capacity.required' => 'The room capacity is required.',
            'capacity.integer' => 'The room capacity must be a number.',
            'price.required' => 'The room price is required.',
            'price.numeric' => 'The room price must be a valid number.',
            'description.required' => 'The room description is required.',
            'description.string' => 'The room description must be text.',
            'floor_id.required' => 'Please select a floor.',
            'floor_id.exists' => 'The selected floor is invalid.',
            'images.array' => 'Images must be provided as an array.',
            'images..image' => 'Files must be images.',
            'images..mimes' => 'Images must be jpeg, png, jpg, or gif.',
            'images.*.max' => 'Images may not be larger than 2MB.',
        ];
    }
}
