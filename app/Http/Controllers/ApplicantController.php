<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

use Carbon\Carbon;

use App\Models\Applicant;
use App\Models\User;
use App\Models\UserVehicle;
use App\Models\VehicleImage;
use App\Models\Setting;

use QrCode;

class ApplicantController extends Controller
{
  public function applicationPage(Request $request) {
    return view('application');
  }

  public function applicantChangePage(Request $request, $id) {
    $applicant = Applicant::where('id', $id)
                          ->with(['vehicle.photos'])
                          ->where('status', 'request_change')->first();
    if (!$applicant) {
      return redirect('/login');
    }

    return view('application', [
      'applicant' => $applicant
    ]);
  }

  public function application(Request $request) {
    $id = $request->get('id');
    $pnpIdPath = $request->get('pnpIdPath') ? $request->get('pnpIdPath') : '';
    $deedOfSalePath = $request->get('deedOfSalePath') ? $request->get('deedOfSalePath') : '';
    $driverLicensePath = $request->get('driverLicensePath') ? $request->get('driverLicensePath') : '';
    $endorserIdPath = $request->get('endorserIdPath') ? $request->get('endorserIdPath') : '';
    $orPath = $request->get('orPath') ? $request->get('orPath') : '';
    $crPath = $request->get('crPath') ? $request->get('crPath') : '';
    $firstname = $request->get('firstname');
    $middlename = $request->get('middlename');
    $lastname = $request->get('lastname');
    $email = $request->get('email');
    $password = $request->get('password');
    $rank = $request->get('rank');
    $address = $request->get('address');
    $designation = $request->get('designation');
    $otherOffice = $request->get('otherOffice');
    $office = $request->get('office');
    $mobile = $request->get('mobile');
    $telephone = $request->get('telephone');
    $endorser = $request->get('endorser');

    $type = $request->get('type');
    $plate_number = $request->get('plate_number');
    $make = $request->get('make');
    $model = $request->get('model');
    $year_model = $request->get('year_model');
    $color = $request->get('color');
    $engine_number = $request->get('engine_number');
    $chassis_number = $request->get('chassis_number');
    $ownVehicle = $request->get('own_vehicle') && $request->get('own_vehicle') == 'yes' ? 1 : 0;

    if (!$id) {
      $applicantExists = Applicant::where('email', $email)->first();
      if ($applicantExists && $applicantExists->status != "request_change") {
        $msg = "";
        if ($applicantExists->status == 'rejected') {
          $msg = "Your previous application has been rejected!";
        } else if ($applicantExists->status == 'pending') {
          $msg = "Application with the same email already exists!";
        } else if ($applicantExists->status == 'approved') {
          $msg = "Your previous application has been approved, you can now login.";
        }
        return view('/application', ['error' => $msg, 'email' => $email]);
      }
    }

    if ($request->file('pnp_id')) {
      $pnpIdPath = $request->file('pnp_id')->store('applications', 'public');
    }

    if ($request->file('deed_of_sale')) {
      $deedOfSalePath = $request->file('deed_of_sale')->store('applications', 'public');
    }

    if ($request->file('driver_license')) {
      $driverLicensePath = $request->file('driver_license')->store('applications', 'public');
    }

    if ($request->file('endorser_id')) {
      $endorserIdPath = $request->file('endorser_id')->store('applications', 'public');
    }

    if ($request->file('or')) {
      $orPath = $request->file('or')->store('orcr', 'public');
    }
    
    if ($request->file('cr')) {
      $crPath = $request->file('cr')->store('orcr', 'public');
    }

    $msg = "";
    if ($id) {
      $applicant = Applicant::where('id', $id)->with(['vehicle.photos'])->first();
      $applicant->email = $email;
      $applicant->password = app('hash')->make($password);
      $applicant->firstname = $firstname;
      $applicant->middlename = $middlename;
      $applicant->lastname = $lastname;
      $applicant->rank = $rank;
      $applicant->address = $address;
      $applicant->designation = $designation;
      $applicant->other_office = $otherOffice && $office == 'others' ? 1 : 0;
      $applicant->office = $otherOffice && $office == 'others' ? $otherOffice : $office;
      $applicant->mobile = $mobile;
      $applicant->telephone = $telephone;
      $applicant->pnp_id_picture = $pnpIdPath;
      $applicant->status = "pending";
      $applicant->drivers_license = $driverLicensePath;
      $applicant->endorser_id = $endorserIdPath;
      $applicant->endorser = $endorser;
      $applicant->email_sent = 0;
      $applicant->save();

      $applicant->vehicle->plate_number = $plate_number;
      $applicant->vehicle->make = $make;
      $applicant->vehicle->model = $model;
      $applicant->vehicle->year_model = $year_model;
      $applicant->vehicle->color = $color;
      $applicant->vehicle->engine_number = $engine_number;
      $applicant->vehicle->chassis_number = $chassis_number;
      $applicant->vehicle->type = $type;
      $applicant->vehicle->or = $orPath;
      $applicant->vehicle->cr = $crPath;
      $applicant->vehicle->deed_of_sale = $deedOfSalePath;
      $applicant->vehicle->own_vehicle = $ownVehicle;
      $applicant->vehicle->save();

      foreach ($request->photos as $photo) {
        $vehiclePath = $photo->store('vehicles', 'public');
        VehicleImage::firstOrCreate([
          'user_vehicle_id' => $applicant->vehicle->id,
          'image' => $vehiclePath
        ]);
      }

      $msg = "Your application has been updated!";
    } else {
      $application = Applicant::updateOrCreate([
        'email' => $email
      ], [
        'password' => app('hash')->make($password),
        'firstname' => $firstname,
        'middlename' => $middlename,
        'lastname' => $lastname,
        'rank' => $rank,
        'address' => $address,
        'designation' => $designation,
        'other_office' => $otherOffice && $office == 'others' ? 1 : 0,
        'office' => $otherOffice && $office == 'others' ? $otherOffice : $office,
        'mobile' => $mobile,
        'telephone' => $telephone,
        'pnp_id_picture' => $pnpIdPath,
        'status' => 'pending',
        'drivers_license' => $driverLicensePath,
        'endorser_id' => $endorserIdPath,
        'endorser' => $endorser,
      ]);

      $userVehicle = UserVehicle::updateOrCreate([
        'plate_number' => $plate_number
      ], [
        'applicant_id' => $application->id,
        'make' => $make,
        'model' => $model,
        'year_model' => $year_model,
        'color' => $color,
        'engine_number' => $engine_number,
        'chassis_number' => $chassis_number,
        'type' => $type,
        'verified_status' => 'pending',
        'or' => $orPath,
        'cr' => $crPath,
        'deed_of_sale' => $deedOfSalePath,
        'own_vehicle' => $ownVehicle,
      ]);

      foreach ($request->photos as $photo) {
        $vehiclePath = $photo->store('vehicles', 'public');
        VehicleImage::firstOrCreate([
          'user_vehicle_id' => $userVehicle->id,
          'image' => $vehiclePath
        ]);
      }

      $msg = $application->wasRecentlyCreated ? "Application has been sent!" : "Your application has been updated!";
    }

    return redirect('/application')->with('success', $msg);
    
  }

