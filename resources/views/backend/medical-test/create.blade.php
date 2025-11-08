@extends('layouts.app')

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Medical Test  Form</h4>
          <form action="{{ route('medical-test.store') }}" method="post">
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
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Name</label>
                  <input type="text" class="form-control" value="{{ old('name') }}" name="name" required placeholder="Type Name" />
                </div>
              </div>
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Price</label>
                  <input type="text" class="form-control" value="{{ old('price') }}" name="price" placeholder="Type Price" />
                </div>
              </div>
              <div class="col-6">
                <div class="d-flex flex-wrap gap-2" style="margin-top: 1cm;">
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
