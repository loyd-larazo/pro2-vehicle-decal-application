<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;

use App\Models\UserVehicle;
use App\Models\Setting;

use QrCode;

class VehicleController extends Controller
{
  public function index(Request $request) {
    $search = $request->get('search');
    $from = $request->get('from');
    $to = $request->get('to');
    $page = $request->get('page') ?? 1;
    $status = $request->get('status') ?? 'all';

    Paginator::currentPageResolver(function() use ($page) {
      return $page;
    });

    $userVehicles = UserVehicle::when($status != 'all', function($query) use ($status) {
                                  $query->where('verified_status', $status);
                                })
                                ->with(['photos', 'user'])
                                ->where(function($query) use ($search) {
                                  $query->where('make', 'like', "%$search%")
                                        ->orWhere('type', 'like', "%$search%")
                                        ->orWhere('plate_number', 'like', "%$search%")
                                        ->orWhere('model', 'like', "%$search%")
                                        ->orWhere('year_model', 'like', "%$search%")
                                        ->orWhere('color', 'like', "%$search%")
                                        ->orWhere('engine_number', 'like', "%$search%")
                                        ->orWhere('chassis_number', 'like', "%$search%")
                                        ->orWhereHas('user', function($query) use ($search) {
                                          $query->where('email', 'like', "%$search%")
                                                ->orWhere('firstname', 'like', "%$search%")
                                                ->orWhere('lastname', 'like', "%$search%")
                                                ->orWhere('middlename', 'like', "%$search%")
                                                ->orWhere('rank', 'like', "%$search%")
                                                ->orWhere('address', 'like', "%$search%")
                                                ->orWhere('designation', 'like', "%$search%")
                                                ->orWhere('office', 'like', "%$search%")
                                                ->orWhere('mobile', 'like', "%$search%")
                                                ->orWhere('telephone', 'like', "%$search%");
                                        });
                                })
                                ->when($from && $to, function($query) use ($from, $to) {
                                  $query->whereDate('created_at', '>=', $from)
                                        ->whereDate('created_at', '<=', $to);
                                })
                                ->whereNotNull('user_id')
                                ->where('status', 1)
                                ->paginate(20);
    return view('admin.vehicles', [
      'vehicles' => $userVehicles,
      'status' => $status,
      'search' => $search,
      'page' => $page,
      'from' => $from,
      'to' => $to,
    ]);
  }

  public function updateVehicle(Request $request, $id, $status) {
    $user = $request->get('user');

    $userVehicle = UserVehicle::where('id', $id)->with(['user'])->first();
    if ($userVehicle) {
      $today = Carbon::now();
      $userVehicle->verified_by = $user->id;
      $userVehicle->verified_date = $today->toDateTimeString();
      $userVehicle->verified_status = $status;

      $codePrefix = "";
      $lastCode = "00000";
      $settingModel = null;
      if ($userVehicle->type == 'car') {
        $codePrefix = 'P-02-';
        $settingModel = Setting::where('key', 'last_car_code')->first();
        $lastCode = $settingModel->value;
      } else {
        $codePrefix = 'S-02-';
        $settingModel = Setting::where('key', 'last_motor_code')->first();
        $lastCode = $settingModel->value;
      }
      
      $generatedCode = getNextCode($lastCode);
      $name = $userVehicle->user->firstname . ' ' . $userVehicle->user->middlename . ' ' . $userVehicle->user->lastname;
      $code =  $codePrefix . $generatedCode;
      $userVehicle->code = $code;
      $qrData = "Name:" . $name . "\n" . 
                "Vehicle Type:" . $userVehicle->type . "\n" . 
                "Plate Number:" . $userVehicle->plate_number . "\n" . 
                "Make:" . $userVehicle->make . "\n" . 
                "Series:" . $userVehicle->model . "\n" . 
                "Year Model:" . $userVehicle->year_model . "\n" . 
                "Passcard Code:" . $code;
      $userVehicle->qr_code = QrCode::size(300)->generate($qrData);

      $settingModel->value = $generatedCode;
      $settingModel->save();
      $userVehicle->save();
    }

    $msg = $status == "approved" ? "Vehicle has been approved and QR code has been generated!" : "Vehicle has been rejected.";

    return redirect('/vehicles')->with('success', $msg);
  }

