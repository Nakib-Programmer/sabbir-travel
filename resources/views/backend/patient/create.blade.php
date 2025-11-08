
@extends('layouts.app')

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Patient  Form</h4>
          <form action="{{ route('patient.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row">
              @if ($errors->any())
                  <div class="alert alert-danger">
                      <ul>
                          @foreach ($errors->all() as $error)
                              <li>{{ $error }}</li>
                          @endforeach
                      </ul>
                  </div>
              @endif
              <div class="col-md-4 col-sm-12">
                <div class="mb-3">
                  <label class="form-label">Name</label>
                  <input type="text" class="form-control" value="{{ old('name') }}" name="name" required placeholder="Type Name" />
                </div>
              </div>
              <div class="col-md-4 col-sm-12">
                <div class="mb-3">
                  <label class="form-label">Passport Number</label>
                  <input type="text" class="form-control" value="{{ old('passport') }}" name="passport" required placeholder="Type Passport Number" />
                </div>
              </div>
              <div class="col-md-4 col-sm-12">
                <div class="mb-3">
                  <label class="form-label">Passport Image</label>
                  <input type="file" class="form-control" name="passport_image" required />
                </div>
              </div>
              <div class="col-md-6 col-sm-12">
                <div class="mb-3">
                  <label class="form-label">Serial No</label>
                  <input type="text" class="form-control" value="{{ old('serial_no') }}" name="serial_no" required placeholder="Type Serial No" />
                </div>
              </div>
              <div class="col-md-3 col-sm-6">
              <div class="mb-3">
                    <label class="form-label">Date</label>
                    <input 
                      type="date" 
                      class="form-control" 
                      name="date" 
                      value="{{ old('date', date('Y-m-d')) }}" 
                      max="{{ date('Y-m-d') }}" 
                      required 
                  />

                </div>
              </div>
              <div class="col-md-3 col-sm-6">
                <div class="mb-3">
                  <label class="form-label">Medical</label>
                  <select class="form-select" name="medical_id" aria-label="Default select example" required>
                    <option selected value="">Select Medical</option>
                    @foreach($medi as $data)
                      <option value="{{$data->id}}">{{$data->name}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-4 col-sm-12">
                <div class="mb-3">
                  <label class="form-label">Reference By</label>
                  <select class="form-select" name="ref_id" aria-label="Default select example">
                    <option selected>Select Reference</option>
                    @foreach($ref as $data)
                      <option value="{{$data->id}}">{{$data->name}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-4 col-sm-12">
                <div class="mb-3">
                  <label class="form-label">Country</label>
                  <select class="form-select" name="country" required>
                    <option selected>Select Reference</option>
                    <option value="Saudi Arabia">Saudi Arabia</option>
                    <option value="kuwait">kuwait</option>
                    <option value="Dubai">Dubai</option>
                  </select>
                </div>
              </div>
              <div class="col-md-4 col-sm-12">
                <div class="mb-3">
                  <label class="form-label">Medical Slip</label>
                  <input type="file" class="form-control" name="slip" required />
                </div>
              </div>
              <div class="col-md-4 col-sm-12">
                <div class="d-flex flex-wrap gap-2">
                  <button type="submit" class="btn btn-primary waves-effect waves-light">
                    Submit
                  </button>
                  <button type="reset" class="btn btn-secondary waves-effect">
                    Reset
                  </button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
  </div>
@endsection