  public function applicants(Request $request) {
    $search = $request->get('search');
    $from = $request->get('from');
    $to = $request->get('to');
    $status = $request->get('status') ?? 'all';
    $page = $request->get('page') ?? 1;
    $success = $request->get('success');
    $send_email_to = $request->get('send_email_to');

    Paginator::currentPageResolver(function() use ($page) {
      return $page;
    });

    $applicants = Applicant::when($status != 'all', function($query) use ($status) {
                            $query->where('status', $status);
                          })
                          ->where(function($query) use ($search) {
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
                          })
                          ->when($from && $to, function($query) use ($from, $to) {
                            $query->whereDate('created_at', '>=', $from)
                                  ->whereDate('created_at', '<=', $to);
                          })
                          ->with(['vehicle.photos'])
                          ->orderBy('updated_at', 'desc')
                          ->paginate(20);

    $toSendEmail = null;
    if ($send_email_to) {
      $applicant = Applicant::find($send_email_to);
      if ($applicant && $applicant->email_sent == 0) {
        $applicant->email_sent = 1;
        $applicant->save();

        $applicant->email_sent = 0;
        $toSendEmail = json_encode($applicant);
      }
    }

    return view('admin.applicants', [
      'search' => $search,
      'from' => $from,
      'to' => $to,
      'status' => $status,
      'page' => $page,
      'applicants' => $applicants,
      'success' => $success,
      'toSendEmail' => $toSendEmail,
    ]);
  }

