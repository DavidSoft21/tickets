<?php

namespace App\Http\Requests\Api\Status;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Status; // Asegúrate de usar el modelo correcto

class StatusUpdateRequest extends FormRequest
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
        $statusId = $this->route('status');
        $status = Status::find($statusId);

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('status')->ignore($statusId),
            ],
            'description' => 'required|string|max:255',
        ];
    }
}
