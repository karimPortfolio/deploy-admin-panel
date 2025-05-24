<?php

namespace App\Http\Requests;

use App\Enums\InstanceType;
use App\Enums\OsFamily;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

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
            'os_family' => ['required', 'string', new Enum(OsFamily::class)],
            'instance_type' => ['required', 'string', new Enum(InstanceType::class)],
            'vpc_id' => 'required|string|max:255',
            'security_group_id' => 'required|exists:security_groups,id',
        ];
    }
}
