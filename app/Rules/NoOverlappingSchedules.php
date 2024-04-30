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

        $scheduleId = request()->route('schedule') ? request()->route('schedule')->id : null;

        // Get all schedules for the user
        $schedules = Schedule::where('user_id', auth()->id())
            ->where('id', '!=', $scheduleId) // Exclude the current schedule
            ->get();

        foreach ($schedules as $schedule) {
            // Generate all dates for the schedule
            $dates = $this->generateDatesForSchedule($schedule);

            foreach ($dates as $date) {
                // Check if the provided start and end times overlap with the date
                if ($this->overlaps($start, $end, $date['start'], $date['end'])) {
                    return false;
                }
            }
        }

        return true;
    }

    private function generateDatesForSchedule($schedule)
    {
        $dates = [];

        // Add the original schedule date
        $dates[] = [
            'start' => $schedule->start,
            'end' => $schedule->end,
        ];

        // If the schedule is recurring, calculate the recurring dates
        if ($schedule->recurrence) {
            $start = Carbon::parse($schedule->start);
            $end = Carbon::parse($schedule->end);
            $endOfRecurrence = $schedule->end_of_recurrence ? Carbon::parse($schedule->end_of_recurrence) : null;

            while ($start->lessThanOrEqualTo($endOfRecurrence)) {
                $dates[] = [
                    'start' => $start->copy(),
                    'end' => $end->copy(),
                ];

                $start->addWeek();
                $end->addWeek();
            }
        }

        return $dates;
    }

    private function overlaps($start1, $end1, $start2, $end2)
    {
        return $start1->lessThanOrEqualTo($end2) && $end1->greaterThanOrEqualTo($start2);
    }

    public function message()
    {
        return 'The schedule overlaps with another schedule.';
    }

}
