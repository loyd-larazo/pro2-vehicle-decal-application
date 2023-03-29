<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use Carbon\Carbon;

use App\Models\Applicant;
use App\Models\UserVehicle;

class ValidateUser
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
   * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
   */
  public function handle(Request $request, Closure $next)
  {
    $user = $request->session()->get('user');
    if (!$user) {
      return redirect("/login");
    }

    $today = Carbon::now();
    $test= UserVehicle::whereNotNull('expiration_date')
              ->where('expiration_date', '<', $today->format('y-m-d'))
              ->where('issued_status', 'issued')
              ->update([
                'issued_status' => 'expired'
              ]);

    if ($user->type == "issuer" || $user->type == "admin") {
      $vehicles = UserVehicle::where('verified_status', 'pending')->whereNotNull('user_id')->count();
      $request->session()->put('pending_vehicles', $vehicles);

      $forRelease = UserVehicle::where('verified_status', 'approved')
                                ->whereIn('issued_status', ['pending', 'renewal'])
                                ->whereNotNull('code')
                                ->count();
      $request->session()->put('pending_release', $forRelease);
    }

    if ($user->type == "issuer" || $user->type == "admin") {
      $applicants = Applicant::where('status', 'pending')->count();
      $request->session()->put('pending_applicants', $applicants);
    }

    $request->session()->put('userType', $user->type);
    $request->session()->put('fullname', $user->firstname . " " . $user->middlename . " " . $user->lastname);
    $request->attributes->add(['user' => $user]);
    return $next($request);
  }
}
