<?php

namespace App\Http\Requests\Schedule;

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
                Rule::unique('schedules')->where(function ($query) {
                    return $query->where('user_id', auth()->id())
                        ->whereDate('start', request('start')->format('Y-m-d'))
                        ->where(function ($query) {
                            $start = request('start')->format('H:i');
                            $end = request('end')->format('H:i');
                            return $query->whereRaw("'$start' BETWEEN TIME(start) AND TIME(end)")
                                ->orWhereRaw("'$end' BETWEEN TIME(start) AND TIME(end)");
                        })
                        ;
                }),
            ],

            'end' => 'date_format:Y-m-d H:i',

        ];
    }
}
