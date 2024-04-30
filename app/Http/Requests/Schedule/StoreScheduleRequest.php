<?php

namespace App\Http\Requests\Schedule;

use App\Rules\EndDateAfterStartDate;
use App\Rules\NoOverlappingSchedules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'backgroundColor' => 'required|string|max:255',
            'start' => [
                'required',
                'date_format:Y-m-d H:i',
                new NoOverlappingSchedules,
            ],
            'end' => [
                'required',
                'date_format:Y-m-d H:i',
                new EndDateAfterStartDate
                ],
            'end_of_recurrence' => 'nullable|date|after:start',
        ];
    }
}
