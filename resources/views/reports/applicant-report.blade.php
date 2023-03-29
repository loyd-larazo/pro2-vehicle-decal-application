@extends('reports.report-layout')

@section('content')
<div class="mb-12">
  <div class="">
    <h2>{{ isset($title) ? $title : 'Applicant' }} Information</h2>

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
          @if ($applicant->rank == 'CIV')
            <tr>
              <th>Endorser</th>
              <td>{{ $applicant->endorser }}</td>
            </tr>
            <tr>
              <th>Endorser ID</th>
              <td>
                @if ($applicant->endorser_id)
                  <img class="pnp-report" src="/storage/{{ $applicant->endorser_id }}" />
                @endif
              </td>
            </tr>
            <tr>
              <th>Driver's License</th>
              <td>
                @if ($applicant->drivers_license)
                  <img class="pnp-report" src="/storage/{{ $applicant->drivers_license }}" />
                @endif
              </td>
            </tr>
          @endif
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
          @if (!isset($title))
            <tr>
              <th>Status</th>
              <td>{{ ucfirst($applicant->status) }}</td>
            </tr>
          @endif
          @if ($applicant->verified)
            <tr>
              <th>Verified By</th>
              <td>{{ $applicant->verified->firstname . " " . $applicant->verified->middlename . " " . $applicant->verified->lastname }}</td>
            </tr>
            <tr>
              <th>Verified Date</th>
              <td>{{ $applicant->verified_date }}</td>
            </tr>
          @endif
          @if ($applicant->remarks)
            <tr>
              <th>Remarks</th>
              <td>{{ $applicant->remarks }}</td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
    <hr />

    @if ($applicant->vehicle)
      <h2>Vehicle Information</h2>
      <div class="px-4">
        <table class="table mb-4 px-4">
          <tbody>
            <tr>
              <th>Status</th>
              <td>{{ ucfirst($applicant->vehicle->verified_status) }}</td>
            </tr>
            <tr>
              <th>Vehicle Type</th>
              <td>{{ ucfirst($applicant->vehicle->type) }}</td>
            </tr>
            <tr>
              <th>Plate Number</th>
              <td>{{ $applicant->vehicle->plate_number }}</td>
            </tr>
            <tr>
              <th>Make</th>
              <td>{{ $applicant->vehicle->make }}</td>
            </tr>
            <tr>
              <th>Series</th>
              <td>{{ $applicant->vehicle->model }}</td>
            </tr>
            <tr>
              <th>Year Model</th>
              <td>{{ $applicant->vehicle->year_model }}</td>
            </tr>
            <tr>
              <th>Color</th>
              <td>{{ $applicant->vehicle->color }}</td>
            </tr>
            <tr>
              <th>Engine Number</th>
              <td>{{ $applicant->vehicle->engine_number }}</td>
            </tr>
            <tr>
              <th>Chassis Number</th>
              <td>{{ $applicant->vehicle->chassis_number }}</td>
            </tr>
            <tr>
              <th>Do you own the vehicle?</th>
              <td>{{ $applicant->vehicle->own_vehicle ? 'Yes' : 'No' }}</td>
            </tr>
            @if(!$applicant->vehicle->own_vehicle)
              <tr>
                <th>Deed of Sale</th>
                <td>
                  <img class="pnp-report" src="/storage/{{ $applicant->vehicle->deed_of_sale }}" />
                </td>
              </tr>
            @endif
            <tr>
              <th>OR</th>
              <td>
                @if ($applicant->vehicle->or)
                  <img class="pnp-report" src="/storage/{{ $applicant->vehicle->or }}" />
                @endif
              </td>
            </tr>
            <tr>
              <th>CR</th>
              <td>
                @if ($applicant->vehicle->cr)
                  <img class="pnp-report" src="/storage/{{ $applicant->vehicle->cr }}" />
                @endif
              </td>
            </tr>
            <tr>
              <th>Photos of Vehicle</th>
              <td>
                @if ($applicant->vehicle->photos)
                  @foreach ($applicant->vehicle->photos as $photo)
                    <img class="pnp-report" src="/storage/{{ $photo->image }}" />
                  @endforeach
                @endif
              </td>
            </tr>
            @if ($applicant->vehicle->code)
              <tr>
                <th>Code</th>
                <td>{{ $applicant->vehicle->code }}</td>
              </tr>
              <tr>
                <th>QR Code</th>
                <td>
                  <div>{!! $applicant->vehicle->qr_code !!}</div>
                </td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>
@endsection