  public function release(Request $request) {
    $search = $request->get('search');
    $page = $request->get('page') ?? 1;
    $from = $request->get('from');
    $to = $request->get('to');
    $status = $request->get('status') ?? 'all';
    $page = $request->get('page') ?? 1;

    Paginator::currentPageResolver(function() use ($page) {
      return $page;
    });

    $userVehicles = UserVehicle::when($status != 'all', function($query) use ($status) {
                                  if ($status == 'pending') {
                                    $query->whereIn('issued_status', ['pending', 'renewal']);
                                  } else {
                                    $query->where('issued_status', $status);
                                  }
                                })
                                ->where('verified_status', 'approved')
                                ->with(['photos', 'user'])
                                ->whereNotNull('code')
                                ->where(function($query) use ($search) {
                                  $query->where('make', 'like', "%$search%")
                                        ->orWhere('type', 'like', "%$search%")
                                        ->orWhere('plate_number', 'like', "%$search%")
                                        ->orWhere('model', 'like', "%$search%")
                                        ->orWhere('year_model', 'like', "%$search%")
                                        ->orWhere('color', 'like', "%$search%")
                                        ->orWhere('engine_number', 'like', "%$search%")
                                        ->orWhere('chassis_number', 'like', "%$search%")
                                        ->orWhereHas('user', function($query) use ($search) {
                                          $query->where('email', 'like', "%$search%")
                                                ->orWhere('firstname', 'like', "%$search%")
                                                ->orWhere('lastname', 'like', "%$search%")
                                                ->orWhere('middlename', 'like', "%$search%")
                                                ->orWhere('rank', 'like', "%$search%")
                                                ->orWhere('address', 'like', "%$search%")
                                                ->orWhere('designation', 'like', "%$search%")
                                                ->orWhere('office', 'like', "%$search%")
                                                ->orWhere('mobile', 'like', "%$search%")
                                                ->orWhere('telephone', 'like', "%$search%");
                                        });
                                })
                                ->paginate(20);
    return view('admin.release', [
      'vehicles' => $userVehicles,
      'search' => $search,
      'page' => $page,
      'from' => $from,
      'to' => $to,
      'status' => $status,
    ]);
  }

  public function updateSticker(Request $request, $id, $status) {
    $user = $request->get('user');

    $userVehicle = UserVehicle::with(['user'])->where('id', $id)->first();
    if ($userVehicle) {
      if ($status == 'renew') {
        $userVehicle->issued_status = "renewal";
        $userVehicle->save();

        return redirect('/profile/vehicles')->with('success', 'Renewal request has been sent');
      } else if ($status == 'release') {
        $user = $userVehicle->user;
        $name = $user->firstname . ' ' . $user->middlename . ' ' . $user->lastname;

        $today = Carbon::now();
        $expiryDate = Carbon::now()->addYear();
        $userVehicle->issued_status = "issued";
        $userVehicle->issued_by = $user->id;
        $userVehicle->issued_date = $today->toDateTimeString();
        $userVehicle->expiration_date = $expiryDate->toDateTimeString();
        $qrData = "Name:" . $name . "\n" . 
                                "Vehicle Type:" . $userVehicle->type . "\n" . 
                                "Plate Number:" . $userVehicle->plate_number . "\n" . 
                                "Make:" . $userVehicle->make . "\n" . 
                                "Series:" . $userVehicle->model . "\n" . 
                                "Year Model:" . $userVehicle->year_model . "\n" . 
                                "Passcard Code:" . $userVehicle->code . "\n" . 
                                "Start Date:" . $today->toDateTimeString() . "\n" . 
                                "Expiration Date:" . $expiryDate->toDateTimeString();
        $userVehicle->qr_code = QrCode::size(300)->generate($qrData);
        $userVehicle->save();

        return redirect('/release')->with('success', 'Sticker/Passcard has been released!');
      }
    }
  }

  public function validatePlatenumber(Request $request, $plateNumber) {
    $id = $request->get('id');
    $userVehicle = UserVehicle::where('plate_number', $plateNumber)
                              ->when($id, function($query) use ($id) {
                                $query->where('id', '!=', $id);
                              })
                              ->first();
                        
    return response()->json(['data' => $userVehicle ? true : false]);
  }
}
