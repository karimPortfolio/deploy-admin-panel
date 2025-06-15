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

    public function prepareForValidation()
    {
        $this->merge([
            'os_family' => $this->input('os_family.value'),
            'instance_type' => $this->input('instance_type.value'),
            'vpc_id' => $this->input('vpc_id.vpc_id'),
            'security_group_id' => $this->input('security_group_id.id'),
        ]);
    }
}
