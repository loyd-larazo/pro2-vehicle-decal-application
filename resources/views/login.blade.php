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
      <script>
        $(function() {
          $(document).on('keydown', '.password', function(e) {
            if (e.keyCode == 32) return false;
          });
        })
      </script>
  </head>
  <body class="bg-primary">
    <div id="layoutAuthentication">
      <div id="layoutAuthentication_content">
        <main>
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-lg-5">
                <div class="card shadow-lg border-0 rounded-lg mt-5">
                  <div class="card-header">
                    <img class="logo position-absolute" src="/images/logo.png"/>
                    <h3 class="text-center font-weight-light my-4">Login</h3>
                  </div>
                  <div class="card-body">
                    @if(isset($error))
                      <div class="alert alert-danger text-center" role="alert">
                        {{ $error }}
                      </div>
                    @endif
                    <form action="/login" method="POST">
                      <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                      <div class="form-floating mb-3">
                        <input class="form-control" id="inputEmail" name="email" type="email" placeholder="name@example.com" value="{{ isset($email) ? $email : '' }}"/>
                        <label for="inputEmail">Email address</label>
                      </div>
                      <div class="form-floating mb-3">
                        <input class="form-control password" id="inputPassword" name="password" type="password" placeholder="Password" />
                        <label for="inputPassword">Password</label>
                      </div>
                      <div class="d-flex align-items-center justify-content-center mt-4 mb-0">
                        <button type="submit" class="btn btn-primary" href="index.html">Login</button>
                      </div>
                    </form>
                  </div>
                  <div class="card-footer text-center py-3">
                    <div class="small"><a href="/application">Send new Application!</a></div>
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
