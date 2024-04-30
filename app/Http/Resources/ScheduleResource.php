<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'backgroundColor' => $this->backgroundColor,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->diffForHumans()
        ];

        if ($this->start) {
            $data['start'] = $this->start->format('Y-m-d H:i:s');
        }

        if ($this->end) {
            $data['end'] = $this->end->format('Y-m-d H:i:s');
        }

        if ($this->startTime) {
            $data['startTime'] = $this->startTime;
        }

        if ($this->endTime) {
            $data['endTime'] = $this->endTime;
        }

        if ($this->startRecur) {
            $data['startRecur'] = $this->startRecur;
        }

        if ($this->endRecur) {
            $data['endRecur'] = $this->endRecur;
        }

        if ($this->daysOfWeek) {
            $data['daysOfWeek'] = $this->daysofweek;
        }

        return $data;
    }
}
