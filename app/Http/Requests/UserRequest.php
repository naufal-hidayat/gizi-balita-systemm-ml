<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->isAdmin();
    }

    public function rules()
    {
        $userId = $this->route('user') ? $this->route('user')->id : null;
        
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId)
            ],
            'role' => 'required|in:admin,petugas_posyandu',
            'posyandu_name' => 'required_if:role,petugas_posyandu|nullable|string|max:255',
            'village' => 'required_if:role,petugas_posyandu|nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ];

        // Password validation
        if ($this->isMethod('post')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        } else {
            $rules['password'] = 'nullable|string|min:8|confirmed';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'role.required' => 'Role wajib dipilih',
            'posyandu_name.required_if' => 'Nama posyandu wajib diisi untuk petugas posyandu',
            'village.required_if' => 'Nama desa wajib diisi untuk petugas posyandu',
        ];
    }
}