@extends('layouts.app')

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Edit Patient Form</h4>
          <form action="{{ route('patient.update', $patient->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT') <!-- Use PUT method for update -->
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
              <div class="col-4">
                <div class="mb-3">
                  <label class="form-label">Name</label>
                  <input type="text" class="form-control" value="{{ old('name', $patient->name) }}" name="name" required placeholder="Type Name" />
                </div>
              </div>
              <div class="col-4">
                <div class="mb-3">
                  <label class="form-label">Passport Number</label>
                  <input type="text" class="form-control" value="{{ old('passport', $patient->passport) }}" name="passport" required placeholder="Type Passport Number" />
                </div>
              </div>
              <div class="col-4">
                <div class="mb-3">
                  <label class="form-label">Passport Image</label>
                  <input type="file" class="form-control" name="passport_image" />
                  @if($patient->passport_image)
                    <p class="mt-2">
                      Current Passport: <a href="{{ asset('passportImage/' . $patient->passport_image) }}" target="_blank">View Passport</a>
                    </p>
                  @endif
                </div>
              </div>
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Serial No</label>
                  <input type="text" class="form-control" value="{{ old('serial_no', $patient->serial_no) }}" name="serial_no" required placeholder="Type Serial No" />
                </div>
              </div>
              <div class="col-3">
                <div class="mb-3">
                  <label class="form-label">Date</label>
                  <input type="date" class="form-control" value="{{ old('date', $patient->date) }}" name="date" required />
                </div>
              </div>
              <div class="col-3">
                <div class="mb-3">
                  <label class="form-label">Medical</label>
                  <select class="form-select" name="medical_id" required>
                    <option value="">Select Medical</option>
                    @foreach($medi as $data)
                      <option value="{{ $data->id }}" {{ $patient->medical_id == $data->id ? 'selected' : '' }}>{{ $data->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-4">
                <div class="mb-3">
                  <label class="form-label">Reference By</label>
                  <select class="form-select" name="ref_id">
                    <option value="">Select Reference</option>
                    @foreach($ref as $data)
                      <option value="{{ $data->id }}" {{ $patient->ref_id == $data->id ? 'selected' : '' }}>{{ $data->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-4">
                <div class="mb-3">
                  <label class="form-label">Country</label>
                  <select class="form-select" name="country" required>
                    <option value="">Select Country</option>
                    <option value="Saudi Arabia" {{ $patient->country == 'Saudi Arabia' ? 'selected' : '' }}>Saudi Arabia</option>
                    <option value="kuwait" {{ $patient->country == 'kuwait' ? 'selected' : '' }}>kuwait</option>
                    <option value="Dubai" {{ $patient->country == 'Dubai' ? 'selected' : '' }}>Dubai</option>
                  </select>
                </div>
              </div>
              <div class="col-4">
                <div class="mb-3">
                  <label class="form-label">Medical Slip</label>
                  <input type="file" class="form-control" name="slip" />
                  @if($patient->slip)
                    <p class="mt-2">
                      Current Slip: <a href="{{ asset('invoice/' . $patient->slip) }}" target="_blank">View Slip</a>
                    </p>
                  @endif
                </div>
              </div>
              <div class="col-4">
                <div class="d-flex flex-wrap gap-2">
                  <button type="submit" class="btn btn-primary waves-effect waves-light">
                    Update
                  </button>
                  <a href="{{ route('patient.index') }}" class="btn btn-secondary waves-effect">
                    Cancel
                  </a>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
