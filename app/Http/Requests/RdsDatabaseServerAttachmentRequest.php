<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RdsDatabaseServerAttachmentRequest extends FormRequest
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
            'rds_database_id' => [
                'required',
                'exists:rds_databases,id',
                Rule::unique('rds_database_server', 'rds_database_id')->where(function ($query)  {
                    return $query->where('server_id', $this->input('server_id'));
                })
            ],
            'server_id' => [
                'required',
                'exists:servers,id', 
                Rule::unique('rds_database_server', 'server_id')->where(function ($query)  {
                return $query->where('rds_database_id', $this->input('rds_database_id'));
            })],
            'is_primary' => ['nullable', 'boolean']
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'server_id' => $this->input('server.id'),
        ]);
    }

    public function messages(): array
    {
        return [
            'rds_database_id.unique' => __('messages.rds_databases.rds_database_id_unique'),
            'server_id.unique' => __('messages.rds_databases.rds_server_id_unique'),
        ];
    }
}
