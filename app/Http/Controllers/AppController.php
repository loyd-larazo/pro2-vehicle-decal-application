<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\Paginator;

use Carbon\Carbon;

use App\Models\Applicant;
use App\Models\User;
use App\Models\UserVehicle;
use App\Models\VehicleImage;
use App\Models\Setting;

use DB;
use QrCode;

class AppController extends Controller
{
  public function dashboard(Request $request) {
    $user = $request->get('user');
    if ($user->type == 'user') {
      return redirect('/profile/vehicles');
    }

    $applicantPending = Applicant::where('status', 'pending')->count();
    $applicantApproved = Applicant::where('status', 'approved')->count();
    $applicantRejected = Applicant::where('status', 'rejected')->count();
    $applicantRequestChange = Applicant::where('status', 'request_change')->count();

    $carsPending = UserVehicle::where('type', 'car')->where('verified_status', 'pending')->count();
    $carsApproved = UserVehicle::where('type', 'car')->where('verified_status', 'approved')->count();
    $carsRejected = UserVehicle::where('type', 'car')->where('verified_status', 'rejected')->count();

    $motorsPending = UserVehicle::where('type', 'motor')->where('verified_status', 'pending')->count();
    $motorsApproved = UserVehicle::where('type', 'motor')->where('verified_status', 'approved')->count();
    $motorsRejected = UserVehicle::where('type', 'motor')->where('verified_status', 'rejected')->count();

    $releasePending = UserVehicle::whereNotNull('code')->where('verified_status', 'approved')->whereIn('issued_status', ['pending', 'renewal'])->count();
    $releaseIssued = UserVehicle::whereNotNull('code')->where('verified_status', 'approved')->where('issued_status', 'issued')->count();
    $releaseRejected = UserVehicle::whereNotNull('code')->where('verified_status', 'approved')->where('issued_status', 'rejected')->count();
    $releaseExpired = UserVehicle::whereNotNull('code')->where('verified_status', 'approved')->where('issued_status', 'expired')->count();

    return view('dashboard', [
      'applicants' => [
        'pending' => $applicantPending,
        'approved' => $applicantApproved,
        'rejected' => $applicantRejected,
        'request_change' => $applicantRequestChange,
        'total' => $applicantPending + $applicantApproved + $applicantRejected + $applicantRequestChange
      ],
      'cars' => [
        'pending' => $carsPending,
        'approved' => $carsApproved,
        'rejected' => $carsRejected,
        'total' => $carsPending + $carsApproved + $carsRejected
      ],
      'motors' => [
        'pending' => $motorsPending,
        'approved' => $motorsApproved,
        'rejected' => $motorsRejected,
        'total' => $motorsPending + $motorsApproved + $motorsRejected
      ],
      'release' => [
        'pending' => $releasePending,
        'issued' => $releaseIssued,
        'rejected' => $releaseRejected,
        'expired' => $releaseExpired,
        'total' => $releasePending + $releaseIssued + $releaseRejected + $releaseExpired
      ]
    ]);
  }

  public function profile(Request $request) {
    $user = $request->get('user');

    return view('profile', [
      'user' => $user
    ]);
  }

  public function saveProfile(Request $request) {
    $id = $request->get('id');
    $pnpIdPath = $request->get('pnpIdPath');
    $endorserIdPath = $request->get('endorserIdPath');
    $driversLicensePath = $request->get('driversLicensePath');
    $firstname = $request->get('firstname');
    $middlename = $request->get('middlename');
    $lastname = $request->get('lastname');
    $email = $request->get('email');
    $change_password = $request->get('change_password');
    $password = $request->get('password');
    $rank = $request->get('rank');
    $endorser = $request->get('endorser');
    $address = $request->get('address');
    $designation = $request->get('designation');
    $office = $request->get('office');
    $mobile = $request->get('mobile');
    $telephone = $request->get('telephone');

    $user = User::where('id', $id)->first();
    if ($user) {
      $user->firstname = $firstname;
      $user->middlename = $middlename;
      $user->lastname = $lastname;
      $user->email = $email;
      $user->rank = $rank;
      $user->endorser = $endorser;
      $user->address = $address;
      $user->designation = $designation;
      $user->office = $office;
      $user->mobile = $mobile;
      $user->telephone = $telephone;

      if ($change_password == 'on') {
        $user->password = app('hash')->make($password);
      }

      if ($request->file('pnp_id')) {
        $pnpIdPath = $request->file('pnp_id')->store('applications', 'public');
      }
      $user->pnp_id_picture = $pnpIdPath;

      if ($request->file('endorser_id')) {
        $endorserIdPath = $request->file('endorser_id')->store('applications', 'public');
      }
      $user->endorser_id = $endorserIdPath;

      if ($request->file('drivers_license')) {
        $driversLicensePath = $request->file('drivers_license')->store('applications', 'public');
      }
      $user->drivers_license = $driversLicensePath;

      $user->save();

      $request->session()->put('user', $user);
    }

    return redirect('/profile')->with('success', "Profile has been saved!");
  }

