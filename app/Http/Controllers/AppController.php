<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\Paginator;

use App\Models\User;
use App\Models\UserVehicle;
use App\Models\VehicleImage;

class AppController extends Controller
{
  public function dashboard(Request $request) {
    return view('dashboard');
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
    $firstname = $request->get('firstname');
    $middlename = $request->get('middlename');
    $lastname = $request->get('lastname');
    $email = $request->get('email');
    $change_password = $request->get('change_password');
    $password = $request->get('password');
    $rank = $request->get('rank');
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
      $user->address = $address;
      $user->designation = $designation;
      $user->office = $office;
      $user->mobile = $mobile;
      $user->telephone = $telephone;

      if ($change_password == 'on') {
        $user->password = app('hash')->make($password);
      }

      $pnpImagePath = $pnpIdPath;
      if ($request->file('pnp_id')) {
        $pnpImagePath = $request->file('pnp_id')->store('applications', 'public');
      }
      $user->pnp_id_picture = $pnpImagePath;

      $user->save();

      $request->session()->put('user', $user);
    }

    return redirect('/profile')->with('success', "Profile has been saved!");
  }

  public function profileVehicles(Request $request) {
    $user = $request->get('user');
    $search = $request->get('search');
    $page = $request->get('page') ?? 1;

    Paginator::currentPageResolver(function() use ($page) {
      return $page;
    });

    $vehicles = UserVehicle::with(['photos'])
                          ->where(function($query) use ($search) {
                            $query->where('make', 'like', "%$search%")
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
      'page' => $page,
    ]);
  }

  public function saveProfileVehicles(Request $request) {
    $user = $request->get('user');

    $id = $request->get('id');
    $type = $request->get('type');
    $plate_number = $request->get('plate_number');
    $make = $request->get('make');
    $model = $request->get('model');
    $year_model = $request->get('year_model');
    $color = $request->get('color');
    $engine_number = $request->get('engine_number');
    $chassis_number = $request->get('chassis_number');

    $orCr = "";
    if ($request->file('or_cr')) {
      $orCr = $request->file('or_cr')->store('orcr', 'public');
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
      $userVehicle->type = $type;
      $userVehicle->or_cr = $orCr;
    } else {
      $userVehicle = UserVehicle::updateOrCreate([
        'plate_number' => $plate_number
      ], [
        'user_id' => $user->id,
        'make' => $make,
        'model' => $model,
        'year_model' => $year_model,
        'color' => $color,
        'engine_number' => $engine_number,
        'chassis_number' => $chassis_number,
        'type' => $type,
        'verified_status' => 'pending',
        'or_cr' => $orCr,
      ]);
    }

    foreach ($request->photos as $photo) {
      $vehiclePath = $photo->store('vehicles', 'public');
      VehicleImage::firstOrCreate([
        'user_vehicle_id' => $userVehicle->id,
        'image' => $vehiclePath
      ]);
    }

    return redirect('/profile/vehicles')->with('success', "Vehicle has been saved!");
  }
}


