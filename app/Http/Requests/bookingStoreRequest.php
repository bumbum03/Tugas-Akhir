<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class bookingStoreRequest extends FormRequest
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
            'payment_type' => 'required',
            'total_price' => 'required|numeric',
            'email' => 'required|email',
            'name' => 'required|max:255',      
            'phone' => 'required|numeric',
            'booking_date' => 'required|date',
            'end_date' => 'required|date',
            'villa_id' => 'nullable',
            'user_id' => 'nullable',
            'status' => 'nullable',
        ];
    }
}
