@extends('layout')

@section('content')
  <div class="row p-0 m-0 mb-2">
    <img class="logo-heading col-auto " src="/images/logo.png"/>
    <h1 class="col mt-3">Profile Settings</h1>
  </div>
              
  <div class="card mb-4">
    <div class="card-body">
      
      <form id="profileForm" action="/profile" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        @if(isset($user))
          <input type="hidden" name="id" value="{{ $user->id }}" />
          <input type="hidden" name="pnpIdPath" value="{{ $user->pnp_id_picture }}" />
          <input type="hidden" name="endorserIdPath" value="{{ $user->endorser_id }}" />
          <input type="hidden" name="driversLicensePath" value="{{ $user->drivers_license }}" />
        @endif

        <div id="jsError" class="alert alert-danger text-center d-none" role="alert"></div>

        @if(\Session::get('error'))
          <div class="alert alert-danger text-center" role="alert">
            {{ \Session::get('error') }}
          </div>
        @endif
      
        @if(\Session::get('success'))
          <div class="alert alert-success text-center" role="alert">
            {{ \Session::get('success') }}
          </div>
        @endif

        <div class="row mb-3">
          <div class="col-md-6">
            <div class="form-floating mb-3 mb-md-0">
              <input required class="form-control" id="firstname" type="text" name="firstname" placeholder="Enter your first name" value="{{ isset($user) ? $user->firstname : '' }}"/>
              <label for="firstname">First name</label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-floating">
              <input class="form-control" id="middlename" type="text" name="middlename" placeholder="Enter your middle name" value="{{ isset($user) ? $user->middlename : '' }}"/>
              <label for="middlename">Middle name</label>
            </div>
          </div>
        </div>
        <div class="form-floating mb-3">
          <input required class="form-control" id="lastname" type="text" name="lastname" placeholder="Enter your last name" value="{{ isset($user) ? $user->lastname : '' }}"/>
          <label for="lastname">Last name</label>
        </div>
        <div class="form-floating mb-3">
          <input required class="form-control" id="email" type="email" name="email" placeholder="name@example.com" value="{{ isset($user) ? $user->email : '' }}"/>
          <label for="email">Email address</label>
        </div>
        <div class="row m-0 mb-3" id="passwordForm">
          <div class="col-12 form-check" id="changePasswordForm">
            <input type="checkbox" class="form-check-input" id="changePassword" name="change_password">
            <label class="form-check-label" for="changePassword">Change Password</label>
          </div>
          <div id="passwordInputs" class="d-none">
            <div class="row">
              <div class="col-md-6 ps-0">
                <div class="form-floating mb-3 mb-md-0">
                  <input class="form-control" id="password" type="password" name="password" placeholder="Create a password" />
                  <label for="password">Password</label>
                </div>
              </div>
              <div class="col-md-6 pe-0">
                <div class="form-floating mb-3 mb-md-0">
                  <input class="form-control" id="passwordConfirm" type="password" name="confirmPassword" placeholder="Confirm password" />
                  <label for="passwordConfirm">Confirm Password</label>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="form-floating mb-3">
          <select class="form-control" id="rank" name="rank" required>
            <option value="">Select your Rank</option>
            <option {{ isset($user) && $user->rank == 'PGEN' ? 'selected' : '' }} value="PGEN">PGEN</option>
            <option {{ isset($user) && $user->rank == 'PLTGEN' ? 'selected' : '' }} value="PLTGEN">PLTGEN</option>
            <option {{ isset($user) && $user->rank == 'PMGEN' ? 'selected' : '' }} value="PMGEN">PMGEN</option>
            <option {{ isset($user) && $user->rank == 'PBGEN' ? 'selected' : '' }} value="PBGEN">PBGEN</option>
            <option {{ isset($user) && $user->rank == 'PCOL' ? 'selected' : '' }} value="PCOL">PCOL</option>
            <option {{ isset($user) && $user->rank == 'PLTCOL' ? 'selected' : '' }} value="PLTCOL">PLTCOL</option>
            <option {{ isset($user) && $user->rank == 'PMAJ' ? 'selected' : '' }} value="PMAJ">PMAJ</option>
            <option {{ isset($user) && $user->rank == 'PCPT' ? 'selected' : '' }} value="PCPT">PCPT</option>
            <option {{ isset($user) && $user->rank == 'PLT' ? 'selected' : '' }} value="PLT">PLT</option>
            <option {{ isset($user) && $user->rank == 'PEMS' ? 'selected' : '' }} value="PEMS">PEMS</option>
            <option {{ isset($user) && $user->rank == 'PCMS' ? 'selected' : '' }} value="PCMS">PCMS</option>
            <option {{ isset($user) && $user->rank == 'PSMS' ? 'selected' : '' }} value="PSMS">PSMS</option>
            <option {{ isset($user) && $user->rank == 'PMSg' ? 'selected' : '' }} value="PMSg">PMSg</option>
            <option {{ isset($user) && $user->rank == 'PSSg' ? 'selected' : '' }} value="PSSg">PSSg</option>
            <option {{ isset($user) && $user->rank == 'PCpl' ? 'selected' : '' }} value="PCpl">PCpl</option>
            <option {{ isset($user) && $user->rank == 'Patrolman' ? 'selected' : '' }} value="Patrolman">Patrolman</option>
            <option {{ isset($user) && $user->rank == 'NUP' ? 'selected' : '' }} value="NUP">NUP</option>
            <option {{ isset($user) && $user->rank == 'CIV' ? 'selected' : '' }} value="CIV">CIV</option>
          </select>
          <label for="rank">Rank</label>
        </div>
        <div id="civFields" class="{{(isset($user) && $user->rank == 'CIV') ? '' : 'd-none'}}">
          <div class="form-floating mb-3">
            <input class="form-control" id="endorser" type="text" name="endorser" placeholder="Enter your Name of Endorser" value="{{ isset($user) ? $user->endorser : '' }}"/>
            <label for="endorser">Name of Endorser</label>
          </div>

          <div>
            <div class="form-floating mb-3">
              <input  {{ isset($user) ? '' : 'required' }} class="form-control file" id="endorserId" data-target="src" data-preview="#endorserIdPreview" type="file" name="endorser_id" accept="image/*" placeholder="Upload your Endorser ID" />
              <label for="endorserId">Endorser ID</label>
            </div>
            <div class="form-floating mb-3 text-center">
              <img id="endorserIdPreview" class="preview-images prev-image" src="{{ isset($user) ? '/storage/'.$user->endorser_id : '' }}"/>
            </div>
          </div>

          <div>
            <div class="form-floating mb-3">
              <input  {{ isset($user) ? '' : 'required' }} class="form-control file" id="driverLicense" data-target="src" data-preview="#driverLicensePreview" type="file" name="drivers_license" accept="image/*" placeholder="Upload your Drivers License" />
              <label for="driverLicense">Driver's License ID</label>
            </div>
            <div class="form-floating mb-3 text-center">
              <img id="driverLicensePreview" class="preview-images prev-image" src="{{ isset($user) ? '/storage/'.$user->drivers_license : '' }}"/>
            </div>
          </div>
        </div>
        <div class="form-floating mb-3">
          <textarea required class="form-control" id="address" name="address">{{ isset($user) ? $user->address : '' }}</textarea>
          <label for="address">Address</label>
        </div>
        <div class="form-floating mb-3">
          <input required class="form-control" id="designation" type="text" name="designation" placeholder="Enter your Designation/Position" value="{{ isset($user) ? $user->designation : '' }}"/>
          <label for="designation">Designation/Position</label>
        </div>
        <div class="form-floating mb-3">
          <input required class="form-control" id="office" type="text" name="office" placeholder="Enter your Office/Unit Assignment" value="{{ isset($user) ? $user->office : '' }}" />
          <label for="office">Office/Unit Assignment</label>
        </div>
        <div class="row mb-3">
          <div class="col-md-6">
            <div class="form-floating mb-3">
              <input 
                required 
                class="form-control mobile-number" 
                id="mobile" 
                type="text" 
                name="mobile" 
                placeholder="Enter your Mobile Number" 
                value="{{ isset($user) ? $user->mobile : '' }}" />
              <label for="mobile">Mobile Number</label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-floating mb-3">
              <input 
                class="form-control" 
                id="telephone" 
                type="text" 
                name="telephone" 
                placeholder="Enter your Telephone Number" 
                onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))"
                value="{{ isset($user) ? $user->telephone : '' }}" />
              <label for="telephone">Telephone Number</label>
            </div>
          </div>
        </div>

        @if ($user->type == 'user')
          <div class="form-floating mb-3">
            <input {{ isset($user) ? '' : 'required' }} class="form-control file" id="pnpId" type="file" name="pnp_id" accept="image/*" placeholder="Upload your PNP ID" />
            <label for="pnpId">PNP ID Picture</label>
          </div>
          <div class="form-floating mb-3 text-center">
            <img id="imgPreview" class="prev-image" src="{{ isset($user) ? '/storage/'.$user->pnp_id_picture : '' }}" />
          </div>
        @endif

        <div class="mt-4 mb-0">
          <div class="d-grid"><button type="submit" class="btn btn-primary btn-block">Save Profile</button></div>
          <div class="d-grid mt-2"><a href="/report/profile" target="_blank" class="btn btn-info btn-block"> <i class="fa-solid fa-print me-2"></i>Print Report</a></div>
        </div>
      </form>

    </div>
  </div>

  <script>
    $(function() {
      secureMobile();
      rankChange();

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

      const user = @json($user);
      
      $("#changePassword").prop("checked", false);

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

      $('.file').change(function() {
        const preview = $(this).data('preview');
        const target = $(this).data('target');

        for (var i = 0; i < this.files.length; i++) {
          let file = this.files[i];
          loadImage(file, target, preview);
        }
      });

      $('#profileForm').submit(function(e) {
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

        if ($('#rank').val() != user.rank && ($('#rank').val() == 'CIV')) {
          if (!$('#endorser').val() || !$('#endorserId').val() || !$('#driverLicense').val()) {
            return showError("Please enter all required fields.");
          }
        }

        $(this).unbind('submit').submit();
      });

      $('#pnpId').change(function() {
        const file = this.files[0];
        if (file) {
          let reader = new FileReader();
          reader.onload = function(event){
            $('#imgPreview').attr('src', event.target.result);
          }
          reader.readAsDataURL(file);
        }
      });
    });
  </script>
@endsection