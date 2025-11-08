@extends('layouts.app')

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Patient  Details</h4>
            <div class="row">
              <div class="col-md-6">
                <p><strong>Name:</strong> {{ $patient->name }}</p>
              </div>
              <div class="col-md-6">
                <p><strong>Passport Number:</strong> {{ $patient->passport }}</p>
              </div>
              <div class="col-md-6">
                <p><strong>Serial No:</strong> {{ $patient->serial_no }}</p>
              </div>
              <div class="col-md-6">
                <p><strong>Date:</strong> {{ $patient->date }}</p>
              </div>
              <div class="col-md-6">
                <p><strong>Medical Center:</strong> {{ $patient->medical->name ?? 'N/A' }}</p>
              </div>
              <div class="col-md-6">
                <p><strong>Reference By:</strong> {{ $patient->reference->name ?? 'N/A' }}</p>
              </div>
              <div class="col-md-6">
                <p><strong>Country:</strong> {{ $patient->country }}</p>
              </div>
              <div class="col-md-6">
                <p><strong>Medical Slip:</strong>@if($patient->slip)
                  <a href="{{ asset('invoice/' . $patient->slip) }}" target="_blank" class="btn btn-info btn-sm">
                    View Slip
                  </a>
                @else
                  No Slip Uploaded
                @endif</p>
                
              </div>
              <div class="col-md-6">
                <p><strong>Passport Image:</strong>@if($patient->passport_image)
                  <a href="{{ asset('passportImage/' . $patient->passport_image) }}" target="_blank" class="btn btn-info btn-sm">
                    View Passport
                  </a>
                @else
                  No Slip Uploaded
                @endif</p>
              </div>
            </div>
            <div class="mt-4">
              <a href="{{ route('patient.index') }}" class="btn btn-secondary">Back to List</a>
              <a href="{{ route('patient.edit', $patient->id) }}" class="btn btn-primary">Edit Patient</a>
            </div>
          </div>
        </div>
      </div>
  </div>
@endsection