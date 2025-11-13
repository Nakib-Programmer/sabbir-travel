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
              <th>Phone</th>
              <th>Passport</th>
              <th>Action</th>
            </tr>
          </thead>

          <tbody>
             @foreach($fit as $item)
                <tr>
                <td>{{$item['name']}}</td>
                <td>{{$item['phone']}}</td>
                <td>{{$item['passport']}}</td>
                <td>
                <div class="d-flex gap-3">
                    <a href="{{route('wafid-print', $item['id'])}}" class="btn btn-sm btn-success"><i class="mdi mdi-pencil font-size-18"></i></a>

                </div>
                </td>

                </tr>
                @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!-- end col -->
</div>
<!-- end row -->
@if (session('open_print_id'))
    <script>
        window.open("{{ route('wafid-print', session('open_print_id')) }}", "_blank");
    </script>
@endif
@endsection
