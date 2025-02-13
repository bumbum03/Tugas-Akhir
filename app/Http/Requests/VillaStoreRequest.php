<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VillaStoreRequest extends FormRequest
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
            'nama' => 'required',
            'deskripsi' => 'required',
            'alamat' => 'required',
            'email' => 'required|email',
            'nomor' => 'required',
            'tahun_dibangun' => 'required',
            'total_kamar' => 'required',
            'kapasitas' => 'required',
            'check_in' => 'required', 
            'check_out' => 'required',
            'price' => 'required',
            'kota_id' => 'required',
            'status' => 'required',
        ];
    }
}
