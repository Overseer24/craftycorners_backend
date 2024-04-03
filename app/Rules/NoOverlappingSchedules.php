<?php

namespace App\Rules;

use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;


class NoOverlappingSchedules implements Rule
{



    public function passes($attribute, $value)
    {

        $start = Carbon::parse(request('start'));
        $end = Carbon::parse(request('end'));

        // Check if there are any schedules overlapping with the provided start and end times
        $overlappingSchedules = Schedule::where('user_id', auth()->id())
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start', [$start, $end])
                    ->orWhereBetween('end', [$start, $end])
                    ->orWhere(function ($q) use ($start, $end) {
                        $q->where('start', '<=', $start)->where('end', '>', $end);
                    });
            })
            ->exists();

        return !$overlappingSchedules;
    }
    public function message()
    {
        return 'The schedule overlaps with another schedule.';
    }

}
