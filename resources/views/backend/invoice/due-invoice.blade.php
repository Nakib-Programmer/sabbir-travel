@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <table id="datatable" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>Passport</th>
              <th>Name</th>
              <th>Date</th>
              <th>Expired</th>
              <th>Country</th>
              <th>Total</th>
              <th>Paid</th>
              <th>Due</th>
              <th>Slip</th>
              <th>Action</th>
            </tr>
          </thead>

          <tbody>
            @foreach($data as $invoice)
            <tr>
              <td>{{ $invoice->patient->passport }}</td>
              <td>{{ $invoice->patient->name }}</td>
              <td>{{ $invoice->patient->date }}</td>
              <td>
              @php
                  $patientDate = \Carbon\Carbon::parse($invoice->patient->date); // Parse the patient date
                  $today = \Carbon\Carbon::today(); // Get today's date
                  $daysLeft = $patientDate->diffInDays($today, false); // Calculate the difference in days
              @endphp

              @if ($daysLeft > 0 && $daysLeft <= 30)
                  {{ $daysLeft }} days
              @else
                  Expired
              @endif
              </td>
              <td>{{ $invoice->patient->country }}</td>
              <td>{{ $invoice->subtotal }}</td>
              <td>{{ $invoice->paid }}</td>
              <td>{{ $invoice->due }}</td>
              <td>
                @if($invoice->patient->slip)
                    <a href="{{ asset('invoice/' . $invoice->patient->slip) }}" target="_blank" class="btn btn-info btn-sm">
                      View Slip
                    </a>
                  @else
                    <p>No Slip</p>
                  @endif
              </td>
              <td>
              <div class="d-flex gap-3">
                <a href="{{route('invoice.edit', $invoice->id)}}" class="btn btn-sm btn-success"><i class="mdi mdi-pencil font-size-18"></i></a>
                <a href="{{ route('invoice.print', $invoice->id) }}" class="btn btn-sm btn-info" target="_blank"><i class="mdi mdi-printer font-size-18"></i></a>
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