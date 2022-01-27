<?php

namespace App\Http\Resources;

use App\Models\Appointment;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'date' => $this->date,
            'detail' => [
                AppointmentDetailResource::collection(Appointment::all())->collection->groupBy($this->date)
            ]
            //'email' => $this->email
        ];
    }
}
