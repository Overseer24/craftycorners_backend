<?php

namespace App\Http\Requests\Schedule;

use App\Rules\EndDateAfterStartDate;
use App\Rules\NoOverlappingSchedules;
use Illuminate\Foundation\Http\FormRequest;

class UpdateScheduleRequest extends FormRequest
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
            'title' => 'string|max:255',
            'backgroundColor' => 'string|max:255',
            'start' => ['nullable',
                'date_format:Y-m-d H:i',
                new NoOverlappingSchedules
            ],
            'end' => ['nullable',
                'date_format:Y-m-d H:i',
                new EndDateAfterStartDate
            ],

            'startTime' => 'nullable|date_format:H:i',
            'endTime' => 'nullable|date_format:H:i|after:startTime',

        ];
    }
}
