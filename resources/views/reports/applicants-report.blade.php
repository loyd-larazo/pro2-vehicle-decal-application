@extends('reports.report-layout')

@section('content')
<div class="mb-12">
  <div class="">
    <h2>{{ ucfirst($status) }} Applicants</h2>

    <div class="px-4">
      <table class="table mb-4">
        <thead>
          <tr>
            <th>Full Name</th>
            <th>Email</th>
            <th>Rank</th>
            <th>Address</th>
            <th>Designation</th>
            <th>Office/Unit</th>
            <th>Mobile</th>
            <th>Telephone</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($applicants as $applicant)
            <tr>
              <td>{{ ucfirst($applicant->firstname . " " . $applicant->middlename . " " . $applicant->lastname) }}</td>
              <td>{{ $applicant->email }}</td>
              <td>{{ $applicant->rank }}</td>
              <td>{{ $applicant->address }}</td>
              <td>{{ $applicant->designation }}</td>
              <td>{{ $applicant->office }}</td>
              <td>{{ $applicant->mobile }}</td>
              <td>{{ $applicant->telephone }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection