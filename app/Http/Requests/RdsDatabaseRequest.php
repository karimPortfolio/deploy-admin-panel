<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RdsDatabaseRequest extends FormRequest
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
            'db_instance_identifier' => [
                'required', 'string', 'between:1,63',
                'regex:/^[a-z][a-z0-9-]+$/', 'unique:rds_databases,db_instance_identifier',
            ],
            'engine' => ['required', 'string', Rule::enum(\App\Enums\DBEngines::class)],
            'engine_version' => ['nullable', 'string'],
            'db_instance_class' => ['required', 'string', Rule::enum(\App\Enums\DBInstanceClass::class)],
            'db_name' => ['nullable', 'string', 'max:64'],
            'master_username' => ['required','string','regex:/^[a-zA-Z][a-zA-Z0-9_]+$/','max:16'],
            'master_password' => ['required','string','min:8','max:128'],
            'allocated_storage' => ['required','integer','min:20'],
            'storage_type' => ['required','string', Rule::enum(\App\Enums\StorageType::class)],
            'multi_az' => ['boolean'],
            'publicly_accessible' => ['boolean'],
            'backup_retention_period' => ['nullable','integer','between:0,35'],
            'vpc_security_group' => ['required', 'string', 'exists:security_groups,group_id'],
            'storage_encrypted' => ['boolean'],
            ];
    }
}
