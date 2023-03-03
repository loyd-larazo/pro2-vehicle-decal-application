<?php

use Illuminate\Support\Facades\Route;

use App\Http\Middleware\ValidateUser;

use App\Http\Controllers\AppController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VehicleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/login', [UserController::class, 'loginPage']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/application', [ApplicantController::class, 'applicationPage']);
Route::post('/application', [ApplicantController::class, 'application']);
Route::get('/applicant/{id}', [ApplicantController::class, 'applicantChangePage']);

Route::middleware([ValidateUser::class])->group(function () {
  Route::get('/', [AppController::class, 'dashboard']);

  Route::get('/applicants', [ApplicantController::class, 'applicants'])->name("applicants");
  Route::get('/applicant/{id}/{status}', [ApplicantController::class, 'updateApplication']);

  Route::get('/app/{userType}', [UserController::class, 'appUsers']);
  Route::post('/app/{userType}', [UserController::class, 'saveAppUsers']);

  Route::get('/profile', [AppController::class, 'profile'])->name('profile');
  Route::post('/profile', [AppController::class, 'saveProfile']);
  Route::get('/profile/vehicles', [AppController::class, 'profileVehicles'])->name('profileVehicles');
  Route::post('/profile/vehicles', [AppController::class, 'saveProfileVehicles']);

  Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles');
  Route::get('/vehicle/{id}/{status}', [VehicleController::class, 'updateVehicle']);
  Route::get('/vehicle/user/plate/{plate}', [VehicleController::class, 'validatePlatenumber']);

  Route::get('/release', [VehicleController::class, 'release'])->name('release');
  Route::get('/release/{id}/{status}', [VehicleController::class, 'updateSticker']);

  Route::get('/report/profile', [ReportController::class, 'profile']);
  Route::get('/report/applicants/{status}', [ReportController::class, 'applicants']);
  Route::get('/report/applicant/{id}', [ReportController::class, 'applicant']);
  Route::get('/report/vehicles/{status}', [ReportController::class, 'vehicles']);
  Route::get('/reports/app/{userType}', [ReportController::class, 'users']);

  Route::get('/logout', [UserController::class, 'logout']);
});