<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Applicant;
use App\Models\UserVehicle;

class ReportController extends Controller
{
  public function profile(Request $request) {
    $user = $request->get('user');
    $id = $request->get('id');

    if ($id) {
      $user = User::find($id);
    }
    
    $user->load('vehicles');

    return view('reports.profile-report', ['user' => $user]);
  }

  public function applicants(Request $request, $status) {
    $search = $request->get('search');
    $from = $request->get('from');
    $to = $request->get('to');
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
                          ->orderBy('updated_at', 'desc')
                          ->get();

    return view('reports.applicants-report', [
      'status' => $status, 
      'applicants' => $applicants, 
      'orientation' => 'landscape'
    ]);
  }

  public function applicant(Request $request, $id) {
    $applicant = Applicant::with(['verified', 'vehicle.photos'])
                          ->where('id', $id)
                          ->first();

    return view('reports.applicant-report', [
      'applicant' => $applicant,
    ]);
  }

  public function vehicles(Request $request, $status) {
    $search = $request->get('search');
    $from = $request->get('from');
    $to = $request->get('to');
    $vehicles = UserVehicle::when($status != 'all', function($query) use ($status) {
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
                          ->with(['photos', 'user'])
                          ->orderBy('updated_at', 'desc')
                          ->get();

    return view('reports.vehicles-report', [
      'status' => $status, 
      'vehicles' => $vehicles, 
      'orientation' => 'landscape'
    ]);
  }

  public function vehicle(Request $request, $id) {
    $vehicle = UserVehicle::with(['photos'])
                          ->where('id', $id)
                          ->first();

    return view('reports.vehicle-report', [
      'vehicle' => $vehicle, 
      'orientation' => 'landscape'
    ]);
  }

  public function users(Request $request, $userType) {
    $search = $request->get('search');
    $from = $request->get('from');
    $to = $request->get('to');
    $status = $request->get('statusFilter') ? $request->get('statusFilter') : 'active';

    $typeQuery = rtrim($userType, "s");
    $users = User::where('type', $typeQuery)
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
                ->where('status', $status == 'active' ? 1 : 0)
                ->orderBy('updated_at', 'desc')
                ->get();

    return view('reports.users-report', [
      'userType' => $userType, 
      'users' => $users, 
      'orientation' => 'landscape'
    ]);
  }

  public function user(Request $request, $id) {
    $user = User::where('id', $id)->first();

    return view('reports.applicant-report', [
      'applicant' => $user,
      'title' => "User",
    ]);
  }

  public function release(Request $request) {
    $search = $request->get('search');
    $status = $request->get('status') ?? 'all';
    
    $vehicles = UserVehicle::when($status != 'all', function($query) use ($status) {
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
                              ->get();

    return view('reports.vehicles-report', [
      'status' => "For Release",
      'vehicles' => $vehicles,
      'orientation' => 'landscape'
    ]);
  }

  public function userVehicles(Request $request, $id) {
    $search = $request->get('search');
    $status = $request->get('status') ?? 'all';
    
    $user = User::where('id', $id)
                ->with(['vehicles' => function($query) use ($search, $status) {
                  $query->where(function($query) use ($search) {
                    $query->where('make', 'like', "%$search%")
                          ->orWhere('type', 'like', "%$search%")
                          ->orWhere('plate_number', 'like', "%$search%")
                          ->orWhere('model', 'like', "%$search%")
                          ->orWhere('year_model', 'like', "%$search%")
                          ->orWhere('color', 'like', "%$search%")
                          ->orWhere('engine_number', 'like', "%$search%")
                          ->orWhere('chassis_number', 'like', "%$search%");
                  })
                  ->when($status != 'all', function($query) use ($status) {
                    $query->where('verified_status', $status);
                  });
                }])
                ->first();
    $vehicles = $user->vehicles;

    return view('reports.vehicles-report', [
      'status' => $user->firstname ." ". $user->lastname,
      'vehicles' => $vehicles,
      'orientation' => 'landscape'
    ]);
  }
}
