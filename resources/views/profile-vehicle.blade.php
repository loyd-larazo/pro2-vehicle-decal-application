@extends('layout')

@section('content')
  <div class="row p-0 m-0 mb-2">
    <img class="logo-heading col-auto " src="/images/logo.png"/>
    <h1 class="col mt-3">My Vehicles</h1>
    <div class="col-auto">
      <button class="btn btn-primary btn-sm mt-4 viewVehicle" data-action="add" data-bs-toggle="modal" data-bs-target="#viewVehicleModal">
        <i class="fa-solid fa-plus"></i> Vehicle
      </button>
    </div>
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
    </div>

    <form class="row mb-2" action="/profile/vehicles" method="GET">
      <div class="col-auto mt-2">
        <div class="input-group">
          <select class="form-select" onchange="this.form.submit()" id="status" name="status">
            <option value="pending" {{ $status && $status == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ $status && $status == 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ $status && $status == 'rejected' ? 'selected' : '' }}>Rejected</option>
          </select>
          <div class="form-outline pt-1 ms-2">
            Vehicle Status
          </div>
        </div>
      </div>
      <div class="col-auto mt-2">
      </div>
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
            <th scope="col">Type</th>
            <th scope="col">Plate Number</th>
            <th scope="col">Make</th>
            <th scope="col">Series</th>
            <th scope="col">Year Model</th>
            <th scope="col">Color</th>
            <th scope="col">Status</th>
            <th scope="col">Decal Status</th>
            <th scope="col">Expiration</th>
            <th scope="col"></th>
          </tr>
        </thead>
        <tbody>
          @if ($vehicles && count($vehicles))
            @foreach ($vehicles as $vehicle)
              <tr>
                <td>{{ ucfirst($vehicle->type) }}</td>
                <td>{{ $vehicle->plate_number }}</td>
                <td>{{ $vehicle->make }}</td>
                <td>{{ $vehicle->model }}</td>
                <td>{{ $vehicle->year_model }}</td>
                <td>{{ $vehicle->color }}</td>
                <td>{{ ucfirst($vehicle->verified_status) }}</td>
                <td>{{ ucfirst($vehicle->issued_status) }}</td>
                <td>{{ $vehicle->expiration_date ?? "xxxx-xx-xx" }}</td>
                <td>
                  <button class="btn btn-sm btn-primary viewVehicle" data-status="{{ $vehicle->verified_status }}" data-action="view" data-id="{{ $vehicle->id }}" data-json="{{ json_encode($vehicle) }}" data-bs-toggle="modal" data-bs-target="#viewVehicleModal">View</button>
                  @if ($vehicle->issued_status == 'expired')
                    <button class="btn btn-sm btn-success renewVehicle" data-id="{{ $vehicle->id }}" data-json="{{ json_encode($vehicle) }}" data-bs-toggle="modal" data-bs-target="#verifyModal">Renew</button>
                  @endif
                  {{-- @if (Session::get('userType') && in_array(Session::get('userType'), ["user"]))
                    <button class="btn btn-sm btn-warning viewVehicle" data-action="edit" data-id="{{ $vehicle->id }}" data-json="{{ json_encode($vehicle) }}" data-bs-toggle="modal" data-bs-target="#viewVehicleModal">Edit</button>
                  @endif --}}
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

  <div class="modal fade" id="viewVehicleModal" tabindex="-1" aria-labelledby="viewVehicleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form id="userForm" class="modal-content" action="/profile/vehicles" method="POST" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="viewVehicleModalLabel"><span id="modalHeader">Add</span> Vehicle</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="_token" value="{{ csrf_token() }}" />
          <div id="jsError" class="alert alert-danger text-center d-none" role="alert"></div>
          <input type="hidden" name="id" id="userId" />
          
          <div class="row mb-3">
            <div class="col-md-6">
              <div class="form-floating">
                <select class="form-control" id="type" name="type" required>
                  <option value="">Select vehicle type</option>
                  <option value="motor">Motor</option>
                  <option value="car">Car</option>
                </select>
                <label for="type">Vehicle Type</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating mb-3 mb-md-0">
                <input class="form-control" id="plateNumber" type="text" name="plate_number" placeholder="Enter your plate number" required />
                <label for="plateNumber">Plate Number</label>
              </div>
            </div>
          </div>

          <div class="form-floating mb-3">
            <input class="form-control" id="make" type="text" name="make" placeholder="Enter Make" required />
            <label for="make">Make</label>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <div class="form-floating mb-3 mb-md-0">
                <input class="form-control" id="model" type="text" name="model" placeholder="Enter Series" required />
                <label for="model">Series</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <select class="form-control" id="yearModel" name="year_model" required>
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
            <input class="form-control" id="color" type="text" name="color" placeholder="Enter Color" required />
            <label for="color">Color</label>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <div class="form-floating mb-3 mb-md-0">
                <input class="form-control" id="engineNumber" type="text" name="engine_number" placeholder="Enter Engine Number" required />
                <label for="engineNumber">Engine Number</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating mb-3 mb-md-0">
                <input class="form-control" id="chassisNumber" type="text" name="chassis_number" placeholder="Enter Chassis Number" required />
                <label for="chassisNumber">Chassis Number</label>
              </div>
            </div>
          </div>
          
          <div>
            <div id="orCrFile" class="form-floating mb-3">
              <input class="form-control file" required id="orCr" data-target="src" data-preview="#orCrPreview" type="file" name="or_cr" accept="image/*" placeholder="Upload your OR/CR" />
              <label for="orCr">OR/CR</label>
            </div>
            <label id="orCrLabel">OR/CR</label>
            <div class="form-floating mb-3 text-center">
              <img id="orCrPreview" class="preview-images prev-image"/>
            </div>
          </div>
          
          <div>
            <div id="photosFile" class="form-floating mb-3">
              <input class="form-control file" required id="photos" data-target="element" data-preview=".photos-preview" type="file" name="photos[]" accept="image/*" placeholder="Upload photo of your vehicle" multiple/>
              <label for="photos">Photo of Vehicle</label>
            </div>
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
          <button id="saveModal" type="submit" class="btn btn-success">Save</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>

  <div class="modal fade" id="verifyModal" tabindex="-1" aria-labelledby="verifyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="verifyModalLabel">Renew Vehicle</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to renew this vehicle?
        </div>
        <div class="modal-footer">
          <a href="#" id="renew" class="btn btn-success">Yes</a>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    $(function() {
      function loadImage(file, type, target) {
        let reader = new FileReader();
        reader.onload = function(event) {
          if (type == 'src') {
            $(target).attr('src', event.target.result);
          } else if (type == 'element') {
            $(target).append(`<img class="preview-images prev-image" src="${event.target.result}"/>`);
            initImagePreview();
          }
        }
        reader.readAsDataURL(file);
      }

      $('#userForm').submit(function(e) {
        e.preventDefault();
        hideError();

        var plateNum = $('#plateNumber').val();
        $.get(`/vehicle/user/plate/${plateNum}`, (data, status) => {
          console.log(data.data);
          if (data.data) {
            showError("Plate number already exists.");
          } else {
            $(this).unbind('submit').submit();
          }
        });
      });

      $('.file').change(function() {
        const preview = $(this).data('preview');
        const target = $(this).data('target');

        for (var i = 0; i < this.files.length; i++) {
          let file = this.files[i];
          loadImage(file, target, preview);
        }
      });

      $('.renewVehicle').click(function() {
        var id = $(this).data('id');
        $('#renew').attr('href', `/release/${id}/renew`)
      })

      $('.viewVehicle').click(function() {
        var data = $(this).data('json');
        var action = $(this).data('action');
        var id = $(this).data('id');

        var verifiedStatus = $(this).data('status');

        if (verifiedStatus == 'approved') {
          $('#codeForm').removeClass('d-none');
          $('#code').val(data ? data.code : '');
          $('#qrCode').html(data ? data.qr_code : '');
          $('#downloadQrCode').attr('data-name', data ? data.code : '');
        } else {
          $('#codeForm').addClass('d-none');
        }

        $('#userId').val(data ? data.id : '');
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
            photosStr += `<img class="preview-images prev-image" src="/storage/${p.image}"/>`;
          });
        }
        $('.photos-preview').html(photosStr);
        initImagePreview();

        $('#modalHeader').html(capitalize(action));

        if (action == 'add' || action == 'edit') {
          $('select[name="type"]').attr('required', 'required').removeAttr('disabled');
          $('input[name="plate_number"]').attr('required', 'required').removeAttr('disabled');
          $('input[name="make"]').attr('required', 'required').removeAttr('disabled');
          $('input[name="model"]').attr('required', 'required').removeAttr('disabled');
          $('select[name="year_model"]').attr('required', 'required').removeAttr('disabled');
          $('input[name="color"]').attr('required', 'required').removeAttr('disabled');
          $('input[name="engine_number"]').attr('required', 'required').removeAttr('disabled');
          $('input[name="chassis_number"]').attr('required', 'required').removeAttr('disabled');

          $('#orCrFile').removeClass('d-none');
          $('#photosFile').removeClass('d-none');
          $('#orCrLabel').addClass('d-none');
          $('#photosLabel').addClass('d-none');
          $('#saveModal').removeClass('d-none');

          if (data) {
            if (data.or_cr) {
              $('#orCr').removeAttr('required');
            } else {
              $('#orCr').attr('required', 'required');
            }

            if (data.photos && data.photos.length) {
              $('#photos').removeAttr('required');
            } else {
              $('#photos').attr('required', 'required');
            }
          }
        } else if (action == 'view') {
          $('select[name="type"]').removeAttr('required').attr('disabled', 'disabled');
          $('input[name="plate_number"]').removeAttr('required').attr('disabled', 'disabled');
          $('input[name="make"]').removeAttr('required').attr('disabled', 'disabled');
          $('input[name="model"]').removeAttr('required').attr('disabled', 'disabled');
          $('select[name="year_model"]').removeAttr('required').attr('disabled', 'disabled');
          $('input[name="color"]').removeAttr('required').attr('disabled', 'disabled');
          $('input[name="engine_number"]').removeAttr('required').attr('disabled', 'disabled');
          $('input[name="chassis_number"]').removeAttr('required').attr('disabled', 'disabled');
          
          $('#orCrFile').addClass('d-none');
          $('#photosFile').addClass('d-none');
          $('#orCrLabel').removeClass('d-none');
          $('#photosLabel').removeClass('d-none');
          $('#saveModal').addClass('d-none');
        }
      })
    });
    
  </script>
@endsection