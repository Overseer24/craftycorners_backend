<?php

namespace App\Http\Requests\Schedule;

use App\Rules\EndDateAfterStartDate;
use App\Rules\NoOverlappingSchedules;
use Illuminate\Foundation\Http\FormRequest;

class StoreRecurringScheduleRequest extends FormRequest
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
            'startTime' => 'required|date_format:H:i',
            'endTime' => 'required|date_format:H:i|after:startTime',
            'startRecur' => [
                'required',
                'date_format:Y-m-d H:i',
                new NoOverlappingSchedules,
            ],

            'endRecur' => 'required|date_format:Y-m-d H:i|after:startRecur',
            'daysOfWeek.*' => 'integer|between:0,6',
            'daysOfWeek' => 'required|array',
        ];
    }
}
