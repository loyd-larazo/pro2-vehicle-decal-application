@extends('layout')

@section('content')
  <div class="row p-0 m-0 mb-2">
    <img class="logo-heading col-auto " src="/images/logo.png"/>
    <h1 class="col mt-3">Dashboard</h1>
    
  </div>
              
  <div class="card mb-4">
    <div class="card-body px-4 row">
      <div class="stats col-md-6">
        <div class="border border-primary p-3 position-relative m-3 mx-0">
          <a href="/applicants" class="btn btn-info btn-block dashboard-print"> <i class="fa-solid fa-file-export"></i></i></a>
          <div class="icon text-center">
            <i class="fa-solid fa-user-tie"></i>
            <h3>{{ $applicants['total'] }} Applicants</h3>
          </div>
          <div class="text-start row pt-5">
            <div class="stats-score col-12 col-md-3 text-warning">Pending: {{ $applicants['pending'] }}</div>
            <div class="stats-score col-12 col-md-3 text-success">Approved: {{ $applicants['approved'] }}</div>
            <div class="stats-score col-12 col-md-3 text-danger">Rejected: {{ $applicants['rejected'] }}</div>
            <div class="stats-score col-12 col-md-3 text-info">Change Request: {{ $applicants['request_change'] }}</div>
          </div>
        </div>
      </div>

      <div class="stats col-md-6">
        <div class="row border border-success p-3 position-relative m-3 mx-0">
          <a href="/vehicles?status=all&search=car" class="btn btn-info btn-block dashboard-print"> <i class="fa-solid fa-file-export"></i></i></a>
          <div class="icon text-center">
            <i class="fa-solid fa-car"></i>
            <h3>{{ $cars['total'] }} Cars</h3>
          </div>
          <div class="text-start row pt-5">
            <div class="stats-score col-12 col-md-4 text-warning">Pending: {{ $cars['pending'] }}</div>
            <div class="stats-score col-12 col-md-4 text-success">Approved: {{ $cars['approved'] }}</div>
            <div class="stats-score col-12 col-md-4 text-danger">Rejected: {{ $cars['rejected'] }}</div>
          </div>
        </div>
      </div>

      <div class="stats col-md-6">
        <div class="row border border-danger p-3 position-relative m-3 mx-0">
          <a href="/vehicles?status=all&search=motor" class="btn btn-info btn-block dashboard-print"> <i class="fa-solid fa-file-export"></i></i></a>
          <div class="icon text-center">
            <i class="fa-solid fa-motorcycle"></i>
            <h3>{{ $motors['total'] }} Motorcycles</h3>
          </div>
          <div class="text-start row pt-5">
            <div class="stats-score col-12 col-md-4 text-warning">Pending: {{ $motors['pending'] }}</div>
            <div class="stats-score col-12 col-md-4 text-success">Approved: {{ $motors['approved'] }}</div>
            <div class="stats-score col-12 col-md-4 text-danger">Rejected: {{ $motors['rejected'] }}</div>
          </div>
        </div>
      </div>

      <div class="stats col-md-6">
        <div class="row border border-warning p-3 position-relative m-3 mx-0">
          <a href="/release" class="btn btn-info btn-block dashboard-print"> <i class="fa-solid fa-file-export"></i></i></a>
          <div class="icon text-center">
            <i class="fa-solid fa-address-card"></i>
            <h3>{{ $release['total'] }} For Release</h3>
          </div>
          <div class="text-start row pt-5">
            <div class="stats-score col-12 col-md-3 text-warning">Pending: {{ $release['pending'] }}</div>
            <div class="stats-score col-12 col-md-3 text-success">Issued: {{ $release['issued'] }}</div>
            <div class="stats-score col-12 col-md-3 text-danger">Rejected: {{ $release['rejected'] }}</div>
            <div class="stats-score col-12 col-md-3 text-secondary">Expired: {{ $release['expired'] }}</div>
          </div>
        </div>
      </div>

    </div>
  </div>
@endsection