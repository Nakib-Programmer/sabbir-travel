@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
      <h4 class="card-title"><a class="btn btn-primary float-end" href="{{route('contact.create')}}">Add Contact</a></h4>
      </div>
      <div class="card-body">
        <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
          <thead>
            <tr>
              <th>Name</th>
              <th>Phone</th>
              <th>Designation</th>
              <th>Organization</th>
              <th>Note</th>
              <th>Action</th>
            </tr>
          </thead>

          <tbody>
            @foreach($data as $item)
            <tr>
              <td>{{$item['name']}}</td>
              <td>{{$item['phone']}}</td>
              <td>{{$item['designation']}}</td>
              <td>{{$item['organization']}}</td>
              <td>{{$item['note']}}</td>
              <td>
              <div class="d-flex gap-3">
                <a href="{{route('contact.edit', $item['id'])}}" class="btn btn-sm btn-success"><i class="mdi mdi-pencil font-size-18"></i></a>
                <form action="{{ route('contact.destroy', $item['id']) }}" method="POST">
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