  public function profileVehicles(Request $request) {
    $user = $request->get('user');
    $from = $request->get('from');
    $to = $request->get('to');
    $search = $request->get('search');
    $status = $request->get('status') ?? 'all';
    $page = $request->get('page') ?? 1;

    Paginator::currentPageResolver(function() use ($page) {
      return $page;
    });

    $vehicles = UserVehicle::with(['photos'])
                          ->when($status != 'all', function($query) use ($status) {
                            $query->where('verified_status', $status);
                          })
                          ->where(function($query) use ($search) {
                            $query->where('make', 'like', "%$search%")
                                  ->orWhere('type', 'like', "%$search%")
                                  ->orWhere('plate_number', 'like', "%$search%")
                                  ->orWhere('model', 'like', "%$search%")
                                  ->orWhere('year_model', 'like', "%$search%")
                                  ->orWhere('color', 'like', "%$search%")
                                  ->orWhere('engine_number', 'like', "%$search%")
                                  ->orWhere('chassis_number', 'like', "%$search%");
                          })
                          ->where('user_id', $user->id)
                          ->paginate(20);

    return view('profile-vehicle', [
      'vehicles' => $vehicles,
      'search' => $search,
      'from' => $from,
      'to' => $to,
      'page' => $page,
      'status' => $status,
      'user' => $user,
    ]);
  }

  public function saveProfileVehicles(Request $request) {
    $user = $request->get('user');
    $today = Carbon::now();

    $adminSave = $request->get('adminSave');
    $id = $request->get('id');
    $userId = $request->get('userId');
    $type = $request->get('type');
    $plate_number = $request->get('plate_number');
    $make = $request->get('make');
    $model = $request->get('model');
    $year_model = $request->get('year_model');
    $color = $request->get('color');
    $engine_number = $request->get('engine_number');
    $chassis_number = $request->get('chassis_number');
    $own_vehicle = $request->get('own_vehicle') && $request->get('own_vehicle') == 'yes' ? 1 : 0;
    $isActive = $request->get('isActive') && $request->get('isActive') == 'active' ? 1 : 0;

    $deedOfSalePath = $request->get('deedOfSalePath') ? $request->get('deedOfSalePath') : '';
    $orPath = $request->get('orPath') ? $request->get('orPath') : '';
    $crPath = $request->get('crPath') ? $request->get('crPath') : '';

    if ($request->file('or')) {
      $orPath = $request->file('or')->store('orcr', 'public');
    }

    if ($request->file('cr')) {
      $crPath = $request->file('cr')->store('orcr', 'public');
    }

    if ($request->file('deed_of_sale')) {
      $deedOfSalePath = $request->file('deed_of_sale')->store('applications', 'public');
    }

    $userVehicle = null;
    if ($id) {
      $userVehicle = UserVehicle::find($id);
      $userVehicle->plate_number = $plate_number;
      $userVehicle->make = $make;
      $userVehicle->model = $model;
      $userVehicle->year_model = $year_model;
      $userVehicle->color = $color;
      $userVehicle->engine_number = $engine_number;
      $userVehicle->chassis_number = $chassis_number;
      $userVehicle->own_vehicle = $own_vehicle;
      $userVehicle->type = $type;
      $userVehicle->or = $orPath;
      $userVehicle->cr = $crPath;
      $userVehicle->deed_of_sale = $deedOfSalePath;
      $userVehicle->status = $isActive;
      $userVehicle->save();
    } else {
      $userVehicle = UserVehicle::updateOrCreate([
        'plate_number' => $plate_number
      ], [
        'user_id' => $userId ?? $user->id,
        'make' => $make,
        'model' => $model,
        'year_model' => $year_model,
        'color' => $color,
        'engine_number' => $engine_number,
        'chassis_number' => $chassis_number,
        'own_vehicle' => $own_vehicle,
        'type' => $type,
        'verified_by' => $user->id,
        'verified_date' => $today->toDateTimeString(),
        'verified_status' => $adminSave ? 'approved' : 'pending',
        'or' => $orPath,
        'cr' => $crPath,
        'deed_of_sale' => $deedOfSalePath,
      ]);
    }

    if ($request->photos) {
      foreach ($request->photos as $photo) {
        $vehiclePath = $photo->store('vehicles', 'public');
        VehicleImage::firstOrCreate([
          'user_vehicle_id' => $userVehicle->id,
          'image' => $vehiclePath
        ]);
      }
    }

    if ($adminSave) {
      if (!$id) {
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

      return redirect('/app/users')->with('success', "Vehicle has been saved!");
    }

    return redirect('/profile/vehicles')->with('success', "Vehicle has been saved!");
  }
}


