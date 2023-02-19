@extends('reports.report-layout')

@section('content')
<div class="mb-12">
  <div class="">
    <h2>Applicant Information</h2>

    <div class="px-4">
      <table class="table mb-4">
        <tbody>
          <tr>
            <th>Full Name</th>
            <td>{{ ucfirst($applicant->firstname . " " . $applicant->middlename . " " . $applicant->lastname) }}</td>
          </tr>
          <tr>
            <th>Email Address</th>
            <td>{{ $applicant->email }}</td>
          </tr>
          <tr>
            <th>Rank</th>
            <td>{{ $applicant->rank }}</td>
          </tr>
          <tr>
            <th>Address</th>
            <td>{{ $applicant->address }}</td>
          </tr>
          <tr>
            <th>Designation/Position</th>
            <td>{{ $applicant->designation }}</td>
          </tr>
          <tr>
            <th>Office/Unit Assignment</th>
            <td>{{ $applicant->office }}</td>
          </tr>
          <tr>
            <th>Mobile Number</th>
            <td>{{ $applicant->mobile }}</td>
          </tr>
          <tr>
            <th>Telephone Number</th>
            <td>{{ $applicant->telephone }}</td>
          </tr>
          <tr>
            <th>PNP ID Picture</th>
            <td>
              @if ($applicant->pnp_id_picture)
                <img class="pnp-report" src="/storage/{{ $applicant->pnp_id_picture }}" />
              @endif
            </td>
          </tr>
          <tr>
            <th>Status</th>
            <td>{{ ucfirst($applicant->status) }}</td>
          </tr>
          <tr>
            <th>Verified By</th>
            <td>{{ $applicant->verified->firstname . " " . $applicant->verified->middlename . " " . $applicant->verified->lastname }}</td>
          </tr>
          <tr>
            <th>Verified Date</th>
            <td>{{ $applicant->verified_date }}</td>
          </tr>
          @if ($applicant->remarks)
            <tr>
              <th>Remarks</th>
              <td>{{ $applicant->remarks }}</td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection