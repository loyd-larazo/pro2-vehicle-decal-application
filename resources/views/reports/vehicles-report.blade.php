@extends('reports.report-layout')

@section('content')
<div class="mb-12">
  <div class="">
    <h2>{{ ucfirst($status) }} Vehicles</h2>

    <div class="px-4">
      <table class="table mb-4">
        <thead>
          <tr>
            <th>Full Name</th>
            <th>Type</th>
            <th>Plate Number</th>
            <th>Make</th>
            <th>Series</th>
            <th>Year Model</th>
            <th>Color</th>
            <th>Engine No.</th>
            <th>Chassis No.</th>
            <th>Code</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($vehicles as $vehicle)
            <tr>
              <td>{{ ucwords($vehicle->user->firstname . " " . $vehicle->user->middlename . " " . $vehicle->user->lastname) }}</td>
              <td>{{ ucfirst($vehicle->type) }}</td>
              <td>{{ $vehicle->plate_number }}</td>
              <td>{{ $vehicle->make }}</td>
              <td>{{ $vehicle->model }}</td>
              <td>{{ $vehicle->year_model }}</td>
              <td>{{ $vehicle->color }}</td>
              <td>{{ $vehicle->engine_number }}</td>
              <td>{{ $vehicle->chassis_number }}</td>
              <td>{{ $vehicle->code }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection