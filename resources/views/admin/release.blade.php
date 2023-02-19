@extends('layout')

@section('content')
  <div class="row p-0 m-0 mb-2">
    <img class="logo-heading col-auto " src="/images/logo.png"/>
    <h1 class="col mt-3">For Release</h1>
  </div>
              
  <div class="card mb-4">
    <div class="card-body">

      @if(\Session::get('error') || isset($error))
        <div class="alert alert-danger text-center" role="alert">
          {{ \Session::get('error') ?? $error }}
        </div>
      @endif

      @if(\Session::get('success') || isset($success))
        <div class="alert alert-success text-center" role="alert">
          {{ \Session::get('success') ?? $success }}
        </div>
      @endif

      <form class="row mb-2" action="/release" method="GET">
        <div class="col"></div>
        <div class="col-auto mt-2">
          <div class="input-group">
            <div class="form-outline">
              <input type="search" id="search" name="search" class="search form-control" placeholder="Search" value="{{ $search }}"/>
            </div>
            <button type="submit" id="searchBtn" class="btn btn-primary">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </div>
      </form>

      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th scope="col">User</th>
              <th scope="col">Rank</th>
              <th scope="col">Type</th>
              <th scope="col">Plate Number</th>
              <th scope="col">Manufacturer</th>
              <th scope="col">Model</th>
              <th scope="col">Year Model</th>
              <th scope="col"></th>
            </tr>
          </thead>
          <tbody>
            @if ($vehicles && count($vehicles))
              @foreach ($vehicles as $vehicle)
                <tr>
                  <td>{{ ucfirst($vehicle->user->firstname . " " . $vehicle->user->middlename . " ". $vehicle->user->lastname) }}</td>
                  <td>{{ $vehicle->user->rank }}</td>
                  <td>{{ ucfirst($vehicle->type) }}</td>
                  <td>{{ $vehicle->plate_number }}</td>
                  <td>{{ $vehicle->make }}</td>
                  <td>{{ $vehicle->model }}</td>
                  <td>{{ $vehicle->year_model }}</td>
                  <td>
                    <button class="btn btn-sm btn-primary viewVehicle" data-type="vehicle" data-json="{{ json_encode($vehicle) }}" data-bs-toggle="modal" data-bs-target="#viewVehicleModal">Vehicle</button>
                    <button class="btn btn-sm btn-primary viewVehicle" data-type="user" data-json="{{ json_encode($vehicle) }}" data-bs-toggle="modal" data-bs-target="#viewUserModal">User</button>
                    <button class="btn btn-sm btn-success verify" data-id="{{ $vehicle->id }}" data-type="{{ $vehicle->issued_status == 'renewal' ? 'renew' : 'release' }}" data-bs-toggle="modal" data-bs-target="#verifyModal">{{ $vehicle->issued_status == 'renewal' ? 'Renew' : 'Release'}}</button>
                  </td>
                </tr>
              @endforeach
            @else
              <tr>
                <th colspan="9" class="text-center">No vehicle found</th>
              </tr>
            @endif
          </tbody>
          
          @if ($vehicles && count($vehicles))
            <tfoot>
              <tr>
                <th colspan="12" class="text-center">
                  <div class="row g-3 align-items-center m-auto">
                    <div class="col-auto">
                      <label class="col-form-label">Page</label>
                    </div>
                    <div class="col-auto">
                      <select class="form-select page-select">
                        @for($i = 1; $i <= $vehicles->lastPage(); $i++)
                          <option value="{{ $i }}" {{ $vehicles->currentPage() == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                      </select>
                    </div>
                    <div class="col-auto">
                      <label class="col-form-label">of {{ $vehicles->lastPage() }}</label>
                    </div>
                  </div>
                </th>
              </tr>
            </tfoot>
          @endif
        </table>
      </div>
    </div>
  </div>

  <div class="modal fade" id="viewVehicleModal" tabindex="-1" aria-labelledby="viewVehicleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div id="userForm" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewVehicleModalLabel"><span id="modalHeader">View</span> Vehicle</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          
          <div class="row mb-3">
            <div class="col-md-6">
              <div class="form-floating">
                <select class="form-control" id="type" name="type" disabled>
                  <option value="">Select vehicle type</option>
                  <option value="motor">Motor</option>
                  <option value="car">Car</option>
                </select>
                <label for="type">Vehicle Type</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating mb-3 mb-md-0">
                <input class="form-control" id="plateNumber" type="text" name="plate_number" placeholder="Enter your plate number" disabled />
                <label for="plateNumber">Plate Number</label>
              </div>
            </div>
          </div>

          <div class="form-floating mb-3">
            <input class="form-control" id="make" type="text" name="make" placeholder="Enter Manufacturer" disabled />
            <label for="make">Manufacturer</label>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <div class="form-floating mb-3 mb-md-0">
                <input class="form-control" id="model" type="text" name="model" placeholder="Enter Model" disabled />
                <label for="model">Model</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <select class="form-control" id="yearModel" name="year_model" disabled>
                  <option value="">Select Year Model</option>
                  @for ($i = date("Y"); $i >= 1850; $i--)
                    <option value="{{ $i }}">{{ $i }}</option>
                  @endfor
                </select>
                <label for="yearModel">Year Model</label>
              </div>
            </div>
          </div>

          <div class="form-floating mb-3">
            <input class="form-control" id="color" type="text" name="color" placeholder="Enter Color" disabled />
            <label for="color">Color</label>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <div class="form-floating mb-3 mb-md-0">
                <input class="form-control" id="engineNumber" type="text" name="engine_number" placeholder="Enter Engine Number" disabled />
                <label for="engineNumber">Engine Number</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating mb-3 mb-md-0">
                <input class="form-control" id="chassisNumber" type="text" name="chassis_number" placeholder="Enter Chassis Number" disabled />
                <label for="chassisNumber">Chassis Number</label>
              </div>
            </div>
          </div>
          
          <div>
            <label id="orCrLabel">OR/CR</label>
            <div class="form-floating mb-3 text-center">
              <img id="orCrPreview" class="preview-images"/>
            </div>
          </div>
          
          <div>
            <label id="photosLabel">Photo of Vehicle</label>
            <div class="form-floating mb-3 text-center photos-preview">
            </div>
          </div>

          <div id="codeForm" class="mt-4">
            <div class="form-floating mb-3">
              <input class="form-control" id="code" type="text" name="code" placeholder="Code" disabled />
              <label for="color">Code</label>
            </div>

            <div>
              <label id="codeLabel">QR Code</label>
              <button class="btn btn-sm btn-primary float-end" id="downloadQrCode"><i class="fa-solid fa-download"></i></button>
              <div id="qrCode" class="qr-code"></div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div id="userForm" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewUserModalLabel">User Profile</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row mb-3">
            <div class="col-md-6">
              <div class="form-floating mb-3 mb-md-0">
                <input disabled class="form-control" id="firstname" type="text" name="firstname" placeholder="Enter your first name" />
                <label for="firstname">First name</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input disabled class="form-control" id="middlename" type="text" name="middlename" placeholder="Enter your middle name" />
                <label for="middlename">Middle name</label>
              </div>
            </div>
          </div>
          <div class="form-floating mb-3">
            <input disabled class="form-control" id="lastname" type="text" name="lastname" placeholder="Enter your last name" />
            <label for="lastname">Last name</label>
          </div>
          <div class="form-floating mb-3">
            <input disabled class="form-control" id="email" type="email" name="email" placeholder="name@example.com" />
            <label for="email">Email address</label>
          </div>
          <div class="form-floating mb-3">
            <select class="form-control" id="rank" name="rank" disabled>
              <option value="">Select your Rank</option>
              <option value="PGEN">PGEN</option>
              <option value="PLTGEN">PLTGEN</option>
              <option value="PMGEN">PMGEN</option>
              <option value="PBGEN">PBGEN</option>
              <option value="PCOL">PCOL</option>
              <option value="PLTCOL">PLTCOL</option>
              <option value="PMAJ">PMAJ</option>
              <option value="PCPT">PCPT</option>
              <option value="PLT">PLT</option>
              <option value="PEMS">PEMS</option>
              <option value="PCMS">PCMS</option>
              <option value="PSMS">PSMS</option>
              <option value="PMSg">PMSg</option>
              <option value="PSSg">PSSg</option>
              <option value="PCpl">PCpl</option>
            </select>
            <label for="rank">Rank</label>
          </div>
          <div class="form-floating mb-3">
            <textarea disabled class="form-control" id="address" name="address"></textarea>
            <label for="address">Address</label>
          </div>
          <div class="form-floating mb-3">
            <input disabled class="form-control" id="designation" type="text" name="designation" placeholder="Enter your Designation/Position" />
            <label for="designation">Designation/Position</label>
          </div>
          <div class="form-floating mb-3">
            <input disabled class="form-control" id="office" type="text" name="office" placeholder="Enter your Office/Unit Assignment" />
            <label for="office">Office/Unit Assignment</label>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <div class="form-floating mb-3">
                <input disabled class="form-control" id="mobile" type="text" name="mobile" placeholder="Enter your Mobile Number" />
                <label for="mobile">Mobile Number</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating mb-3">
                <input disabled class="form-control" id="telephone" type="text" name="telephone" placeholder="Enter your Telephone Number" />
                <label for="telephone">Telephone Number</label>
              </div>
            </div>
          </div>
          <div id="idPreview">
            <label for="pnpId">PNP ID Picture</label>
            <div class="form-floating mb-3 text-center">
              <img id="imgPreview" />
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="verifyModal" tabindex="-1" aria-labelledby="verifyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="verifyModalLabel"><span class="modalType"></span> Sticker/Passcard</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to <span class="modalType"></span> this Sticker/Passcard?
        </div>
        <div class="modal-footer">
          <button type="button" id="verifyBtn" class="btn btn-success">Yes</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    $(function() {

      $('.page-select').change(function() {
        var search = $('#search').val();
        var status = $('#status').val();
        var page = $(this).val();
        location.href = `/release?search=${search}&page=${page}&status=${status}`;
      });

      $('.viewVehicle').click(function() {
        var data = $(this).data('json');
        var type = $(this).data('type');
        var verifiedStatus = $(this).data('status');

        $('#codeForm').removeClass('d-none');
        $('#code').val(data ? data.code : '');
        $('#qrCode').html(data ? data.qr_code : '');
        $('#downloadQrCode').attr('data-name', data ? data.code : '');

        if (type == 'vehicle') {
          $('select[name="type"]').val(data ? data.type : '');
          $('input[name="plate_number"]').val(data ? data.plate_number : '');
          $('input[name="make"]').val(data ? data.make : '');
          $('input[name="model"]').val(data ? data.model : '');
          $('select[name="year_model"]').val(data ? data.year_model : '');
          $('input[name="color"]').val(data ? data.color : '');
          $('input[name="engine_number"]').val(data ? data.engine_number : '');
          $('input[name="chassis_number"]').val(data ? data.chassis_number : '');

          var orCrPath = ''
          if (data && data.or_cr) {
            orCrPath = `/storage/${data.or_cr}`;
          }
          $('#orCrPreview').attr('src', orCrPath);

          var photosStr = '';
          if (data && data.photos && data.photos.length) {
            data.photos.map(p => {
              photosStr += `<img class="preview-images" src="/storage/${p.image}"/>`;
            });
          }
          $('.photos-preview').html(photosStr);
        } else if (type == 'user') {
          $('input[name="firstname"]').val(data ? data.user.firstname : '');
          $('input[name="middlename"]').val(data ? data.user.middlename : '');
          $('input[name="lastname"]').val(data ? data.user.lastname : '');
          $('input[name="email"]').val(data ? data.user.email : '');
          $('select[name="rank"]').val(data ? data.user.rank : '');
          $('textarea[name="address"]').val(data ? data.user.address : '');
          $('input[name="designation"]').val(data ? data.user.designation : '');
          $('input[name="office"]').val(data ? data.user.office : '');
          $('input[name="mobile"]').val(data ? data.user.mobile : '');
          $('input[name="telephone"]').val(data ? data.user.telephone : '');
          if (data && data.user.pnp_id_picture) {
            $('#imgPreview').attr('src', `/storage/${data.user.pnp_id_picture}`)
          }
        }
      });

      $('.verify').click(function() {
        var id = $(this).data('id');
        var type = $(this).data('type');

        $('.modalType').html(capitalize(type));

        if (type == 'approve') {
          $('#verifyBtn').removeClass('btn-danger').addClass('btn-success');
        }

        $('#verifyBtn').attr('data-id', id).attr('data-type', 'release');
      });

      $('#verifyBtn').click(function() {
        var id = $(this).data('id');
        var type = $(this).data('type');

        location.href = `/release/${id}/${type}`;
      });

      $('#downloadQrCode').click(function() {
        var name = $(this).data('name');
        saveSvg($('#qrCode'), `${name}.svg`);
      })
    });
  </script>
@endsection