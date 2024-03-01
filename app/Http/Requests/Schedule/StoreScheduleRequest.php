<?php

namespace App\Http\Requests\Schedule;

use Illuminate\Foundation\Http\FormRequest;

class StoreScheduleRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'schedule_description' => 'required|string|max:255',
            'schedule_day' => 'required|string|max:255',
            'start' => 'required|date_format:H:i',
            'end' => 'required|date_format:H:i',
            'schedule_color' => 'required|string|max:255'
        ];
    }
}
