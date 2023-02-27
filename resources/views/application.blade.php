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
          secureMobile();

          $('#applicationForm').submit(function(e) {
            e.preventDefault();
            hideError();

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

          $(document).on('keydown', '.password', function(e) {
            if (e.keyCode == 32) return false;
          });

          $('#agree').change(function() {
            if (this.checked) {
              $('#submitForm').removeAttr('disabled');
            } else {
              $('#submitForm').attr('disabled', 'disabled');
            }
          })
        })
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
                      @endif

                      <div class="row mb-3">
                        <div class="col-md-6">
                          <div class="form-floating mb-3 mb-md-0">
                            <input required class="form-control" id="inputFirstName" type="text" name="firstname" placeholder="Enter your first name" value="{{ isset($applicant) ? $applicant->firstname : '' }}"/>
                            <label for="inputFirstName">First name</label>
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
                        <label for="inputLastName">Last name</label>
                      </div>
                      <div class="row mb-3">
                        <div class="col-md-6">
                          <div class="form-floating mb-3 mb-md-0">
                            <input required class="form-control" id="inputEmail" type="email" name="email" placeholder="name@example.com" value="{{ isset($applicant) ? $applicant->email : '' }}"/>
                            <label for="inputEmail">Email address</label>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-floating mb-3 mb-md-0">
                            <input required class="form-control" id="confirmInputEmail" type="email" name="confirm-email" placeholder="name@example.com" value="{{ isset($applicant) ? $applicant->email : '' }}"/>
                            <label for="confirmInputEmail">Confirm Email address</label>
                          </div>
                        </div>
                      </div>
                      <div class="row mb-3">
                        <div class="col-md-6">
                          <div class="form-floating mb-3 mb-md-0">
                            <input required class="form-control password" id="inputPassword" type="password" name="password" placeholder="Create a password" />
                            <label for="inputPassword">Password</label>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-floating mb-3 mb-md-0">
                            <input required class="form-control password" id="inputPasswordConfirm" type="password" name="confirmPassword" placeholder="Confirm password" />
                            <label for="inputPasswordConfirm">Confirm Password</label>
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
                        </select>
                        <label for="rank">Rank</label>
                      </div>
                      <div class="form-floating mb-3">
                        <textarea required class="form-control" id="address" name="address">{{ isset($applicant) ? $applicant->address : '' }}</textarea>
                        <label for="address">Address</label>
                      </div>
                      <div class="form-floating mb-3">
                        <input required class="form-control" id="designation" type="text" name="designation" placeholder="Enter your Designation/Position" value="{{ isset($applicant) ? $applicant->designation : '' }}"/>
                        <label for="designation">Designation/Position</label>
                      </div>
                      <div class="form-floating mb-3">
                        <input required class="form-control" id="office" type="text" name="office" placeholder="Enter your Office/Unit Assignment" value="{{ isset($applicant) ? $applicant->office : '' }}" />
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
                              maxlength="11" 
                              name="mobile" 
                              placeholder="Enter your Mobile Number" 
                              value="{{ isset($applicant) ? $applicant->mobile : '' }}" />
                            <label for="mobile">Mobile Number</label>
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
                        <label for="pnpId">PNP ID Picture</label>
                      </div>
                      <div class="form-floating mb-3 text-center">
                        <img class="prev-image" id="imgPreview" src="{{ isset($applicant) ? '/storage/'.$applicant->pnp_id_picture : '' }}" />
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
