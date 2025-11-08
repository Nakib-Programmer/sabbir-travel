@extends('layouts.app')

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Reference  Form</h4>
          <form action="{{  route('reference.update', $data->id) }}" method="post">
            @csrf
            @method('PUT')
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
                  <input type="text" class="form-control" name="name" value="{{ $data->name }}" required placeholder="Type Name" />
                </div>
              </div>
              <div class="col-4">
                <div class="mb-3">
                  <label class="form-label">Phone Number</label>
                  <input type="number" class="form-control" name="phone" value="{{ $data->phone }}" required placeholder="Type Phone Number" />
                </div>
              </div>
              <div class="col-4">
                <div class="mb-3">
                  <label class="form-label">Code</label>
                  <input type="text" class="form-control" name="code" value="{{ $data->code }}" required placeholder="Type Code" />
                </div>
              </div>
              <div class="col-4">
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