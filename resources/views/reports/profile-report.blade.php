@extends('reports.report-layout')

@section('content')
<div class="mb-12">
  <div class="">
    <h2>Profile Information</h2>

    <div class="px-4">
      <table class="table mb-4">
        <tbody>
          <tr>
            <th>Full Name</th>
            <td>{{ ucfirst($user->firstname . " " . $user->middlename . " " . $user->lastname) }}</td>
          </tr>
          <tr>
            <th>Email Address</th>
            <td>{{ $user->email }}</td>
          </tr>
          <tr>
            <th>Rank</th>
            <td>{{ $user->rank }}</td>
          </tr>
          <tr>
            <th>Address</th>
            <td>{{ $user->address }}</td>
          </tr>
          <tr>
            <th>Designation/Position</th>
            <td>{{ $user->designation }}</td>
          </tr>
          <tr>
            <th>Office/Unit Assignment</th>
            <td>{{ $user->office }}</td>
          </tr>
          <tr>
            <th>Mobile Number</th>
            <td>{{ $user->mobile }}</td>
          </tr>
          <tr>
            <th>Telephone Number</th>
            <td>{{ $user->telephone }}</td>
          </tr>
          <tr>
            <th>PNP ID Picture</th>
            <td>
              @if ($user->pnp_id_picture)
                <img class="pnp-report" src="/storage/{{ $user->pnp_id_picture }}" />
              @endif
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <hr />

    @if ($user->vehicles && count($user->vehicles))
      <h2>Vehicle Information</h2>
      @foreach ($user->vehicles as $vehicle)
        <div class="px-4">
          <table class="table mb-4 px-4">
            <tbody>
              <tr>
                <th>Status</th>
                <td>{{ ucfirst($vehicle->verified_status) }}</td>
              </tr>
              <tr>
                <th>Vehicle Type</th>
                <td>{{ ucfirst($vehicle->type) }}</td>
              </tr>
              <tr>
                <th>Plate Number</th>
                <td>{{ $vehicle->plate_number }}</td>
              </tr>
              <tr>
                <th>Make</th>
                <td>{{ $vehicle->make }}</td>
              </tr>
              <tr>
                <th>Series</th>
                <td>{{ $vehicle->model }}</td>
              </tr>
              <tr>
                <th>Year Model</th>
                <td>{{ $vehicle->year_model }}</td>
              </tr>
              <tr>
                <th>Color</th>
                <td>{{ $vehicle->color }}</td>
              </tr>
              <tr>
                <th>Engine Number</th>
                <td>{{ $vehicle->engine_number }}</td>
              </tr>
              <tr>
                <th>Chassis Number</th>
                <td>{{ $vehicle->chassis_number }}</td>
              </tr>
              <tr>
                <th>OR/CR</th>
                <td>
                  @if ($vehicle->or_cr)
                    <img class="pnp-report" src="/storage/{{ $vehicle->or_cr }}" />
                  @endif
                </td>
              </tr>
              <tr>
                <th>Photos of Vehicle</th>
                <td>
                  @if ($vehicle->photos)
                    @foreach ($vehicle->photos as $photo)
                      <img class="pnp-report" src="/storage/{{ $photo->image }}" />
                    @endforeach
                  @endif
                </td>
              </tr>
              @if ($vehicle->code)
                <tr>
                  <th>Code</th>
                  <td>{{ $vehicle->code }}</td>
                </tr>
                <tr>
                  <th>QR Code</th>
                  <td>
                    <div>{!! $vehicle->qr_code !!}</div>
                  </td>
                </tr>
              @endif
            </tbody>
          </table>
        </div>
        <hr />
      @endforeach
    @endif
  </div>
</div>
@endsection