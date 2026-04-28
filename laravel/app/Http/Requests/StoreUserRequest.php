<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        //allow only if the request passed the admin only gate check
        return Gate::allows('admin-only');
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'first_name' => trim($this->first_name),
            'last_name'  => trim($this->last_name),
            'email'      => filter_var(strtolower(trim($this->email)), FILTER_SANITIZE_EMAIL),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user'); // Get the user ID from the route if it exists
        return [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $userId,
            'password'   => $userId ? 'nullable|min:8|confirmed' : 'required|min:8|confirmed',
            'role'       => 'required|exists:roles,slug',
        ];
    }
}