  public function updateApplication(Request $request, $id, $status) {
    $user = $request->get('user');
    $remarks = $request->get('remarks');
    $applicant = Applicant::where('id', $id)->with(['vehicle'])->first();
    if ($applicant) {
      $applicant->status = $status;
      $applicant->remarks = $remarks;

      // Move applicant as a user if approved
      if ($status == 'approved') {
        $today = Carbon::now();
        $applicant->verified_by = $user->id;
        $applicant->verified_date = $today->toDateTimeString();

        $userModel = User::create([
          'type' => 'user',
          'email' => $applicant->email,
          'password' => $applicant->password,
          'firstname' => $applicant->firstname,
          'middlename' => $applicant->middlename,
          'lastname' => $applicant->lastname,
          'rank' => $applicant->rank,
          'address' => $applicant->address,
          'designation' => $applicant->designation,
          'other_office' => $applicant->other_office,
          'office' => $applicant->office,
          'mobile' => $applicant->mobile,
          'telephone' => $applicant->telephone,
          'pnp_id_picture' => $applicant->pnp_id_picture,
          'drivers_license' => $applicant->drivers_license,
          'endorser_id' => $applicant->endorser_id,
          'endorser' => $applicant->endorser,
        ]);

        $applicant->vehicle->user_id = $userModel->id;
        $applicant->vehicle->verified_by = $user->id;
        $applicant->vehicle->verified_date = $today->toDateTimeString();
        $applicant->vehicle->verified_status = $status;

        $codePrefix = "";
        $lastCode = "00000";
        $settingModel = null;
        if ($applicant->vehicle->type == 'car') {
          $codePrefix = 'P-02-';
          $settingModel = Setting::where('key', 'last_car_code')->first();
          $lastCode = $settingModel->value;
        } else {
          $codePrefix = 'S-02-';
          $settingModel = Setting::where('key', 'last_motor_code')->first();
          $lastCode = $settingModel->value;
        }

        $generatedCode = getNextCode($lastCode);
        $name = $applicant->firstname . ' ' . $applicant->middlename . ' ' . $applicant->lastname;
        $code =  $codePrefix . $generatedCode;
        $applicant->vehicle->code = $code;
        $qrData = $name . " - " . $applicant->vehicle->type . " - " . $applicant->vehicle->plate_number . " - " . $applicant->vehicle->make . " - " . $applicant->vehicle->model . " - " . $applicant->vehicle->year_model . " - " . $code;
        $applicant->vehicle->qr_code = QrCode::size(300)->generate($qrData);
        
        $settingModel->value = $generatedCode;
        $settingModel->save();
        $applicant->vehicle->save();
      }

      $applicant->save();

      $msg = $status == 'approved' ? "Applicant has been approved!" : ($status == 'request_change' ? "Change request has been sent to the applicant!" : "Applicant has been rejected!");

      return \Redirect::route("applicants", [
        'success' => $msg,
        'send_email_to' => $applicant->id
      ]);
    }
  }
}
