<?php

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController as User;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\UserAppointmentController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::apiResource('users', User::class)
    ->only(['index', 'show'])
    ->middleware('auth:sanctum');

Route::post('login', [
    App\Http\Controllers\LoginController::class,
    'login'
]);

Route::group(
    ['prefix' => 'appointments' ], function (){
        /*Route::get('', function (){
            return Appointment::groupBy('date')->get();
        });*/
        Route::get('', [AppointmentController::class, 'getAllAppointments']);
        Route::get('', [AppointmentController::class, 'getAllAppointments']);
        Route::get('days', [function () {
            return Appointment::select('date')->distinct()->get();
                }]);
    //Route::get('days/{date}', [AppointmentController::class, 'getHoursPerDay']);
    Route::get('days/{date}', [function ($date) {
        return Appointment::where('date', '=', Carbon::parse($date))->get();;
    }]);
    Route::post('new/anonymous', [AppointmentController::class, 'anonymousAppointment']);
        Route::post('new/registered', [AppointmentController::class, 'registeredAppointment']);
}
);

Route::group(
    ['prefix' => 'user'], function (){
    Route::get('appointments', [UserAppointmentController::class, 'getUserAppointments']);
    Route::post('appointments/{id}', [UserAppointmentController::class, 'cancelUserAppointment']);
}
);
