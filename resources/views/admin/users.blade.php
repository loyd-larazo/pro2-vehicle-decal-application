@extends('layout')

@section('content')
  <div class="row p-0 m-0 mb-2">
    <img class="logo-heading col-auto " src="/images/logo.png"/>
    <h1 class="col mt-3">{{ ucfirst($userType) }}</h1>
    @if ($userType != 'users')
      <div class="col-auto">
        <button class="btn btn-primary btn-sm mt-4 viewUser" data-type="{{ $userType }}" data-action="add" data-bs-toggle="modal" data-bs-target="#viewUserModal">
          <i class="fa-solid fa-plus"></i> {{ rtrim(ucfirst($userType), "s") }}
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
                    <button class="btn btn-sm btn-primary viewUser" data-type="{{ $userType }}" data-action="view" data-id="{{ $user->id }}" data-json="{{ json_encode($user) }}" data-bs-toggle="modal" data-bs-target="#viewUserModal">View</button>
                    @if ($userType != "users")
                      <button class="btn btn-sm btn-warning viewUser" data-type="{{ $userType }}" data-action="edit" data-id="{{ $user->id }}" data-json="{{ json_encode($user) }}" data-bs-toggle="modal" data-bs-target="#viewUserModal">Edit</button>
                    @endif
                  </td>
                </tr>
              @endforeach
            @else
              <tr>
                <th colspan="4" class="text-center">No {{ $userType }} found</th>
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
                      <a href="/reports/app/{{$userType}}?search={{$search}}&from={{$from}}&to={{$to}}" target="_blank" class="btn btn-info mt-2"> <i class="fa-solid fa-print me-2"></i>Print List</a>
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

  <div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form id="userForm" class="modal-content" action="/app/{{ $userType }}" method="POST">
        <div class="modal-header">
          <h5 class="modal-title" id="viewUserModalLabel"><span id="modalHeader">View</span> {{ rtrim(ucfirst($userType), "s") }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="_token" value="{{ csrf_token() }}" />
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
            <label>PNP ID:</label>
            <div class="form-floating mb-3" id="pnpIdUpload">
              <input class="form-control file" id="pnpId" type="file" name="pnp_id" accept="image/*" placeholder="Upload your PNP ID" />
              <label for="pnpId">PNP ID Picture</label>
            </div>
            <div class="form-floating mb-3 text-center">
              <img id="imgPreview" />
            </div>
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
          $(this).val(from + ' - ' + to);
          location.href = `/app/{{$userType}}?from=${from}&to=${to}`;
        });

        $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            location.href = `/app/{{$userType}}`;
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
        $('textarea[name="address"]').val(data ? data.address : '');
        $('input[name="designation"]').val(data ? data.designation : '');
        $('input[name="office"]').val(data ? data.office : '');
        $('input[name="mobile"]').val(data ? data.mobile : '');
        $('input[name="telephone"]').val(data ? data.telephone : '');
        if (data && data.pnp_id_picture) {
          $('#idPreview').removeClass("d-none");
          $('#imgPreview').attr('src', `/storage/${data.pnp_id_picture}`)
        } else {
          $('#idPreview').addClass("d-none");
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
          $('textarea[name="address"]').removeAttr('disabled');
          $('input[name="designation"]').removeAttr('disabled');
          $('input[name="office"]').removeAttr('disabled');
          $('input[name="mobile"]').removeAttr('disabled');
          $('input[name="telephone"]').removeAttr('disabled');
          $('#pnpIdUpload').removeClass('d-none');
          $('#saveModal').removeClass('d-none');
          $('#changePasswordForm').removeClass('d-none');
          $('#passwordForm').removeClass('d-none');
          $('#passwordInputs').addClass('d-none');
          $('#password').removeAttr('required');
          $('#confirmPassword').removeAttr('required');
          $("#changePassword").prop("checked", false);
        } else if (action == 'view') {
          $('#printReport').removeClass('d-none').attr('href', `/report/profile?id=${data.id}`);
          $('#userId').val("");
          $('#modalHeader').html("View");
          $('input[name="firstname"]').attr('disabled', 'disabled');
          $('input[name="middlename"]').attr('disabled', 'disabled');
          $('input[name="lastname"]').attr('disabled', 'disabled');
          $('input[name="email"]').attr('disabled', 'disabled');
          $('select[name="rank"]').attr('disabled', 'disabled');
          $('textarea[name="address"]').attr('disabled', 'disabled');
          $('input[name="designation"]').attr('disabled', 'disabled');
          $('input[name="office"]').attr('disabled', 'disabled');
          $('input[name="mobile"]').attr('disabled', 'disabled');
          $('input[name="telephone"]').attr('disabled', 'disabled');
          $('#pnpIdUpload').addClass('d-none');
          $('#saveModal').addClass('d-none');
          $('#passwordForm').addClass('d-none');
        } else if (action == 'add') {
          $('#userId').val("");
          $('#modalHeader').html("Add");
          $('input[name="firstname"]').removeAttr('disabled');
          $('input[name="middlename"]').removeAttr('disabled');
          $('input[name="lastname"]').removeAttr('disabled');
          $('input[name="email"]').removeAttr('disabled');
          $('select[name="rank"]').removeAttr('disabled');
          $('textarea[name="address"]').removeAttr('disabled');
          $('input[name="designation"]').removeAttr('disabled');
          $('input[name="office"]').removeAttr('disabled');
          $('input[name="mobile"]').removeAttr('disabled');
          $('input[name="telephone"]').removeAttr('disabled');
          $('#pnpIdUpload').removeClass('d-none');
          $('#saveModal').removeClass('d-none');
          $('#changePasswordForm').addClass('d-none');
          $('#passwordForm').removeClass('d-none');
          $('#passwordInputs').removeClass('d-none');
          $('#password').removeAttr('required');
          $('#confirmPassword').removeAttr('required');
          $("#changePassword").prop("checked", false);
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
    });
  </script>
@endsection