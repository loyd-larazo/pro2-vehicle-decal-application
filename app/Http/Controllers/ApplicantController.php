<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

use Carbon\Carbon;

use App\Models\Applicant;
use App\Models\User;

class ApplicantController extends Controller
{
  public function applicationPage(Request $request) {
    return view('application');
  }

  public function applicantChangePage(Request $request, $id) {
    $applicant = Applicant::where('id', $id)->where('status', 'request_change')->first();
    if (!$applicant) {
      return redirect('/login');
    }

    return view('application', [
      'applicant' => $applicant
    ]);
  }

  public function application(Request $request) {
    $id = $request->get('id');
    $pnpIdPath = $request->get('pnpIdPath');
    $firstname = $request->get('firstname');
    $middlename = $request->get('middlename');
    $lastname = $request->get('lastname');
    $email = $request->get('email');
    $password = $request->get('password');
    $rank = $request->get('rank');
    $address = $request->get('address');
    $designation = $request->get('designation');
    $office = $request->get('office');
    $mobile = $request->get('mobile');
    $telephone = $request->get('telephone');

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

    $pnpImagePath = $pnpIdPath;
    if ($request->file('pnp_id')) {
      $pnpImagePath = $request->file('pnp_id')->store('applications', 'public');
    }

    $msg = "";
    if ($id) {
      $applicant = Applicant::find($id);
      $applicant->email = $email;
      $applicant->password = app('hash')->make($password);
      $applicant->firstname = $firstname;
      $applicant->middlename = $middlename;
      $applicant->lastname = $lastname;
      $applicant->rank = $rank;
      $applicant->address = $address;
      $applicant->designation = $designation;
      $applicant->office = $office;
      $applicant->mobile = $mobile;
      $applicant->telephone = $telephone;
      $applicant->pnp_id_picture = $pnpImagePath;
      $applicant->status = "pending";
      $applicant->email_sent = 0;
      $applicant->save();
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
        'office' => $office,
        'mobile' => $mobile,
        'telephone' => $telephone,
        'pnp_id_picture' => $pnpImagePath,
        'status' => 'pending',
      ]);

      $msg = $application->wasRecentlyCreated ? "Application has been sent!" : "Your application has been updated!";
    }

    return redirect('/application')->with('success', $msg);
    
  }

  public function applicants(Request $request) {
    $search = $request->get('search');
    $from = $request->get('from');
    $to = $request->get('to');
    $status = $request->get('status') ?? 'pending';
    $page = $request->get('page') ?? 1;

    $success = $request->get('success');
    $send_email_to = $request->get('send_email_to');

    Paginator::currentPageResolver(function() use ($page) {
      return $page;
    });

    $applicants = Applicant::where('status', $status)
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
    $applicant = Applicant::find($id);
    if ($applicant) {
      $applicant->status = $status;
      $applicant->remarks = $remarks;

      // Move applicant as a user if approved
      if ($status == 'approved') {
        $today = Carbon::now();
        $applicant->verified_by = $user->id;
        $applicant->verified_date = $today->toDateTimeString();

        User::create([
          'type' => 'user',
          'email' => $applicant->email,
          'password' => $applicant->password,
          'firstname' => $applicant->firstname,
          'middlename' => $applicant->middlename,
          'lastname' => $applicant->lastname,
          'rank' => $applicant->rank,
          'address' => $applicant->address,
          'designation' => $applicant->designation,
          'office' => $applicant->office,
          'mobile' => $applicant->mobile,
          'telephone' => $applicant->telephone,
          'pnp_id_picture' => $applicant->pnp_id_picture,
        ]);
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
