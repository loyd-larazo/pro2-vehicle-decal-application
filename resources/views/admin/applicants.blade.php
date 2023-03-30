@extends('layout')

@section('content')
  <div class="row p-0 m-0 mb-2">
    <img class="logo-heading col-auto " src="/images/logo.png"/>
    <h1 class="col mt-3">Applicant Approval</h1>
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

      <form class="row mb-2" action="/applicants" method="GET">
        <div class="col-auto mt-2">
          <div class="input-group">
            <select class="form-select" onchange="this.form.submit()" id="status" name="status">
              <option value="all" {{ $status && $status == 'all' ? 'selected' : '' }}>All</option>
              <option value="pending" {{ $status && $status == 'pending' ? 'selected' : '' }}>Pending</option>
              <option value="approved" {{ $status && $status == 'approved' ? 'selected' : '' }}>Approved</option>
              <option value="rejected" {{ $status && $status == 'rejected' ? 'selected' : '' }}>Rejected</option>
              <option value="request_change" {{ $status && $status == 'request_change' ? 'selected' : '' }}>Change Request</option>
            </select>
            <div class="form-outline pt-1 ms-2">
              Applicants
            </div>
          </div>
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
              @if (!$status || ($status && $status == 'all'))
                <th scope="col">Status</th>
              @endif
              <th scope="col"></th>
            </tr>
          </thead>
          <tbody>
            @if ($applicants && count($applicants))
              @foreach ($applicants as $applicant)
                <tr>
                  <td>{{ $applicant->email }}</td>
                  <td>{{ $applicant->firstname . " " . $applicant->middlename . " " . $applicant->lastname }}</td>
                  <td>{{ $applicant->rank }}</td>
                  <td>{{ $applicant->mobile }}</td>
                  @if (!$status || ($status && $status == 'all'))
                    <td class="text-uppercase">{{ str_replace("_", " ", $applicant->status) }}</td>
                  @endif
                  <td class="text-center">
                    <button class="btn btn-sm btn-primary viewApplicant" data-id="{{ $applicant->id }}" data-json="{{ json_encode($applicant) }}" data-bs-toggle="modal" data-bs-target="#viewApplicantModal">View</button>
                    @if (Session::get('userType') && in_array(Session::get('userType'), ["admin"]))
                      @if ($applicant->status == "pending")
                        <button class="btn btn-sm btn-success verify" data-id="{{ $applicant->id }}" data-type="approve" data-bs-toggle="modal" data-bs-target="#verifyModal">Approve</button>
                        <button class="btn btn-sm btn-danger verify" data-id="{{ $applicant->id }}" data-type="reject" data-bs-toggle="modal" data-bs-target="#verifyModal">Reject</button>
                      @endif
                    @endif
                  </td>
                </tr>
              @endforeach
            @else
              <tr>
                <th colspan="6" class="text-center">No applicant found</th>
              </tr>
            @endif
          </tbody>
          
          @if ($applicants && count($applicants))
            <tfoot>
              <tr>
                <th colspan="6" class="text-center">
                  <div class="row m-0 p-0">
                    <div class="col p-0 row g-3 align-items-center m-auto">
                      <div class="col-auto">
                        <label class="col-form-label">Page</label>
                      </div>
                      <div class="col-auto">
                        <select class="form-select page-select">
                          @for($i = 1; $i <= $applicants->lastPage(); $i++)
                            <option value="{{ $i }}" {{ $applicants->currentPage() == $i ? 'selected' : '' }}>{{ $i }}</option>
                          @endfor
                        </select>
                      </div>
                      <div class="col-auto">
                        <label class="col-form-label">of {{ $applicants->lastPage() }}</label>
                      </div>
                    </div>
                    <div class="col p-0 text-end">
                      <a href="/report/applicants/{{$status}}?search={{$search}}&from={{$from}}&to={{$to}}" target="_blank" class="btn btn-info mt-2"> <i class="fa-solid fa-print me-2"></i>Print List</a>
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

  <div class="modal fade" id="verifyModal" tabindex="-1" aria-labelledby="verifyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="verifyModalLabel"><span class="modalType"></span> Applicant</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to <span class="modalType"></span> this applicant?
          <div id="changeForm" class="d-none">
            <div class="mt-3mb-3 form-check">
              <input type="checkbox" class="form-check-input" id="requestChange">
              <label class="form-check-label" for="requestChange">Request for changes</label>
            </div>
            <div id="changeRequestForm" class="d-none">
              <label>Remarks: </label>
              <textarea class="form-control" id="changeRequest"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="verifyBtn" class="btn btn-success">Yes</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="viewApplicantModal" tabindex="-1" aria-labelledby="viewApplicantModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewApplicantModalLabel">View Applicant</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <ul class="nav nav-tabs nav-justified mb-3">
            <li class="nav-item applicant-switch" data-tab="applicant">
              <a class="nav-link active" aria-current="page">Applicant</a>
            </li>
            <li class="nav-item applicant-switch" data-tab="vehicle">
              <a class="nav-link">Vehicle</a>
            </li>
          </ul>
          <div id="applicantForm">
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
                <option value="Patrolman">Patrolman</option>
                <option value="NUP">NUP</option>
                <option value="CIV">CIV</option>
              </select>
              <label for="rank">Rank</label>
            </div>
            <div id="civFields">
              <div class="form-floating mb-3">
                <input disabled class="form-control" id="endorser" type="text" name="endorser" placeholder="Endorser" />
                <label for="endorser">Endorser</label>
              </div>
              <div id="idPreview">
                <label>Endorser ID:</label>
                <div class="form-floating mb-3 text-center">
                  <img id="endorserIdPreview" class="prev-image img-preview" />
                </div>
              </div>
              <div id="idPreview">
                <label>Driver's License:</label>
                <div class="form-floating mb-3 text-center">
                  <img id="driversLicensePreview" class="prev-image img-preview" />
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
              <input disabled class="form-control" id="office" type="text" name="office" placeholder="Enter your Office/Unit Assignment" />
              <label for="office">Office/Unit Assignment</label>
            </div>
            <div class="row mb-3">
              <div class="col-md-6">
                <div class="form-floating mb-3">
                  <input 
                    disabled 
                    class="form-control" 
                    id="mobile" 
                    type="text" 
                    name="mobile" 
                    onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))"
                    oninput="if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
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
              <label>PNP ID:</label>
              <div class="form-floating mb-3 text-center">
                <img id="imgPreview" class="prev-image" />
              </div>
            </div>
          </div>

          <div id="vehicleForm" class="d-none">
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
              <input class="form-control" id="make" type="text" name="make" placeholder="Enter Make" disabled />
              <label for="make">Make</label>
            </div>
  
            <div class="row mb-3">
              <div class="col-md-6">
                <div class="form-floating mb-3 mb-md-0">
                  <input class="form-control" id="model" type="text" name="model" placeholder="Enter Series" disabled />
                  <label for="model">Series</label>
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

            <div class="form-floating mb-3">
              <select class="form-control" id="ownVehicle" name="own_vehicle" disabled>
                <option value="">Select from options</option>
                <option value="yes">Yes</option>
                <option value="no">No</option>
              </select>
              <label for="ownVehicle">Do you own the vehicle?</label>
            </div>

            <div id="deedOfSaleField" class="d-none">
              <label>Deed of Sale:</label>
              <div class="form-floating mb-3 text-center">
                <img id="deedOfSalePreview" class="prev-image img-preview" />
              </div>
            </div>

            <div>
              <label>OR:</label>
              <div class="form-floating mb-3 text-center">
                <img id="orPreview" class="prev-image img-preview" />
              </div>
            </div>

            <div>
              <label>CR:</label>
              <div class="form-floating mb-3 text-center">
                <img id="crPreview" class="prev-image img-preview" />
              </div>
            </div>
            
            <div>
              <label id="photosLabel">Photos of Vehicle</label>
              <div class="form-floating mb-3 text-center photos-preview">
              </div>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <a id="printReport" target="_blank" class="btn btn-info mt-2"> <i class="fa-solid fa-print me-2"></i>Print</a>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    $(function() {
      var toSendEmail = JSON.parse(@json($toSendEmail));
      var baseUrl = "{{ URL::to('/') }}";

      if (toSendEmail) {
        var content_heading = "";
        var content_footer = "";
        var link = "";
        var remarks = "";
        if (toSendEmail.status == 'approved') {
          content_heading = "Your Application has been Approved!";
          content_footer = "Login";
          link = `${baseUrl}/login`;
        } else if (toSendEmail.status == 'rejected') {
          content_heading = "Your Application has been Rejected!";
          content_footer = "send a new Application";
          link = `${baseUrl}/application`;
        } else if (toSendEmail.status == 'request_change') {
          content_heading = "Your Application needs to be updated!";
          content_footer = "update your Application";
          link = `${baseUrl}/applicant/${toSendEmail.id}`;
          remarks = toSendEmail.remarks;
        }
        
        sendEmailVerify({
          to_email: toSendEmail.email,
          logo: `${baseUrl}/images/logo.png`,
          content_heading,
          content_footer,
          link,
          remarks
        });
      }

      initDatePicker();
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
          var status = $('#status').val();
          $(this).val(from + ' - ' + to);
          location.href = `/applicants?from=${from}&to=${to}&status=${status}`;
        });

        $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            var status = $('#status').val();
            location.href = `/applicants?status=${status}`;
        });
      }

      $('.page-select').change(function() {
        var search = $('#search').val();
        var status = $('#status').val();
        var status = $('#daterange').val();
        var page = $(this).val();
        location.href = `/applicants?search=${search}&page=${page}&status=${status}`;
      });

      $('.verify').click(function() {
        var id = $(this).data('id');
        var type = $(this).data('type');

        $('.modalType').html(capitalize(type));

        if (type == 'approve') {
          $('#changeForm').addClass('d-none');
          $('#verifyBtn').removeClass('btn-danger').addClass('btn-success');
        }

        if (type == 'reject') {
          $('#changeForm').removeClass('d-none');
          $('#verifyBtn').removeClass('btn-success').addClass('btn-danger');
          $('#changeRequest').val("");
        }

        $('#verifyBtn').attr('data-id', id).attr('data-type', type);
      });

      $('#verifyBtn').click(function() {
        var id = $(this).data('id');
        var type = $(this).data('type');
        var requestChange = $('#requestChange').is(":checked") ? true : false;
        var remarks = $('#changeRequest').val();
        type = type == 'approve' ? 'approved' : (requestChange ? 'request_change' : 'rejected');

        var requestRemarks = type == 'request_change' ? `?remarks=${remarks}` : '';

        location.href = `/applicant/${id}/${type}${requestRemarks}`;
      });

      $('#requestChange').change(function() {
        if (this.checked) {
          $('#changeRequestForm').removeClass('d-none');
        } else {
          $('#changeRequestForm').addClass('d-none');
        }
      });

      $('.viewApplicant').click(function() {
        var data = $(this).data('json');
        $('#civFields').addClass('d-none');
        $('#deedOfSaleField').addClass('d-none');

        $('#printReport').attr('href', `/report/applicant/${data.id}`);
        $('input[name="firstname"]').val(data.firstname);
        $('input[name="middlename"]').val(data.middlename);
        $('input[name="lastname"]').val(data.lastname);
        $('input[name="email"]').val(data.email);
        $('select[name="rank"]').val(data.rank);
        $('textarea[name="address"]').val(data.address);
        $('input[name="designation"]').val(data.designation);
        $('input[name="office"]').val(data.office);
        $('input[name="mobile"]').val(data.mobile);
        $('input[name="telephone"]').val(data.telephone);
        $('#imgPreview').attr('src', `/storage/${data.pnp_id_picture}`);
        if (data.rank == 'CIV') {
          $('#civFields').removeClass('d-none');
          $('input[name="endorser"]').val(data.endorser);
          $('#endorserIdPreview').attr('src', `/storage/${data.endorser_id}`);
          $('#driversLicensePreview').attr('src', `/storage/${data.drivers_license}`);
        }
        console.log(data.vehicle);
        $('select[name="type"]').val(data.vehicle.type);
        $('input[name="plate_number"]').val(data.vehicle.plate_number);
        $('input[name="make"]').val(data.vehicle.make);
        $('input[name="model"]').val(data.vehicle.model);
        $('select[name="year_model"]').val(data.vehicle.year_model);
        $('input[name="color"]').val(data.vehicle.color);
        $('input[name="engine_number"]').val(data.vehicle.engine_number);
        $('input[name="chassis_number"]').val(data.vehicle.chassis_number);
        $('select[name="own_vehicle"]').val(data.vehicle.own_vehicle ? 'yes' : 'no');
        if (data.vehicle.own_vehicle == 0) {
          $('#deedOfSaleField').removeClass('d-none');
          $('#deedOfSalePreview').attr('src', `/storage/${data.vehicle.deed_of_sale}`);
        }

        $('#orPreview').attr('src', `/storage/${data.vehicle.or}`);
        $('#crPreview').attr('src', `/storage/${data.vehicle.cr}`);

        var photosStr = '';
        if (data && data.vehicle.photos && data.vehicle.photos.length) {
          data.vehicle.photos.map(p => {
            photosStr += `<img class="preview-images prev-image" src="/storage/${p.image}"/>`;
          });
        }
        $('.photos-preview').html(photosStr);
        initImagePreview();
      });

      $('.applicant-switch').click(function() {
        let activeTab = $(this).data('tab');
        $('.applicant-switch .nav-link').removeClass('active');
        $(this).children('.nav-link').addClass('active');

        if (activeTab == 'applicant') {
          $('#applicantForm').removeClass('d-none');
          $('#vehicleForm').addClass('d-none');
        } else if (activeTab == 'vehicle') {
          $('#applicantForm').addClass('d-none');
          $('#vehicleForm').removeClass('d-none');
        }

        window.scrollTo(0, 0);
      });
    });
  </script>
@endsection