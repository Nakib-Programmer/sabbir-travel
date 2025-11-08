@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
      <h4 class="card-title"><a class="btn btn-primary float-end" href="{{route('wafid-slip.create')}}">Add Medical Test</a></h4>
      </div>
      <div class="card-body">
        <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
          <thead>
            <tr>
              <th>Name</th>
              <th>Price</th>
              <th>Action</th>
            </tr>
          </thead>

          <tbody>
           
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!-- end col -->
</div>
<!-- end row -->

@endsection
