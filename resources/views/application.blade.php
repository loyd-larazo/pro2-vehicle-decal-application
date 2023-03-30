<!DOCTYPE html>
<html lang="en">
  <head>
      <meta charset="utf-8" />
      <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
      <meta name="description" content="" />
      <meta name="author" content="" />
      <title>PRO2 Vehicle Decal Application System</title>
      
      <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
      <link href="/fontawesome/css/all.min.css" rel="stylesheet">
      <link href="/css/app.css" rel="stylesheet">

      <script src="/js/jquery-3.6.1.min.js"></script>
      <script src="/bootstrap/js/bootstrap.min.js"></script>
      <script src="/js/scripts.js"></script>

      <script>
        $(function() {
          let applicant = @json($applicant ?? null);

          secureMobile();

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

          $('#applicationForm').submit(function(e) {
            e.preventDefault();
            hideError();

            if ($('#ownVehicle').val() == 'no' && (!applicant && !$('#deedOfSale').val())) {
              return showError("Please enter all required fields!");
            }

            var plateNum = $('#plateNumber').val();
            if (!applicant || (applicant && applicant.vehicle.plate_number != plateNum)) {
              $.get(`/vehicle/user/plate/${plateNum}`, (data, status) => {
                if (data.data) {
                  showError("Plate number already exists.");
                } else {
                  $(this).unbind('submit').submit();
                }
              });
            } else {
              $(this).unbind('submit').submit();
            }
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

          $('.file').change(function() {
            const preview = $(this).data('preview');
            const target = $(this).data('target');

            for (var i = 0; i < this.files.length; i++) {
              let file = this.files[i];
              loadImage(file, target, preview);
            }
          });

          $(document).on('keydown', '.password', function(e) {
            if (e.keyCode == 32) return false;
          });

          $('#agree').change(function() {
            if (this.checked) {
              $('#submitForm').removeAttr('disabled');
            } else {
              $('#submitForm').attr('disabled', 'disabled');
            }
          });

          rankChange();
          officeChange();
          ownVehicleChange();

          $('#backToStep1').click(function() {
            $('#applicantInfo').removeClass('d-none');
            $('#vehicleInfo').addClass('d-none');
          });

          $('#nextInfo').click(function() {
            hideError();
            if ( $('#inputFirstName').val() == '' || $('#inputLastName').val() == '' ||  $('#inputEmail').val() == '' ||  $('#confirmInputEmail').val() == '' || 
              $('#inputPassword').val() == '' || $('#rank').val() == '' || $('#address').val() == '' || $('#designation').val() == '' || $('#office').val() == '' || 
              $('#mobile').val() == '' || (!applicant && $('#pnpId').val() == '')) {
              return showError("Please enter all required fields!");
            } else {

              if ($('#rank').val() == 'CIV' && (!applicant && ($('#endorser').val() == '' || $('#endorserId').val() == '' || $('#driverLicense').val() == ''))) {
                return showError("Please enter all required fields!");
              }

              if ($('#office').val() == 'others' && ($('#otherOffice').val() == '')) {
                return showError("Please enter all required fields!");
              }

              var email = $('#inputEmail').val();
              var confirmEmail = $('#confirmInputEmail').val();
              if (!validateEmail(email) || !validateEmail(confirmEmail)) {
                return showError("Invalid email format");
              }

              if (email != confirmEmail) {
                return showError("Email not match");
              }

              var password = $('#inputPassword').val();
              var confirmPassword = $('#inputPasswordConfirm').val();
              if (password != confirmPassword) {
                return showError("Password not match");
              }

              var mobile = $('#mobile').val();
              if (!validateMobile(mobile)) {
                return showError("Use this as mobile number format: 09XXXXXXXXX");
              }

              $.get(`/user/email/${email}`, (data, status) => {
                if (data.data) {
                  showError("Email already exists.");
                } else {
                  // next step
                $('#applicantInfo').addClass('d-none');
                $('#vehicleInfo').removeClass('d-none');
                window.scrollTo(0, 0);
                }
              });
            }
          });
        });
      </script>
  </head>
  <body class="bg-primary">
    <div id="layoutAuthentication">
      <div id="layoutAuthentication_content">
        <main>
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-lg-7">
                <div class="card shadow-lg border-0 rounded-lg mt-5 mb-5">
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
                  
                  <div class="card-header">
                    <img class="logo position-absolute" src="/images/logo.png"/>
                    <h3 class="text-center font-weight-light my-4">Application Form</h3>
                  </div>
                  <div class="card-body">
                    @if(isset($error))
                      <div class="alert alert-danger text-center" role="alert">
                        {{ $error }}
                      </div>
                    @endif

                    @if(isset($applicant))
                      <div class="alert alert-info text-center" role="alert">
                        {{ $applicant->remarks }}
                      </div>
                    @endif
                    <div id="jsError" class="alert alert-danger text-center d-none" role="alert"></div>

                    <form id="applicationForm" action="/application" method="POST" enctype="multipart/form-data">
                      <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                      @if(isset($applicant))
                        <input type="hidden" name="id" value="{{ $applicant->id }}" />
                        <input type="hidden" name="pnpIdPath" value="{{ $applicant->pnp_id_picture }}" />
                        <input type="hidden" name="deedOfSalePath" value="{{ $applicant->vehicle->deed_of_sale }}" />
                        <input type="hidden" name="driverLicensePath" value="{{ $applicant->drivers_license }}" />
                        <input type="hidden" name="endorserIdPath" value="{{ $applicant->endorser_id }}" />
                        <input type="hidden" name="orPath" value="{{ $applicant->vehicle->or }}" />
                        <input type="hidden" name="crPath" value="{{ $applicant->vehicle->cr }}" />
                      @endif

                      <div id="applicantInfo">
                        <div class="row mb-3">
                          <h3>Step 1: Applicant Information</h3>
                        </div>

                        <div class="row mb-3">
                          <div class="col-md-6">
                            <div class="form-floating mb-3 mb-md-0">
                              <input required class="form-control" id="inputFirstName" type="text" name="firstname" placeholder="Enter your first name" value="{{ isset($applicant) ? $applicant->firstname : '' }}"/>
                              <label for="inputFirstName"><span class="text-danger">*</span>First name</label>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-floating">
                              <input class="form-control" id="inputMiddleName" type="text" name="middlename" placeholder="Enter your middle name" value="{{ isset($applicant) ? $applicant->middlename : '' }}"/>
                              <label for="inputMiddleName">Middle name</label>
                            </div>
                          </div>
                        </div>
                        <div class="form-floating mb-3">
                          <input required class="form-control" id="inputLastName" type="text" name="lastname" placeholder="Enter your last name" value="{{ isset($applicant) ? $applicant->lastname : '' }}"/>
                          <label for="inputLastName"><span class="text-danger">*</span>Last name</label>
                        </div>
                        <div class="row mb-3">
                          <div class="col-md-6">
                            <div class="form-floating mb-3 mb-md-0">
                              <input required class="form-control" id="inputEmail" type="email" name="email" placeholder="name@example.com" value="{{ isset($applicant) ? $applicant->email : '' }}"/>
                              <label for="inputEmail"><span class="text-danger">*</span>Email address</label>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-floating mb-3 mb-md-0">
                              <input required class="form-control" id="confirmInputEmail" type="email" name="confirm-email" placeholder="name@example.com" value="{{ isset($applicant) ? $applicant->email : '' }}"/>
                              <label for="confirmInputEmail"><span class="text-danger">*</span>Confirm Email address</label>
                            </div>
                          </div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-md-6">
                            <div class="form-floating mb-3 mb-md-0">
                              <input required class="form-control password" id="inputPassword" type="password" name="password" placeholder="Create a password" />
                              <label for="inputPassword"><span class="text-danger">*</span>Password</label>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-floating mb-3 mb-md-0">
                              <input required class="form-control password" id="inputPasswordConfirm" type="password" name="confirmPassword" placeholder="Confirm password" />
                              <label for="inputPasswordConfirm"><span class="text-danger">*</span>Confirm Password</label>
                            </div>
                          </div>
                        </div>
                        <div class="form-floating mb-3">
                          <select class="form-control" id="rank" name="rank" required>
                            <option value="">Select your Rank</option>
                            <option {{ isset($applicant) && $applicant->rank == 'PGEN' ? 'selected' : '' }} value="PGEN">PGEN</option>
                            <option {{ isset($applicant) && $applicant->rank == 'PLTGEN' ? 'selected' : '' }} value="PLTGEN">PLTGEN</option>
                            <option {{ isset($applicant) && $applicant->rank == 'PMGEN' ? 'selected' : '' }} value="PMGEN">PMGEN</option>
                            <option {{ isset($applicant) && $applicant->rank == 'PBGEN' ? 'selected' : '' }} value="PBGEN">PBGEN</option>
                            <option {{ isset($applicant) && $applicant->rank == 'PCOL' ? 'selected' : '' }} value="PCOL">PCOL</option>
                            <option {{ isset($applicant) && $applicant->rank == 'PLTCOL' ? 'selected' : '' }} value="PLTCOL">PLTCOL</option>
                            <option {{ isset($applicant) && $applicant->rank == 'PMAJ' ? 'selected' : '' }} value="PMAJ">PMAJ</option>
                            <option {{ isset($applicant) && $applicant->rank == 'PCPT' ? 'selected' : '' }} value="PCPT">PCPT</option>
                            <option {{ isset($applicant) && $applicant->rank == 'PLT' ? 'selected' : '' }} value="PLT">PLT</option>
                            <option {{ isset($applicant) && $applicant->rank == 'PEMS' ? 'selected' : '' }} value="PEMS">PEMS</option>
                            <option {{ isset($applicant) && $applicant->rank == 'PCMS' ? 'selected' : '' }} value="PCMS">PCMS</option>
                            <option {{ isset($applicant) && $applicant->rank == 'PSMS' ? 'selected' : '' }} value="PSMS">PSMS</option>
                            <option {{ isset($applicant) && $applicant->rank == 'PMSg' ? 'selected' : '' }} value="PMSg">PMSg</option>
                            <option {{ isset($applicant) && $applicant->rank == 'PSSg' ? 'selected' : '' }} value="PSSg">PSSg</option>
                            <option {{ isset($applicant) && $applicant->rank == 'PCpl' ? 'selected' : '' }} value="PCpl">PCpl</option>
                            <option {{ isset($applicant) && $applicant->rank == 'Patrolman' ? 'selected' : '' }} value="Patrolman">Patrolman</option>
                            <option {{ isset($applicant) && $applicant->rank == 'NUP' ? 'selected' : '' }} value="NUP">NUP</option>
                            <option {{ isset($applicant) && $applicant->rank == 'CIV' ? 'selected' : '' }} value="CIV">CIV</option>
                          </select>
                          <label for="rank"><span class="text-danger">*</span>Rank</label>
                        </div>

                        <div id="civFields" class="{{(isset($applicant) && $applicant->rank == 'CIV') ? '' : 'd-none'}}">
                          <div class="form-floating mb-3">
                            <input class="form-control" id="endorser" type="text" name="endorser" placeholder="Enter your Name of Endorser" value="{{ isset($applicant) ? $applicant->endorser : '' }}"/>
                            <label for="endorser"><span class="text-danger">*</span>Name of Endorser</label>
                          </div>

                          <div>
                            <div class="form-floating mb-3">
                              <input class="form-control file" id="endorserId" data-target="src" data-preview="#endorserIdPreview" type="file" name="endorser_id" accept="image/*" placeholder="Upload your Endorser ID" value="{{ isset($applicant) ? '/storage/'.$applicant->endorser_id : '' }}"/>
                              <label for="endorserId"><span class="text-danger">*</span>Endorser ID</label>
                            </div>
                            <div class="form-floating mb-3 text-center">
                              <img id="endorserIdPreview" class="preview-images prev-image" src="{{ isset($applicant) ? '/storage/'.$applicant->endorser_id : '' }}"/>
                            </div>
                          </div>

                          <div>
                            <div class="form-floating mb-3">
                              <input class="form-control file" id="driverLicense" data-target="src" data-preview="#driverLicensePreview" type="file" name="driver_license" accept="image/*" placeholder="Upload your Drivers License" />
                              <label for="driverLicense"><span class="text-danger">*</span>Driver's License ID</label>
                            </div>
                            <div class="form-floating mb-3 text-center">
                              <img id="driverLicensePreview" class="preview-images prev-image" src="{{ isset($applicant) ? '/storage/'.$applicant->drivers_license : '' }}"/>
                            </div>
                          </div>
                        </div>

                        <div class="form-floating mb-3">
                          <textarea required class="form-control" id="address" name="address">{{ isset($applicant) ? $applicant->address : '' }}</textarea>
                          <label for="address"><span class="text-danger">*</span>Address</label>
                        </div>
                        <div class="form-floating mb-3">
                          <input required class="form-control" id="designation" type="text" name="designation" placeholder="Enter your Designation/Position" value="{{ isset($applicant) ? $applicant->designation : '' }}"/>
                          <label for="designation"><span class="text-danger">*</span>Designation/Position</label>
                        </div>
                        <div class="form-floating mb-3">
                          <select class="form-control" id="office" name="office" required>
                            <option value="">Select Office/Unit Assignment</option>
                            <option {{ isset($applicant) && $applicant->office == 'IPPO' ? 'selected' : '' }} value="IPPO">IPPO</option>
                            <option {{ isset($applicant) && $applicant->office == 'CPPO' ? 'selected' : '' }} value="CPPO">CPPO</option>
                            <option {{ isset($applicant) && $applicant->office == 'BPPO' ? 'selected' : '' }} value="BPPO">BPPO</option>
                            <option {{ isset($applicant) && $applicant->office == 'SCPO' ? 'selected' : '' }} value="SCPO">SCPO</option>
                            <option {{ isset($applicant) && $applicant->office == 'NVPO' ? 'selected' : '' }} value="NVPO">NVPO</option>
                            <option {{ isset($applicant) && $applicant->office == 'RPRMD' ? 'selected' : '' }} value="RPRMD">RPRMD</option>
                            <option {{ isset($applicant) && $applicant->office == 'RID' ? 'selected' : '' }} value="RID">RID</option>
                            <option {{ isset($applicant) && $applicant->office == 'ROD' ? 'selected' : '' }} value="ROD">ROD</option>
                            <option {{ isset($applicant) && $applicant->office == 'RTOC' ? 'selected' : '' }} value="RTOC">RTOC</option>
                            <option {{ isset($applicant) && $applicant->office == 'RLRDD' ? 'selected' : '' }} value="RLRDD">RLRDD</option>
                            <option {{ isset($applicant) && $applicant->office == 'RCADD' ? 'selected' : '' }} value="RCADD">RCADD</option>
                            <option {{ isset($applicant) && $applicant->office == 'RCD' ? 'selected' : '' }} value="RCD">RCD</option>
                            <option {{ isset($applicant) && $applicant->office == 'RIDMD' ? 'selected' : '' }} value="RIDMD">RIDMD</option>
                            <option {{ isset($applicant) && $applicant->office == 'RLDDD' ? 'selected' : '' }} value="RLDDD">RLDDD</option>
                            <option {{ isset($applicant) && $applicant->office == 'RPSMU' ? 'selected' : '' }} value="RPSMU">RPSMU</option>
                            <option {{ isset($applicant) && $applicant->office == 'RICTMD' ? 'selected' : '' }} value="RICTMD">RICTMD</option>
                            <option {{ isset($applicant) && $applicant->other_office ? 'selected' : '' }} value="others">Others</option>
                          </select>
                          <label for="office"><span class="text-danger">*</span>Office/Unit Assignment</label>
                        </div>
                        <div id="officeFields" class="{{(isset($applicant) && $applicant->other_office) ? '' : 'd-none'}}">
                          <div class="form-floating mb-3">
                            <input class="form-control" id="otherOffice" type="text" name="otherOffice" placeholder="Other Office/Unit Assignment" value="{{ isset($applicant) ? $applicant->office : '' }}" />
                            <label for="otherOffice"><span class="text-danger">*</span>Other Office/Unit Assignment</label>
                          </div>
                        </div>

                        <div class="row mb-3">
                          <div class="col-md-6">
                            <div class="form-floating mb-3">
                              <input 
                                required 
                                class="form-control mobile-number" 
                                id="mobile" 
                                type="text" 
                                maxlength="11" 
                                name="mobile" 
                                placeholder="Enter your Mobile Number" 
                                value="{{ isset($applicant) ? $applicant->mobile : '' }}" />
                              <label for="mobile"><span class="text-danger">*</span>Mobile Number</label>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-floating mb-3">
                              <input 
                                class="form-control" 
                                id="telephone" 
                                type="number" 
                                name="telephone" 
                                placeholder="Enter your Telephone Number" 
                                onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))"
                                value="{{ isset($applicant) ? $applicant->telephone : '' }}" />
                              <label for="telephone">Telephone Number</label>
                            </div>
                          </div>
                        </div>
                        <div class="form-floating mb-3">
                          <input {{ isset($applicant) ? '' : 'required' }} class="form-control file" id="pnpId" type="file" name="pnp_id" accept="image/*" placeholder="Upload your PNP ID" />
                          <label for="pnpId"><span class="text-danger">*</span>PNP ID Picture</label>
                        </div>
                        <div class="form-floating mb-3 text-center">
                          <img class="prev-image" id="imgPreview" src="{{ isset($applicant) ? '/storage/'.$applicant->pnp_id_picture : '' }}" />
                        </div>
  
                        <div class="mt-4 mb-0">
                          <div class="d-grid">
                            <button type="button" id="nextInfo" class="btn btn-primary btn-block">Next Step</button>
                          </div>
                        </div>
                      </div>

                      <div id="vehicleInfo" class="d-none">
                        <div class="row mb-3">
                          <div class="col">
                            <h3>Step 2: Vehicle Information</h3>
                          </div>
                          <div class="col-auto">
                            <button class="btn btn-primary" id="backToStep1">
                              <i class="fa-solid fa-arrow-left-long"></i> back to Step 1
                            </button>
                          </div>
                        </div>

                        <div class="row mb-3">
                          <div class="col-md-6">
                            <div class="form-floating">
                              <select class="form-control" id="type" name="type" required>
                                <option value="">Select vehicle type</option>
                                <option {{ isset($applicant) && $applicant->vehicle->type == 'motor' ? 'selected' : '' }} value="motor">Motor</option>
                                <option {{ isset($applicant) && $applicant->vehicle->type == 'car' ? 'selected' : '' }} value="car">Car</option>
                              </select>
                              <label for="type"><span class="text-danger">*</span>Vehicle Type</label>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-floating mb-3 mb-md-0">
                              <input class="form-control" id="plateNumber" type="text" name="plate_number" placeholder="Enter your plate number" required value="{{ isset($applicant) ? $applicant->vehicle->plate_number : '' }}" />
                              <label for="plateNumber"><span class="text-danger">*</span>Plate Number</label>
                            </div>
                          </div>
                        </div>
              
                        <div class="form-floating mb-3">
                          <input class="form-control" id="make" type="text" name="make" placeholder="Enter Make" required value="{{ isset($applicant) ? $applicant->vehicle->make : '' }}" />
                          <label for="make"><span class="text-danger">*</span>Make</label>
                        </div>
              
                        <div class="row mb-3">
                          <div class="col-md-6">
                            <div class="form-floating mb-3 mb-md-0">
                              <input class="form-control" id="model" type="text" name="model" placeholder="Enter Series" required value="{{ isset($applicant) ? $applicant->vehicle->model : '' }}" />
                              <label for="model"><span class="text-danger">*</span>Series</label>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-floating">
                              <select class="form-control" id="yearModel" name="year_model" required>
                                <option value="">Select Year Model</option>
                                @for ($i = date("Y"); $i >= 1850; $i--)
                                  <option {{ isset($applicant) && $applicant->vehicle->year_model == $i ? 'selected' : '' }} value="{{ $i }}">{{ $i }}</option>
                                @endfor
                              </select>
                              <label for="yearModel"><span class="text-danger">*</span>Year Model</label>
                            </div>
                          </div>
                        </div>
              
                        <div class="form-floating mb-3">
                          <input class="form-control" id="color" type="text" name="color" placeholder="Enter Color" required value="{{ isset($applicant) ? $applicant->vehicle->color : '' }}" />
                          <label for="color"><span class="text-danger">*</span>Color</label>
                        </div>
              
                        <div class="row mb-3">
                          <div class="col-md-6">
                            <div class="form-floating mb-3 mb-md-0">
                              <input class="form-control" id="engineNumber" type="text" name="engine_number" placeholder="Enter Engine Number" required value="{{ isset($applicant) ? $applicant->vehicle->engine_number : '' }}" />
                              <label for="engineNumber"><span class="text-danger">*</span>Engine Number</label>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-floating mb-3 mb-md-0">
                              <input class="form-control" id="chassisNumber" type="text" name="chassis_number" placeholder="Enter Chassis Number" required value="{{ isset($applicant) ? $applicant->vehicle->chassis_number : '' }}" />
                              <label for="chassisNumber"><span class="text-danger">*</span>Chassis Number</label>
                            </div>
                          </div>
                        </div>

                        <div class="form-floating mb-3">
                          <select class="form-control" id="ownVehicle" name="own_vehicle" required>
                            <option value="">Select from options</option>
                            <option {{ isset($applicant) && $applicant->vehicle->own_vehicle == 1 ? 'selected' : '' }} value="yes">Yes</option>
                            <option {{ isset($applicant) && $applicant->vehicle->own_vehicle == 0 ? 'selected' : '' }} value="no">No</option>
                          </select>
                          <label for="ownVehicle"><span class="text-danger">*</span>Do you own the vehicle?</label>
                        </div>

                        <div id="deedOfSaleField" class="{{ isset($applicant) && $applicant->vehicle->own_vehicle == 1 ? 'd-none' : '' }}">
                          <div class="form-floating mb-3">
                            <input class="form-control file" id="deedOfSale" data-target="src" data-preview="#deedOfSalePreview" type="file" name="deed_of_sale" accept="image/*" placeholder="Deed of Sale" />
                            <label for="deedOfSale"><span class="text-danger">*</span>Deed of Sale</label>
                          </div>
                          <div class="form-floating mb-3 text-center">
                            <img id="deedOfSalePreview" class="preview-images prev-image" src="{{ isset($applicant) ? '/storage/'.$applicant->vehicle->deed_of_sale : '' }}"/>
                          </div>
                        </div>
                        
                        <div>
                          <div id="orFile" class="form-floating mb-3">
                            <input class="form-control file" {{ isset($applicant) ? '' : 'required' }} id="or" data-target="src" data-preview="#orPreview" type="file" name="or" accept="image/*" placeholder="Upload your OR" />
                            <label for="or"><span class="text-danger">*</span>OR</label>
                          </div>
                          <div class="form-floating mb-3 text-center">
                            <img id="orPreview" class="preview-images prev-image" src="{{ isset($applicant) ? '/storage/'.$applicant->vehicle->or : '' }}"/>
                          </div>
                        </div>

                        <div>
                          <div id="crFile" class="form-floating mb-3">
                            <input class="form-control file" {{ isset($applicant) ? '' : 'required' }} id="cr" data-target="src" data-preview="#crPreview" type="file" name="cr" accept="image/*" placeholder="Upload your CR" />
                            <label for="cr"><span class="text-danger">*</span>CR</label>
                          </div>
                          <div class="form-floating mb-3 text-center">
                            <img id="crPreview" class="preview-images prev-image" src="{{ isset($applicant) ? '/storage/'.$applicant->vehicle->cr : '' }}"/>
                          </div>
                        </div>
                        
                        <div>
                          <div id="photosFile" class="form-floating mb-3">
                            <input class="form-control file" {{ isset($applicant) ? '' : 'required' }} id="photos" data-target="element" data-preview="#vehiclePhotos" type="file" name="photos[]" accept="image/*" placeholder="Upload photo of your vehicle" multiple/>
                            <label for="photos"><span class="text-danger">*</span>Photos of Vehicle</label>
                          </div>
                          <div class="form-floating mb-3 text-center photos-preview" id="vehiclePhotos">
                            @if (isset($applicant))
                              @foreach ($applicant->vehicle->photos as $photo)
                                <img class="preview-images prev-image" src="{{ '/storage/'.$photo->image }}"/>
                              @endforeach
                            @endif
                          </div>
                        </div>

                        <div class="mb-3 form-check">
                          <input type="checkbox" class="form-check-input" id="agree">
                          <label class="form-check-label" for="agree">
                            After having duly sworn to in accordance with law, I do hereby depose and state; 
                            I am an applicant for RHQ-PNP decal/passcard; 
                            All documents I have submitted to support my application for decal/passcard are machine copies from original documents; 
                            I shall abide by all Camp Rules and Regulations pertaining to operation and routing of vehicles, speed limits and parking, Presidential Decree 96 and the Land Transportation Code while inside the Camp; and 
                            I shall surrender the decal/passcard to RID/R2 upon sale or transfer of my vehicle. 
                          </label>
                        </div>

                        <div class="mt-4 mb-0">
                          <div class="d-grid">
                            <button disabled type="submit" id="submitForm" class="btn btn-primary btn-block">{{ isset($applicant) ? 'Update' : 'Send' }} Application</button>
                          </div>
                        </div>
                      </div>

                    </form>
                  </div>
                  <div class="card-footer text-center py-3">
                    <div class="small"><a href="/login">Go to Login</a></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>
  </body>
</html>
