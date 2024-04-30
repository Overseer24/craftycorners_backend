<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\ScheduleResource;
use App\Http\Requests\Schedule\StoreScheduleRequest;
use App\Http\Requests\Schedule\UpdateScheduleRequest;

class ScheduleController extends Controller {
    /**
     * Display a listing of the resource.
     */


    public function index() {
        $user = auth()->user();
        $schedules = $user->schedule;

        $schedules = $schedules->map(function ($schedule) {
            if ($schedule->recurrence) {
                $schedule->recurrence = $this->calculateRecurringOccurrences($schedule);
            }
             return $schedule;
        });

        return response()->json($schedules);
    }

    /**
     * Show the form for creating a new resource.
     */


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreScheduleRequest $request) {
        $schedule = auth()->user()->schedule()->create($request->validated());
        return new ScheduleResource($schedule);
    }


    //show all schedules of a specific user
    public function showUserSchedules(User $user)
    {
        $schedules = $user->schedule;
        return ScheduleResource::collection($schedules);
    }
    /**
     * Display the specified resource.
     */

    public function storeRecurring(StoreScheduleRequest $request)
    {
        $validated = $request->validated();
        $validated['recurrence'] = 'weekly';

        $schedule = auth()->user()->schedule()->create($validated);
        return new ScheduleResource($schedule);
    }

    //show the specific schedule
    public function show(Schedule $schedule) {

        return new ScheduleResource($schedule);
    }

    /**
     * Show the form for editing the specified resource.
     */

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateScheduleRequest $request, Schedule $schedule) {
        $schedule->update($request->validated());
        return new ScheduleResource($schedule);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule) {
        $schedule->delete();
        return response()->json([
            'message' => 'Schedule deleted successfully'
        ]);
    }

    private function calculateRecurringOccurrences($schedule)
    {
        $occurrences = [];

        if ($schedule-> recurrence== 'weekly') {
            $start =  Carbon::parse($schedule->start);
            $end = Carbon::parse($schedule->end);
            $endOfRecurrence = $schedule->end_of_recurrence?Carbon::parse($schedule->end_of_recurrence):null;
            while (!$endOfRecurrence || $start->lessThanOrEqualTo($endOfRecurrence)) {
                $occurrences[] = [
                    'start' => $start->toDateTimeString(),
                    'end' => $end->toDateTimeString(),
                ];
                $start->addWeek();
                $end->addWeek();

            }

        }
        return $occurrences;
    }
}
