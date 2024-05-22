<?php

namespace App\Http\Requests\Api\Tickets;

use Illuminate\Foundation\Http\FormRequest;

class TicketUpdateRequest extends FormRequest
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
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:255',
            'deadline' => 'required|string|max:100|date_format:d-m-Y|after:today',
            'status_id' => 'required|int|min:1|exists:status',
            'user_id' => 'required|int|min:1|exists:users,id',
            'status_id' => 'required|int|min:1|exists:status,id'
        ];
    }
}
