@extends('layout')

@section('content')
  <div class="row p-0 m-0 mb-2">
    <img class="logo-heading col-auto " src="/images/logo.png"/>
    <h1 class="col mt-3">{{ ucfirst($userType) }}</h1>
    @if (Session::get('userType') && in_array(Session::get('userType'), ["admin"]))
      <div class="col-auto">
        <button class="btn btn-primary btn-sm mt-4 viewUser" data-type="{{ $userType }}" data-action="add" data-bs-toggle="modal" data-bs-target="#viewUserModal">
          <i class="fa-solid fa-plus"></i> {{ rtrim(ucfirst($userType == 'issuers' ? 'admin' : ($userType == 'admins' ? 'superadmin' : $userType)), "s") }}
        </button>
      </div>
    @endif
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

      <form class="row mb-2" action="/app/{{ $userType }}" method="GET">
        <div class="col-auto mt-2">
          <select name="statusFilter" class="form-select" onchange="this.form.submit()">
            <option {{$status == 'active' ? 'selected' : ''}} value="active">Active {{ ucfirst($userType == 'issuers' ? 'Admin' : ($userType == 'admins' ? 'Superadmins' : $userType)) }}</option>
            <option {{$status == 'disabled' ? 'selected' : ''}} value="disabled">Disabled {{ ucfirst($userType == 'issuers' ? 'Admin' : ($userType == 'admins' ? 'Superadmins' : $userType)) }}</option>
          </select>
        </div>
        <div class="col"></div>
        <div class="col-auto mt-2">
          <div class="input-group">
            <label class="pt-2 me-2">Filter Date: </label>
            <input type="text" id="daterange" name="daterange" class="form-control" />
          </div>
        </div>
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
              <th scope="col">Email</th>
              <th scope="col">Name</th>
              <th scope="col">Rank</th>
              <th scope="col">Mobile</th>
              <th scope="col"></th>
            </tr>
          </thead>
          <tbody>
            @if ($users && count($users))
              @foreach ($users as $user)
                <tr>
                  <td>{{ $user->email }}</td>
                  <td>{{ $user->firstname . " " . $user->middlename . " " . $user->lastname }}</td>
                  <td>{{ $user->rank }}</td>
                  <td>{{ $user->mobile }}</td>
                  <td>
                    <button class="btn btn-sm btn-primary viewUser" data-type="{{ $userType }}" data-action="view" data-id="{{ $user->id }}" data-json="{{ json_encode($user) }}" data-bs-toggle="modal" data-bs-target="#viewUserModal">Info</button>
                    @if($userType == 'users')
                      <button class="btn btn-sm btn-primary viewVehicles" data-id="{{ $user->id }}" data-json="{{ json_encode($user) }}" data-bs-toggle="modal" data-bs-target="#viewVehicleModal">Vehicles</button>
                    @endif

                    @if (Session::get('userType') && in_array(Session::get('userType'), ["admin"]))
                      <button class="btn btn-sm btn-warning viewUser" data-type="{{ $userType }}" data-action="edit" data-id="{{ $user->id }}" data-json="{{ json_encode($user) }}" data-bs-toggle="modal" data-bs-target="#viewUserModal">Edit</button>
                    @endif
                  </td>
                </tr>
              @endforeach
            @else
              <tr>
                <th colspan="4" class="text-center">No {{ ucfirst($userType == 'issuers' ? 'Admin' : ($userType == 'admins' ? 'Superadmins' : $userType)) }} found</th>
              </tr>
            @endif
          </tbody>
          
          @if ($users && count($users))
            <tfoot>
              <tr>
                <th colspan="10" class="text-center">
                  <div class="row m-0 p-0">
                    <div class="col p-0 row g-3 align-items-center m-auto">
                      <div class="col-auto">
                        <label class="col-form-label">Page</label>
                      </div>
                      <div class="col-auto">
                        <select class="form-select page-select">
                          @for($i = 1; $i <= $users->lastPage(); $i++)
                            <option value="{{ $i }}" {{ $users->currentPage() == $i ? 'selected' : '' }}>{{ $i }}</option>
                          @endfor
                        </select>
                      </div>
                      <div class="col-auto">
                        <label class="col-form-label">of {{ $users->lastPage() }}</label>
                      </div>
                    </div>
                    <div class="col p-0 text-end">
                      <a href="/reports/app/{{$userType}}?search={{$search}}&statusFilter={{$status}}&from={{$from}}&to={{$to}}" target="_blank" class="btn btn-info mt-2"> <i class="fa-solid fa-print me-2"></i>Print List</a>
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
    <div class="modal-dialog modal-lg">
      <div id="vehicleForm" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewVehicleModalLabel"><span id="vehiclesHeader"></span> Vehicles</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="table-responsive" id="vehicleTable">
            <table class="table">
              <thead>
                <tr>
                  <th>Type</th>
                  <th>Plate Number</th>
                  <th>Make</th>
                  <th>Series</th>
                  <th>Status</th>
                  <th>Decal Status</th>
                  <th></th>
                </tr>
              </thead>
              <tbody id="vehicleList">
              </tbody>
            </table>
          </div>
          <form action="/profile/vehicles" method="POST" id="vehicleFormInput" class="d-none" enctype="multipart/form-data">
            <div>
              <button id="backToVehicles" class="btn btn-sm btn-primary mb-2">
                <i class="fa-solid fa-arrow-left"></i> back
              </button>
            </div>
            <div id="jsError" class="alert alert-danger text-center d-none" role="alert"></div>
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <input type="hidden" name="id" />
            <input type="hidden" name="userId"/>
            <input type="hidden" name="deedOfSalePath"/>
            <input type="hidden" name="orPath"/>
            <input type="hidden" name="crPath"/>
            <input type="hidden" name="adminSave" value="1"/>
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
  
            <div class="form-floating mb-3">
              <select class="form-control" id="ownVehicle" name="own_vehicle" required>
                <option value="">Select from options</option>
                <option value="yes">Yes</option>
                <option value="no">No</option>
              </select>
              <label for="ownVehicle">Do you own the vehicle?</label>
            </div>
  
            <div id="deedOfSaleField" class="d-none">
              <div id="deedOfSaleFile" class="form-floating mb-3">
                <input class="form-control file" id="deedOfSale" data-target="src" data-preview="#deedOfSalePreview" type="file" name="deed_of_sale" accept="image/*" placeholder="Deed of Sale" />
                <label for="deedOfSale">Deed of Sale</label>
              </div>
              <label id="deedOfSaleLabel">Deed of Sale</label>
              <div class="form-floating mb-3 text-center">
                <img id="deedOfSalePreview" class="preview-images prev-image"/>
              </div>
            </div>
            
            <div>
              <div id="orFile" class="form-floating mb-3">
                <input class="form-control file" required id="or" data-target="src" data-preview="#orPreview" type="file" name="or" accept="image/*" placeholder="Upload your OR" />
                <label for="or">OR</label>
              </div>
              <label id="orLabel">OR</label>
              <div class="form-floating mb-3 text-center">
                <img id="orPreview" class="preview-images prev-image"/>
              </div>
            </div>
  
            <div>
              <div id="crFile" class="form-floating mb-3">
                <input class="form-control file" required id="cr" data-target="src" data-preview="#crPreview" type="file" name="cr" accept="image/*" placeholder="Upload your CR" />
                <label for="cr">CR</label>
              </div>
              <label id="crLabel">CR</label>
              <div class="form-floating mb-3 text-center">
                <img id="crPreview" class="preview-images prev-image"/>
              </div>
            </div>
            
            <div>
              <div id="photosFile" class="form-floating mb-3">
                <input class="form-control file" required id="photos" data-target="element" data-preview=".photos-preview" type="file" name="photos[]" accept="image/*" placeholder="Upload photo of your vehicle" multiple/>
                <label for="photos">Photos of Vehicle</label>
              </div>
              <label id="photosLabel">Photos of Vehicle</label>
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

            <div class="text-center">
              <button type="submit" class="btn btn-success d-none" id="saveVehicle">Save</button>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          @if (Session::get('userType') && in_array(Session::get('userType'), ["admin"]))
            <a class="btn btn-primary veiw-edit-vehicle" id="addVehicle" data-action="add"><i class="fa-solid fa-car"></i> Add</a>
          @endif
          <a id="printVehicleReport" target="_blank" class="btn btn-info"> <i class="fa-solid fa-print me-2"></i>Print</a>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form id="userForm" class="modal-content" action="/app/{{ $userType }}" method="POST" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="viewUserModalLabel"><span id="modalHeader">View</span> {{ rtrim(ucfirst($userType), "s") }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="_token" value="{{ csrf_token() }}" />
          <input type="hidden" name="pnpIdPath"/>
          <input type="hidden" name="driverLicensePath"/>
          <input type="hidden" name="endorserIdPath"/>
          <div id="jsError" class="alert alert-danger text-center d-none" role="alert"></div>
          <input type="hidden" name="id" id="userId" />
          <input type="hidden" name="type" value="{{ rtrim($userType, "s") }}" />

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
          <div class="row m-0 mb-3" id="passwordForm">
            <div class="col-12 form-check" id="changePasswordForm">
              <input type="checkbox" class="form-check-input" id="changePassword" name="change_password">
              <label class="form-check-label" for="changePassword">Change Password</label>
            </div>
            <div id="passwordInputs">
              <div class="row">
                <div class="col-md-6 ps-0">
                  <div class="form-floating mb-3 mb-md-0">
                    <input class="form-control password" id="password" type="password" name="password" placeholder="Create a password" />
                    <label for="password">Password</label>
                  </div>
                </div>
                <div class="col-md-6 pe-0">
                  <div class="form-floating mb-3 mb-md-0">
                    <input class="form-control password" id="passwordConfirm" type="password" name="confirmPassword" placeholder="Confirm password" />
                    <label for="passwordConfirm">Confirm Password</label>
                  </div>
                </div>
              </div>
            </div>
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
              <option value="Patrolman">Patrolman</option>
              <option value="NUP">NUP</option>
              <option value="CIV">CIV</option>
            </select>
            <label for="rank">Rank</label>
          </div>
          <div id="civFields" class="d-none">
            <div class="form-floating mb-3">
              <input class="form-control" id="endorser" type="text" name="endorser" placeholder="Enter your Name of Endorser"/>
              <label for="endorser">Name of Endorser</label>
            </div>

            <div>
              <div class="form-floating mb-3">
                <input class="form-control file" id="endorserId" data-target="src" data-preview="#endorserIdPreview" type="file" name="endorser_id" accept="image/*" placeholder="Upload your Endorser ID"/>
                <label for="endorserId" id="edersorIdLabel">Endorser ID</label>
              </div>
              <div class="form-floating mb-3 text-center">
                <img id="endorserIdPreview" class="preview-images prev-image"/>
              </div>
            </div>

            <div>
              <div class="form-floating mb-3">
                <input class="form-control file" id="driverLicense" data-target="src" data-preview="#driverLicensePreview" type="file" name="driver_license" accept="image/*" placeholder="Upload your Drivers License" />
                <label for="driverLicense" id="DriversLicenseLabel">Driver's License ID</label>
              </div>
              <div class="form-floating mb-3 text-center">
                <img id="driverLicensePreview" class="preview-images prev-image"/>
              </div>
            </div>
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
            <select class="form-control" id="office" name="office" required>
              <option value="">Select Office/Unit Assignment</option>
              <option value="IPPO">IPPO</option>
              <option value="CPPO">CPPO</option>
              <option value="BPPO">BPPO</option>
              <option value="SCPO">SCPO</option>
              <option value="NVPO">NVPO</option>
              <option value="RPRMD">RPRMD</option>
              <option value="RID">RID</option>
              <option value="ROD">ROD</option>
              <option value="RTOC">RTOC</option>
              <option value="RLRDD">RLRDD</option>
              <option value="RCADD">RCADD</option>
              <option value="RCD">RCD</option>
              <option value="RIDMD">RIDMD</option>
              <option value="RLDDD">RLDDD</option>
              <option value="RPSMU">RPSMU</option>
              <option value="RICTMD">RICTMD</option>
              <option value="others">Others</option>
            </select>
            <label for="office">Office/Unit Assignment</label>
          </div>
          <div id="officeFields" class="d-none">
            <div class="form-floating mb-3">
              <input class="form-control" id="otherOffice" type="text" name="otherOffice" placeholder="Other Office/Unit Assignment" />
              <label for="otherOffice">Other Office/Unit Assignment</label>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <div class="form-floating mb-3">
                <input 
                  disabled 
                  class="form-control mobile-number" 
                  id="mobile" 
                  type="text" 
                  name="mobile" 
                  placeholder="Enter your Mobile Number" />
                <label for="mobile">Mobile Number</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating mb-3">
                <input 
                  disabled 
                  class="form-control" 
                  id="telephone" 
                  type="text" 
                  name="telephone" 
                  onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))"
                  placeholder="Enter your Telephone Number" />
                <label for="telephone">Telephone Number</label>
              </div>
            </div>
          </div>
          <div id="idPreview">
            <div class="form-floating mb-3" id="pnpIdUpload">
              <input class="form-control file" id="pnpId" type="file" data-target="src" data-preview="#pnpIdPreview" name="pnp_id" accept="image/*" placeholder="Upload your PNP ID" />
              <label for="pnpId" id="pnpIdLabel">PNP ID Picture</label>
            </div>
            <div class="form-floating mb-3 text-center">
              <img id="pnpIdPreview" class="preview-images prev-image"/>
            </div>
          </div>
          <div class="form-floating mb-3" id="statusForm">
            <select class="form-control" id="status" name="status">
              <option value="">Select Account Status</option>
              <option value="1">Active</option>
              <option value="0">Disabled</option>
            </select>
            <label for="status">Account Status</label>
          </div>
        </div>
        <div class="modal-footer">
          <button id="saveModal" type="submit" class="btn btn-success">Save</button>
          <a id="printReport" target="_blank" class="btn btn-info"> <i class="fa-solid fa-print me-2"></i>Print</a>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    $(function() {
      const userType = "{{ $userType }}";

      initDatePicker();
      secureMobile();
      officeChange();
      if (userType == 'users') {
        rankChange();
        ownVehicleChange();
      }

      function initDatePicker() {
        const fromStr = "{{ isset($from) ? $from : '' }}";
        const toStr = "{{ isset($to) ? $to : '' }}";
        var pickerParams = {
          opens: 'left',
          maxDate: moment(),
          locale: {
            cancelLabel: 'Clear'
          }
        };
        if (fromStr && toStr) {
          pickerParams.startDate = moment(fromStr, 'YYYY/MM/DD');
          pickerParams.endDate = moment(toStr, 'YYYY/MM/DD');
        } else {
          pickerParams.autoUpdateInput = false;
        }
        $('input[name="daterange"]').daterangepicker(pickerParams);

        $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
          const from = picker.startDate.format('YYYY/MM/DD');
          const to = picker.endDate.format('YYYY/MM/DD');
          $(this).val(from + ' - ' + to);
          location.href = `/app/{{$userType}}?from=${from}&to=${to}`;
        });

        $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            location.href = `/app/{{$userType}}`;
        });
      }

      $('.viewVehicles').click(function() {
        var data = $(this).data('json');
        var vehicles = data.vehicles;
        var vehicleLists = ``;
        $('input[name="userId"]').val(data ? data.id : '');
        if (!vehicles.length) {
          vehicleLists = `
            <tr>
              <td colspan="7" class="text-center">No Vehicles found</td>
            </tr>
          `;
          $('#printVehicleReport').addClass('d-none');
        } else {
          var userId = null;
          vehicles.map(vehicle => {
            userId = vehicle.user_id;
            vehicleLists += `
              <tr>
                <td class="text-capitalize">${vehicle.type}</td>
                <td>${vehicle.plate_number}</td>
                <td>${vehicle.make}</td>
                <td>${vehicle.model}</td>
                <td class="text-capitalize">${vehicle.verified_status}</td>
                <td class="text-capitalize">${vehicle.issued_status}</td>
                <td>
                  <button class="btn btn-sm btn-primary veiw-edit-vehicle" data-action="view" data-json='${JSON.stringify(vehicle)}'>View</button>  
                  @if (Session::get('userType') && in_array(Session::get('userType'), ["admin"]))
                    <button class="btn btn-sm btn-warning veiw-edit-vehicle" data-action="edit" data-json='${JSON.stringify(vehicle)}'>Edit</button>  
                  @endif
                </td>
              </tr>
            `;
            $('#printVehicleReport').removeClass('d-none');
          });

          $('#printVehicleReport').attr('href', `/report/user/${userId}/vehicles`);
        }
        $('#vehiclesHeader').html(`${data.firstname} ${data.lastname}`);
        $('#vehicleList').html(vehicleLists);

        $('#vehicleFormInput').addClass('d-none');
        $('#vehicleTable').removeClass('d-none');
        $('#addVehicle').removeClass('d-none');
        if (!vehicles.length) {
          $('#printVehicleReport').removeClass('d-none');
        }
        $('#saveVehicle').addClass('d-none');

        initVehicleEvent();
      });

      $('#backToVehicles').click(function() {
        $('#vehicleFormInput').addClass('d-none');
        $('#vehicleTable').removeClass('d-none');
        $('#addVehicle').removeClass('d-none');
        $('#printVehicleReport').removeClass('d-none');
        $('#saveVehicle').addClass('d-none');
      });

      $('#vehicleFormInput').submit(function(e) {
        e.preventDefault();
        hideError();

        if ($('#ownVehicle').val() == 'no' && !$('#deedOfSale').val()) {
          return showError("Please enter all required fields!");
        }

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

      function initVehicleEvent() {
        $('.veiw-edit-vehicle').click(function() {
          $('#vehicleFormInput').removeClass('d-none');
          $('#vehicleTable').addClass('d-none');
          $('#addVehicle').addClass('d-none');
          hideError();
          
          const vehicle = $(this).data('json');
          const action = $(this).data('action');
          console.log(vehicle);

          if (vehicle && vehicle.verified_status == 'approved') {
            $('#codeForm').removeClass('d-none');
            $('#code').val(vehicle ? vehicle.code : '');
            $('#qrCode').html(vehicle ? vehicle.qr_code : '');
            $('#downloadQrCode').attr('data-name', vehicle ? vehicle.code : '');
          } else {
            $('#codeForm').addClass('d-none');
          }

          $('input[name="id"]').val(vehicle ? vehicle.id : '');
          $('input[name="deedOfSalePath"]').val(vehicle ? vehicle.deed_of_sale : '');
          $('input[name="orPath"]').val(vehicle ? vehicle.or : '');
          $('input[name="crPath"]').val(vehicle ? vehicle.cr : '');

          $('select[name="type"]').val(vehicle ? vehicle.type : '');
          $('input[name="plate_number"]').val(vehicle ? vehicle.plate_number : '');
          $('input[name="make"]').val(vehicle ? vehicle.make : '');
          $('input[name="model"]').val(vehicle ? vehicle.model : '');
          $('select[name="year_model"]').val(vehicle ? vehicle.year_model : '');
          $('input[name="color"]').val(vehicle ? vehicle.color : '');
          $('input[name="engine_number"]').val(vehicle ? vehicle.engine_number : '');
          $('input[name="chassis_number"]').val(vehicle ? vehicle.chassis_number : '');
          $('select[name="own_vehicle"]').val(vehicle ? (vehicle.own_vehicle ? 'yes' : 'no') : '');

          if (!vehicle || (vehicle && vehicle.own_vehicle)) {
            $('#deedOfSaleField').addClass('d-none');
          } else {
            $('#deedOfSaleField').removeClass('d-none');
          }

          var orPath = ''
          if (vehicle && vehicle.or) {
            orPath = `/storage/${vehicle.or}`;
          }
          $('#orPreview').attr('src', orPath);

          var crPath = ''
          if (vehicle && vehicle.cr) {
            crPath = `/storage/${vehicle.cr}`;
          }
          $('#crPreview').attr('src', crPath);

          var deedOfSalePath = ''
          if (vehicle && vehicle.deed_of_sale) {
            deedOfSalePath = `/storage/${vehicle.deed_of_sale}`;
          }
          $('#deedOfSalePreview').attr('src', deedOfSalePath);

          var photosStr = '';
          if (vehicle && vehicle.photos && vehicle.photos.length) {
            vehicle.photos.map(p => {
              photosStr += `<img class="preview-images prev-image" src="/storage/${p.image}"/>`;
            });
          }
          $('.photos-preview').html(photosStr);
          initImagePreview();

          if (action == 'add' || action == 'edit') {
            $('#saveVehicle').removeClass('d-none');
            $('#printVehicleReport').addClass('d-none');
            $('select[name="type"]').attr('required', 'required').removeAttr('disabled');
            $('input[name="plate_number"]').attr('required', 'required').removeAttr('disabled');
            $('input[name="make"]').attr('required', 'required').removeAttr('disabled');
            $('input[name="model"]').attr('required', 'required').removeAttr('disabled');
            $('select[name="year_model"]').attr('required', 'required').removeAttr('disabled');
            $('input[name="color"]').attr('required', 'required').removeAttr('disabled');
            $('input[name="engine_number"]').attr('required', 'required').removeAttr('disabled');
            $('input[name="chassis_number"]').attr('required', 'required').removeAttr('disabled');
            $('select[name="own_vehicle"]').attr('required', 'required').removeAttr('disabled');

            $('#orFile').removeClass('d-none');
            $('#crFile').removeClass('d-none');
            $('#deedOfSaleFile').removeClass('d-none');
            $('#photosFile').removeClass('d-none');
            $('#orLabel').addClass('d-none');
            $('#crLabel').addClass('d-none');
            $('#deedOfSaleLabel').addClass('d-none');
            $('#photosLabel').addClass('d-none');
            $('#saveModal').removeClass('d-none');

            if (vehicle) {
              if (vehicle.or) {
                $('#or').removeAttr('required');
              } else {
                $('#or').attr('required', 'required');
              }

              if (vehicle.cr) {
                $('#cr').removeAttr('required');
              } else {
                $('#cr').attr('required', 'required');
              }

              if (vehicle.deed_of_sale) {
                $('#deedOfSale').removeAttr('required');
              } else {
                $('#deedOfSale').attr('required', 'required');
              }

              if (vehicle.photos && vehicle.photos.length) {
                $('#photos').removeAttr('required');
              } else {
                $('#photos').attr('required', 'required');
              }
            }
          } else if (action == 'view') {
            $('#saveVehicle').addClass('d-none');
            $('select[name="type"]').removeAttr('required').attr('disabled', 'disabled');
            $('input[name="plate_number"]').removeAttr('required').attr('disabled', 'disabled');
            $('input[name="make"]').removeAttr('required').attr('disabled', 'disabled');
            $('input[name="model"]').removeAttr('required').attr('disabled', 'disabled');
            $('select[name="year_model"]').removeAttr('required').attr('disabled', 'disabled');
            $('input[name="color"]').removeAttr('required').attr('disabled', 'disabled');
            $('input[name="engine_number"]').removeAttr('required').attr('disabled', 'disabled');
            $('input[name="chassis_number"]').removeAttr('required').attr('disabled', 'disabled');
            $('select[name="own_vehicle"]').removeAttr('required').attr('disabled', 'disabled');
            
            $('#orFile').addClass('d-none');
            $('#crFile').addClass('d-none');
            $('#deedOfSaleFile').addClass('d-none');
            $('#photosFile').addClass('d-none');
            $('#orLabel').removeClass('d-none');
            $('#crLabel').removeClass('d-none');
            $('#deedOfSaleLabel').removeClass('d-none');
            $('#photosLabel').removeClass('d-none');
            $('#saveModal').addClass('d-none');

            $('#printVehicleReport').attr('href', `/report/vehicle/${vehicle.id}`);
          }
        });
      }

      $('.viewUser').click(function() {
        var action = $(this).data('action');
        var type = $(this).data('type');
        var data = $(this).data('json');

        $('input[name="firstname"]').val(data ? data.firstname : '');
        $('input[name="middlename"]').val(data ? data.middlename : '');
        $('input[name="lastname"]').val(data ? data.lastname : '');
        $('input[name="email"]').val(data ? data.email : '');
        $('select[name="rank"]').val(data ? data.rank : '');
        $('input[name="endorser"]').val(data ? data.endorser : '');
        $('textarea[name="address"]').val(data ? data.address : '');
        $('input[name="designation"]').val(data ? data.designation : '');
        $('select[name="office"]').val(data ? (data.other_office ? "others" : data.office) : '');
        $('input[name="otherOffice"]').val(data ? (data.other_office ? data.office : '') : '');
        $('input[name="mobile"]').val(data ? data.mobile : '');
        $('input[name="telephone"]').val(data ? data.telephone : '');
        $('input[name="pnpIdPath"]').val(data ? data.pnp_id_picture : '');
        $('input[name="driverLicensePath"]').val(data ? data.drivers_license : '');
        $('input[name="endorserIdPath"]').val(data ? data.endorser_id : '');
        $('select[name="status"]').val(data ? data.status : '');
        if (type == 'users') {
          $('#idPreview').removeClass("d-none");
          if (data && data.rank == 'CIV') {
            $('#civFields').removeClass('d-none');
          } else {
            $('#civFields').addClass('d-none');
          }
        } else {
          $('#idPreview').addClass("d-none");
          $('#civFields').addClass('d-none');
        }

        if (data && data.pnp_id_picture) {
          $('#pnpIdPreview').attr('src', `/storage/${data.pnp_id_picture}`)
        } else {
          $('#pnpIdPreview').removeAttr('src');
        }

        if (data && data.drivers_license) {
          $('#driverLicensePreview').attr('src', `/storage/${data.drivers_license}`)
        } else {
          $('#driverLicensePreview').removeAttr('src');
        }

        if (data && data.endorser_id) {
          $('#endorserIdPreview').attr('src', `/storage/${data.endorser_id}`)
        } else {
          $('#endorserIdPreview').removeAttr('src');
        }

        if (data && data.other_office) {
            $('#officeFields').removeClass('d-none');
          } else {
            $('#officeFields').addClass('d-none');
          }

        $('#printReport').addClass('d-none');

        if (action == 'edit') {
          $('#userId').val(data.id);
          $('#modalHeader').html("Edit");
          $('input[name="firstname"]').removeAttr('disabled');
          $('input[name="middlename"]').removeAttr('disabled');
          $('input[name="lastname"]').removeAttr('disabled');
          $('input[name="email"]').removeAttr('disabled');
          $('select[name="rank"]').removeAttr('disabled');
          $('input[name="endorser"]').removeAttr('disabled');
          $('textarea[name="address"]').removeAttr('disabled');
          $('input[name="designation"]').removeAttr('disabled');
          $('select[name="office"]').removeAttr('disabled');
          $('input[name="otherOffice"]').removeAttr('disabled');
          $('input[name="mobile"]').removeAttr('disabled');
          $('input[name="telephone"]').removeAttr('disabled');
          $('select[name="status"]').removeAttr('disabled');
          $('#saveModal').removeClass('d-none');
          $('#changePasswordForm').removeClass('d-none');
          $('#passwordForm').removeClass('d-none');
          $('#passwordInputs').addClass('d-none');
          $('#password').removeAttr('required');
          $('#confirmPassword').removeAttr('required');
          $("#changePassword").prop("checked", false);
          $('#statusForm').removeClass("d-none");
          $('#endorserId').removeClass("d-none");
          $('#endorserIdLabel').removeClass("d-none");
          $('#driverLicense').removeClass("d-none");
          $('#driverLicenseLabel').removeClass("d-none");
          $('#pnpId').removeClass("d-none");
          $('#pnpIdLabel').removeClass("d-none");
        } else if (action == 'view') {
          $('#printReport').removeClass('d-none').attr('href', `/report/profile?id=${data.id}`);
          $('#userId').val("");
          $('#modalHeader').html("View");
          $('input[name="firstname"]').attr('disabled', 'disabled');
          $('input[name="middlename"]').attr('disabled', 'disabled');
          $('input[name="lastname"]').attr('disabled', 'disabled');
          $('input[name="email"]').attr('disabled', 'disabled');
          $('select[name="rank"]').attr('disabled', 'disabled');
          $('input[name="endorser"]').attr('disabled', 'disabled');
          $('textarea[name="address"]').attr('disabled', 'disabled');
          $('input[name="designation"]').attr('disabled', 'disabled');
          $('select[name="office"]').attr('disabled', 'disabled');
          $('input[name="otherOffice"]').attr('disabled', 'disabled');
          $('input[name="mobile"]').attr('disabled', 'disabled');
          $('input[name="telephone"]').attr('disabled', 'disabled');
          $('select[name="status"]').attr('disabled', 'disabled');
          $('#saveModal').addClass('d-none');
          $('#passwordForm').addClass('d-none');
          $('#statusForm').removeClass("d-none");
          $('#endorserId').addClass("d-none");
          $('#endorserIdLabel').removeClass("d-none");
          $('#driverLicense').addClass("d-none");
          $('#driverLicenseLabel').removeClass("d-none");
          $('#pnpId').addClass("d-none");
          $('#pnpIdLabel').removeClass("d-none");
        } else if (action == 'add') {
          $('#userId').val("");
          $('#modalHeader').html("Add");
          $('input[name="firstname"]').removeAttr('disabled');
          $('input[name="middlename"]').removeAttr('disabled');
          $('input[name="lastname"]').removeAttr('disabled');
          $('input[name="email"]').removeAttr('disabled');
          $('select[name="rank"]').removeAttr('disabled');
          $('input[name="endorser"]').removeAttr('disabled');
          $('textarea[name="address"]').removeAttr('disabled');
          $('input[name="designation"]').removeAttr('disabled');
          $('select[name="office"]').removeAttr('disabled');
          $('input[name="otherOffice"]').removeAttr('disabled');
          $('input[name="mobile"]').removeAttr('disabled');
          $('input[name="telephone"]').removeAttr('disabled');
          $('#saveModal').removeClass('d-none');
          $('#changePasswordForm').addClass('d-none');
          $('#passwordForm').removeClass('d-none');
          $('#passwordInputs').removeClass('d-none');
          $('#password').removeAttr('required');
          $('#confirmPassword').removeAttr('required');
          $("#changePassword").prop("checked", false);
          $('#statusForm').addClass("d-none");
          $('#endorserId').removeClass("d-none");
          $('#endorserIdLabel').removeClass("d-none");
          $('#driverLicense').removeClass("d-none");
          $('#driverLicenseLabel').removeClass("d-none");
          $('#pnpId').removeClass("d-none");
          $('#pnpIdLabel').removeClass("d-none");
        }
      });

      $('#changePassword').change(function() {
        if (this.checked) {
          $('#passwordInputs').removeClass('d-none');
          $('#password').attr('required', true);
          $('#confirmPassword').attr('required', true);
        } else {
          $('#passwordInputs').addClass('d-none');
          $('#password').removeAttr('required');
          $('#confirmPassword').removeAttr('required');
        }
      });

      $('#userForm').submit(function(e) {
        e.preventDefault();
        hideError();

        var email = $('#email').val();
        if (!validateEmail(email)) {
          return showError("Invalid email format");
        }

        var changePass = $('#changePassword').is(":checked");
        if (changePass) {
          var password = $('#password').val();
          var confirmPassword = $('#passwordConfirm').val();
          if (password != confirmPassword) {
            return showError("Password not match");
          }
        }

        var mobile = $('#mobile').val();
        if (!validateMobile(mobile)) {
          return showError("Use this as mobile number format: 09XXXXXXXXX");
        }

        $(this).unbind('submit').submit();
      });

      $('.file').change(function() {
        const preview = $(this).data('preview');
        const target = $(this).data('target');

        for (var i = 0; i < this.files.length; i++) {
          let file = this.files[i];
          loadImage(file, target, preview);
        }
      });

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

      $('#downloadQrCode').click(function() {
        var name = $(this).data('name');
        saveSvg($('#qrCode'), `${name}.svg`);
      })
    });
  </script>
@endsection