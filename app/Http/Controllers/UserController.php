<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\Paginator;

use App\Models\User;

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
                ->orderBy('updated_at', 'desc')
                ->paginate(20);

    return view('admin.users', [
      'userType' => $userType,
      'search' => $search,
      'page' => $page,
      'users' => $users,
      'from' => $from,
      'to' => $to,
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
    $mobile = $request->get('mobile');
    $telephone = $request->get('telephone');

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
      $user->office = $office;
      $user->mobile = $mobile;
      $user->telephone = $telephone;

      if ($change_password == 'on') {
        $user->password = app('hash')->make($password);
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
        'office' => $office,
        'mobile' => $mobile,
        'telephone' => $telephone,
      ]);
    }

    return redirect()->back()->with('success', ucfirst($user->type)." has been saved!"); 
  }

  
}
