<?php

namespace App\Http\Controllers;

use App\Http\Resources\AppointmentCollection;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    public function anonymousAppointment(Request $request): \Illuminate\Http\JsonResponse
    {

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i:s',
            'finish_time' => 'date_format:H:i:s',
            'email' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'messages' => $validator->messages()
            ], 401);
        }

        $date = Carbon::parse($request->input('date'));

        $startTime = Carbon::parse($request->input('start_time'));

        //Aparecerá error en caso que intento de agendamiento sea fuera de horario de oficina o fines de semana.
        if ((($date->dayOfWeek == 6) || ($date->dayOfWeek == 0)) || (($startTime->hour < 9) || ($startTime->hour > 17))){
            return response()->json(['error' => 'No es posible agendar citas con la Muerte en horario fuera de oficina o fines de semana.'], 409);
        }

        //Laos siguientes dos manejos de errores se gatillaran al momento de manejar los horarios de agendamiento
        if (($startTime->minute != 0)){
            return response()->json(['error' => 'No es posible agendar cita con la Muerte, solo es posible agendar actualmente con bloques cerrados.'], 409);
        }

        if ($request->has('finish_time')){
            $finishTime = $request->input('finish_time');
            if ($startTime->floatDiffInMinutes($finishTime) > 60){
                return response()->json(['error' => 'No es posible agendar más de una hora con la muerte, es inútil.'], 409);
            } elseif ($startTime->floatDiffInMinutes($finishTime) < 60) {
                return response()->json(['error' => 'No es posible agendar menos de una hora con la muerte, pues de hacerlo sería demasiado traumático.'], 409);
            }
        } else {
            $finishTime = $startTime->addMinute(60)->format('H:i:s');
        }

        //La siguiente rutina se gatillará al encontrar una hora agendada anteriormente.
        $appointments = Appointment::all();

        foreach ($appointments as $value){
            if (($value->date == $request->input('date')) && ($value->start_time == $request->input('start_time'))){
                return response()->json(['error' => 'Hora reservada con la Muerte anteriormente, favor escoja otra hora.'], 409);
            }
        }

        //Se crea un nuevo registro con el agendamiento realizado.
        $appointment = Appointment::insert([
            'date' => $request->input('date'),
            'start_time' => $request->input('start_time'),
            'finish_time' => $finishTime,
            'email' => $request->input('email'),
            'status' => 1
        ]);



        //Se muestra salida JSON con datos de agendamiento con la muerte.
        return response()->json([
            'message' => 'Cita con la Muerte agendada exitosamente',
            'appointment_data' => [
                'date' => $request->input('date'),
                'start_time' => $request->input('start_time'),
                'finish_time' => $finishTime ,
                'email' => $request->input('email')
        ]
        ], 201);
    }

    /*public function checkDeathAvailability($request){
        $appointments = Appointment::all();

        foreach ($appointments as $value){
            if (($value->date == $request->input('date')) && ($value->start_time == $request->input('start_time'))){
                return response()->json(['error' => 'Hora reservada con la Muerte anteriormente, favor escoja otra hora.'], 409);
            }
        }
    }*/

    public function checkAppointmentDuration(){

    }

    public function getAllAppointments(): \Illuminate\Http\JsonResponse
    {
        //$appointments = Appointment::all()->toArray();

        //$appointments = Appointment::select('appointments.id', 'appointments.date', 'appointments.start_time')->groupBy('appointments.date')->get();
        $appointments = AppointmentResource::collection(Appointment::all());

        return response()->json([
            'data' => $appointments
        ]);
    }

    public function getHoursPerDay($date): \Illuminate\Http\JsonResponse
    {



        $date = Carbon::parse($date);
        $hoursPerDay = Appointment::where('date', '=', Carbon::parse($date))->get();

        return response()->json([
            'data' => $hoursPerDay
        ]);

    }


}
