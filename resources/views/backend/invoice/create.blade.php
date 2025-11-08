@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Create Invoice</h4>
                <form action="{{ route('invoice.store') }}" method="post">
                    @csrf

                    <!-- Patient Selection -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="patient-select">Patient</label>
                                <select name="patient_id" id="patient-select" class="form-select" required>
                                    <option value="">Select Patient</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Expiration Date -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="expiration-date">Expiration Date</label>
                                <input type="date" name="expiration_date" id="expiration-date" class="form-control"
                                       value="{{ now()->addDays(30)->format('Y-m-d') }}" readonly>
                            </div>
                        </div>

                        <!-- Medical Test Selection -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="test-select">Medical Tests</label>
                                <select id="test-select" class="form-select">
                                    <option value="">Select Medical Test</option>
                                    @foreach($medicalTests as $test)
                                        <option value="{{ $test->id }}">{{ $test->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="invoice-table">
                            <thead>
                                <tr>
                                    <th style="width: 20%;">Test Name</th>
                                    <th style="width: 25%;">Price</th>
                                    <th style="width: 15%;">Quantity</th>
                                    <th style="width: 25%;">Total</th>
                                    <th style="width: 15%;">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <!-- Subtotal -->
                    <div class="mb-3">
                        <label for="subtotal">Subtotal</label>
                        <input type="text" id="subtotal" name="subtotal" class="form-control" readonly>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary">Create Invoice</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Add selected test to the table
    document.getElementById('test-select').addEventListener('change', function () {
    const selectedTest = this.options[this.selectedIndex];
    const testName = selectedTest.text;
    const testId = selectedTest.value;

    if (!testId) return;

    const tableBody = document.querySelector('#invoice-table tbody');

    // Check if test already exists in the table
    if (document.querySelector(`input[name="tests[${testId}][medical_test_id]"]`)) {
        alert('This test is already added.');
        this.selectedIndex = 0;
        return;
    }

    // Create a new row with test name and editable price/quantity
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>${testName}</td>
        <td>
            <input type="hidden" name="tests[${testId}][medical_test_id]" value="${testId}">
            <input type="number" name="tests[${testId}][price]" class="form-control price w-100" placeholder="Enter price" required>
        </td>
        <td>
            <input type="number" name="tests[${testId}][quantity]" class="form-control quantity w-100" value="1" min="1">
        </td>
        <td>
            <input type="text" name="tests[${testId}][total]" class="form-control total w-100" value="0" readonly>
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm remove-row w-100">Remove</button>
        </td>
    `;
    tableBody.appendChild(row);
    updateSubtotal();

    // Unselect the dropdown option after selection
    this.selectedIndex = 0; // Reset the select input back to the default "Select Medical Test" option
});

    // Recalculate total when price or quantity changes
    document.addEventListener('input', function (event) {
        if (event.target.classList.contains('quantity') || event.target.classList.contains('price')) {
            const row = event.target.closest('tr');
            const price = parseFloat(row.querySelector(`input[name*="[price]"]`).value) || 0;
            const quantity = parseInt(row.querySelector(`input[name*="[quantity]"]`).value) || 1;
            const total = price * quantity;
            row.querySelector(`input[name*="[total]"]`).value = total.toFixed(2);
            updateSubtotal();
        }
    });

    // Remove row when 'Remove' button is clicked
    document.addEventListener('click', function (event) {
        if (event.target.classList.contains('remove-row')) {
            event.target.closest('tr').remove();
            updateSubtotal();
        }
    });

    // Calculate and update subtotal
    function updateSubtotal() {
        let subtotal = 0;
        document.querySelectorAll('.total').forEach(input => {
            subtotal += parseFloat(input.value) || 0;
        });
        document.getElementById('subtotal').value = subtotal.toFixed(2);
    }
</script>
@endsection
