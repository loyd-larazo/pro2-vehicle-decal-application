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
    $status = $request->get('status') ?? 'pending';

    Paginator::currentPageResolver(function() use ($page) {
      return $page;
    });

    $userVehicles = UserVehicle::where('verified_status', $status)
                                ->where(function($query) use ($search) {
                                  $query->where('make', 'like', "%$search%")
                                        ->orWhere('plate_number', 'like', "%$search%")
                                        ->orWhere('model', 'like', "%$search%")
                                        ->orWhere('year_model', 'like', "%$search%")
                                        ->orWhere('color', 'like', "%$search%")
                                        ->orWhere('engine_number', 'like', "%$search%")
                                        ->orWhere('chassis_number', 'like', "%$search%");
                                })
                                ->when($from && $to, function($query) use ($from, $to) {
                                  $query->whereDate('created_at', '>=', $from)
                                        ->whereDate('created_at', '<=', $to);
                                })
                                ->with(['photos', 'user'])
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

    $userVehicle = UserVehicle::find($id);
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
      
      $generatedCode = $this->getNextCode($lastCode);
      $code = $codePrefix . $generatedCode;
      $userVehicle->code = $code;
      $userVehicle->qr_code = QrCode::size(300)->generate($code);

      $settingModel->value = $generatedCode;
      $settingModel->save();
      $userVehicle->save();
    }

    $msg = $status == "approved" ? "Vehicle has been approved and QR code has been generated!" : "Vehicle has been rejected.";

    return redirect('/vehicles')->with('success', $msg);
  }

  private function getNextCode($code) {
    $charSet = ["0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z"];
    $charSetLen = count($charSet);

    if (!$code) {
      return "00000";
    }

    $newCode = "";
    $increaseNext = false;
    for ($i = (strlen($code) - 1); $i >= 0; $i--) {
      // last char
      if (($i + 1) == strlen($code)) {
        $char = $code[$i];
        $charIndex = array_search($char, $charSet);
        if (($charIndex + 1) == $charSetLen) {
          // reset to first and increase next
          $increaseNext = true;
          $newCode = $charSet[0] . $newCode;
        } else {
          $increaseNext = false;
          $newCode = $charSet[$charIndex + 1] . $newCode;
        }
      } else {
        $nextIndex = array_search($code[$i], $charSet);
        if ($increaseNext) {
          $nextIndex = $nextIndex + 1;
          if ($nextIndex == $charSetLen) {
            $nextIndex = 0;
            $increaseNext = true;
          } else {
            $increaseNext = false;
          }
        }

        $newCode = $charSet[$nextIndex] . $newCode;
      }
    }

    return $newCode;
  }

  public function release(Request $request) {
    $search = $request->get('search');
    $page = $request->get('page') ?? 1;

    Paginator::currentPageResolver(function() use ($page) {
      return $page;
    });

    $userVehicles = UserVehicle::whereIn('issued_status', ['pending', 'renewal'])
                                ->where('verified_status', 'approved')
                                ->whereNotNull('code')
                                ->where(function($query) use ($search) {
                                  $query->where('make', 'like', "%$search%")
                                        ->orWhere('plate_number', 'like', "%$search%")
                                        ->orWhere('model', 'like', "%$search%")
                                        ->orWhere('year_model', 'like', "%$search%")
                                        ->orWhere('color', 'like', "%$search%")
                                        ->orWhere('engine_number', 'like', "%$search%")
                                        ->orWhere('chassis_number', 'like', "%$search%");
                                })
                                ->with(['photos', 'user'])
                                ->paginate(20);
    return view('admin.release', [
      'vehicles' => $userVehicles,
      'search' => $search,
      'page' => $page,
    ]);
  }

  public function updateSticker(Request $request, $id, $status) {
    $user = $request->get('user');

    $userVehicle = UserVehicle::find($id);
    if ($userVehicle) {
      if ($status == 'renew') {
        $userVehicle->issued_status = "renewal";
        $userVehicle->save();

        return redirect('/profile/vehicles')->with('success', 'Renewal request has been sent');
      } else if ($status == 'release') {
        $today = Carbon::now();
        $expiryDate = Carbon::now()->addYear();
        $userVehicle->issued_status = "issued";
        $userVehicle->issued_by = $user->id;
        $userVehicle->issued_date = $today->toDateTimeString();
        $userVehicle->expiration_date = $expiryDate->toDateTimeString();
        $userVehicle->save();

        return redirect('/release')->with('success', 'Sticker/Passcard has been released!');
      }
    }
  }
}
