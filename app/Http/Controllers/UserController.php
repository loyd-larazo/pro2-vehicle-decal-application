<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\Paginator;

use App\Models\User;
use App\Models\Applicant;
use App\Models\UserVehicle;

class UserController extends Controller
{
  public function loginPage(Request $request) {
    return view('login');
  }

  public function login(Request $request) {
    $email = $request->get('email');
    $password = $request->get('password');

    $user = User::where('email', $email)->first();
    if (!$user) {
      return view('/login', ['error' => 'Wrong email or password!', 'email' => $email]);
    }

    if (!Hash::check($password, $user->password)) {
      return view('/login', ['error' => 'Wrong email or password!', 'email' => $email]);
    }

    $request->session()->put('user', $user);

    return redirect('/');
  }

  public function logout(Request $request) {
    $request->session()->flush();

    return redirect('/login');
  }

  public function appUsers(Request $request, $userType) {
    if (!in_array($userType, ["users", "issuers", "admins"])) {
      return redirect('/app/users');
    }

    $search = $request->get('search');
    $page = $request->get('page') ?? 1;
    $from = $request->get('from');
    $to = $request->get('to');
    $status = $request->get('statusFilter') ? $request->get('statusFilter') : 'active';

    Paginator::currentPageResolver(function() use ($page) {
      return $page;
    });

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
                ->with(['vehicles.photos'])
                ->paginate(20);

    return view('admin.users', [
      'userType' => $userType,
      'search' => $search,
      'page' => $page,
      'users' => $users,
      'from' => $from,
      'to' => $to,
      'status' => $status,
    ]);
  }

  public function saveAppUsers(Request $request) {
    $id = $request->get('id');
    $type = $request->get('type');
    $firstname = $request->get('firstname');
    $middlename = $request->get('middlename');
    $lastname = $request->get('lastname');
    $email = $request->get('email');
    $change_password = $request->get('change_password');
    $password = $request->get('password');
    $confirmPassword = $request->get('confirmPassword');
    $rank = $request->get('rank');
    $address = $request->get('address');
    $designation = $request->get('designation');
    $office = $request->get('office');
    $otherOffice = $request->get('otherOffice');
    $mobile = $request->get('mobile');
    $telephone = $request->get('telephone');
    $status = $request->get('status');
    $endorser = $request->get('endorser');
    $pnpIdPath = $request->get('pnpIdPath') ? $request->get('pnpIdPath') : '';
    $driverLicensePath = $request->get('driverLicensePath') ? $request->get('driverLicensePath') : '';
    $endorserIdPath = $request->get('endorserIdPath') ? $request->get('endorserIdPath') : '';

    if ($request->file('pnp_id')) {
      $pnpIdPath = $request->file('pnp_id')->store('applications', 'public');
    }

    if ($request->file('driver_license')) {
      $driverLicensePath = $request->file('driver_license')->store('applications', 'public');
    }

    if ($request->file('endorser_id')) {
      $endorserIdPath = $request->file('endorser_id')->store('applications', 'public');
    }

    $user = null;
    if ($id) {
      $user = User::where('id', $id)->first();
      $user->firstname = $firstname;
      $user->middlename = $middlename;
      $user->lastname = $lastname;
      $user->email = $email;
      $user->rank = $rank;
      $user->address = $address;
      $user->designation = $designation;
      $user->other_office = $otherOffice && $office == 'others' ? 1 : 0;
      $user->office = $otherOffice && $office == 'others' ? $otherOffice : $office;
      $user->mobile = $mobile;
      $user->telephone = $telephone;
      $user->status = $status;
      $user->pnp_id_picture = $pnpIdPath;
      $user->drivers_license = $driverLicensePath;
      $user->endorser_id = $endorserIdPath;
      $user->endorser = $endorser;

      if ($change_password == 'on') {
        $user->password = app('hash')->make($password);
      }

      if ($status == 0) {
        UserVehicle::where('user_id', $id)->update(['status' => 0]);
      }

      $user->save();
    } else {
      $user = User::updateOrCreate([
        'email' => $email
      ], [
        'type' => $type,
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
        'drivers_license' => $driverLicensePath,
        'endorser_id' => $endorserIdPath,
        'endorser' => $endorser,
      ]);
    }

    $txtMsg = ucfirst($user->type == 'issuer' ? 'admin' : ($user->type == 'admin' ? 'superadmin' : $user->type));

    return redirect()->back()->with('success', $txtMsg." has been saved!"); 
  }

  public function validateEmail(Request $request, $email) {
    $id = $request->get('id');

    $user = User::where('email', $email)
                ->when($id, function($query) use ($id) {
                  $query->where('id', '!=', $id);
                })
                ->first();
    if ($user) {
      return response()->json(['data' => true]);
    }

    $applicant = Applicant::where('email', $email)->first();
    if ($user) {
      return response()->json(['data' => true]);
    }

    return response()->json(['data' => false]);
  }
}
