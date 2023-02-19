@extends('reports.report-layout')

@section('content')
<div class="mb-12">
  <div class="">
    <h2>{{ ucfirst($userType) }}</h2>

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
          @foreach ($users as $user)
            <tr>
              <td>{{ ucfirst($user->firstname . " " . $user->middlename . " " . $user->lastname) }}</td>
              <td>{{ $user->email }}</td>
              <td>{{ $user->rank }}</td>
              <td>{{ $user->address }}</td>
              <td>{{ $user->designation }}</td>
              <td>{{ $user->office }}</td>
              <td>{{ $user->mobile }}</td>
              <td>{{ $user->telephone }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection