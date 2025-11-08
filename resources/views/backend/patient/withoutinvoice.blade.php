@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
      <!-- <h4 class="card-title"><a class="btn btn-primary float-end" href="{{route('patient.create')}}">Add Patient</a></h4> -->
      </div>
      <div class="card-body">
        <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
          <thead>
            <tr>
              <th>Name</th>
              <th>Passport</th>
              <th>Date</th>
              <th>Reference By</th>
              <th>Medical Center</th>
              <th>Action</th>
            </tr>
          </thead>

          <tbody>
            @foreach($data as $item)
            <tr>
              <td>{{$item->name}}</td>
              <td>{{$item->passport}}</td>
              <td>{{$item->date}}</td>
              <td>
              @if(Auth::user()->type === 1) {{-- Check if the user is an admin --}}
                  {{$item['user']['name'] ?? ''}}
                @else
                  {{$item['reference']['name'] ?? ''}}
                @endif
              </td>
              <td>{{$item->medical->name}}</td>
              <td>
              
                
                <div class="d-flex gap-3">
                  <a href="{{route('patient.edit', $item->id)}}" class="btn btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i class="mdi mdi-pencil font-size-18"></i></a>
                  <a href="{{route('patient.show', $item->id)}}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="View"><i class="mdi mdi-eye font-size-18"></i></a>
                  @if($item->slip)
                  <a href="{{ asset('invoice/' . $item->slip) }}" target="_blank" class="btn btn-sm btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="Slip Download"><i class="mdi mdi-download font-size-18"></i></a>
                  @endif
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