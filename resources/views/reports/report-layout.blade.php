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
    <link href="/css/layout.css" rel="stylesheet">
    <link href="/css/app.css" rel="stylesheet">

    <script src="/js/jquery-3.6.1.min.js"></script>
    <script src="/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
    <script src="/js/scripts.js"></script>
    <script>
      $(function() {
        var landscape = {{ isset($orientation) ? 1 : 0 }};
        if (landscape) {
          var css = '@page { size: landscape; }';
          var head = document.head || document.getElementsByTagName('head')[0];
          var style = document.createElement('style');

          style.type = 'text/css';
          style.media = 'print';

          if (style.styleSheet){
            style.styleSheet.cssText = css;
          } else {
            style.appendChild(document.createTextNode(css));
          }
          head.appendChild(style);
        }
        
        window.print();
      });
    </script>
  </head>
  <body onafterprint="window.close()">
    <div id="layoutSidenav">
      <main class="w-100 mt-5 mx-5">
        <div class="container-fluid px-4">
          <div class="row mb-3">
            <div class="col-auto">
              <img class="logo-report-heading col-auto " src="/images/logo.png"/>
            </div>
            <div class="col-auto h5 pt-4">
              Republic of the Philippines<br>
              Philippine National Police<br>
              Police Regional Office 2<br>
            </div>
            <div class="col text-end">
              {{date("F j, Y h:i:sA")}}
            </div>
          </div>
          
          @yield('content')
          
        </div>
      </main>
    </div>
  </body>
</html>
