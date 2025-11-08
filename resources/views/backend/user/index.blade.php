@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title"><a class="btn btn-primary float-end" href="{{route('user-list.create')}}">Add User</a></h4>
      </div>
      <div class="card-body">
        <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Action</th>
            </tr>
          </thead>

          <tbody>
            @foreach($data as $item)
            <tr>
              <td>{{$item['name']}}</td>
              <td>{{$item['email']}}</td>
              <td>
              <div class="d-flex gap-3">
                <a href="{{route('user-list.edit', $item['id'])}}" class="btn btn-sm btn-success"><i class="mdi mdi-pencil font-size-18"></i></a>
                <form action="{{ route('user-list.destroy', $item['id']) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">
                      <i class="mdi mdi-delete font-size-18"></i>
                    </button>
                </form>
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

@endsection