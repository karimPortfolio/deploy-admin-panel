<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServerRequest extends FormRequest
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
            'instance_id' => 'required|string|max:255',
            'image_id' => 'required|string|max:255',
            'instance_type' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'private_ip_address' => 'nullable|ip',
            'public_ip_address' => 'nullable|ip',
            'vpc_id' => 'nullable|string|max:255',
            'ssh_key_id' => 'nullable|exists:ssh_keys,id',
            'security_group_id' => 'nullable|exists:security_groups,id',
        ];
    }
}
