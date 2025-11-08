@extends('layouts.app')

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Edit User</h4>
          <form action="{{ route('user-list.update', $user->id) }}" method="post">
            @csrf
            @method('PATCH')
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
                        <input type="text" class="form-control" value="{{ old('name', $user->name) }}" name="name" required placeholder="Type Name" />
                    </div>
                </div>

                <div class="col-4">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="{{ old('email', $user->email) }}" name="email" required placeholder="Type Email" />
                    </div>
                </div>

                <div class="col-4">
                    <div class="mb-3">
                        <label class="form-label">Password (Optional)</label>
                        <input type="text" class="form-control" name="password" placeholder="Type New Password (Leave blank to keep current)" />
                    </div>
                </div>

                <div class="col-6">
                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                            Update
                        </button>
                        <a href="{{ route('user-list.index') }}" class="btn btn-secondary waves-effect">
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
