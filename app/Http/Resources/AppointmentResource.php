<?php

namespace App\Http\Resources;

use App\Models\Appointment;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

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

        //$dates = Appointment::select('date')->distinct()->get();


        //$date = DB::table('appointments')->select('date');
        //$date = Appointment::all();
        return [
            //$dates
            'id' => $this->id,
            'date' => $this->date,
            'start_time' => $this->start_time
            //AppointmentDetailResource::collection(Appointment::all())->collection->groupBy($this->date)
            //'email' => $this->email
        ];
    }
}
