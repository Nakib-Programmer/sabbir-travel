<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px;
        }
        .invoice-container {
            width: 95%;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .invoice-header h4 {
            margin: 0;
            font-size: 21px;
        }

        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-top: 10px;
        }

        .patient-info,
        .invoice-info {
            font-size: 14px;
        }

        .patient-info p,
        .invoice-info p {
            margin: 5px 0;
        }

        .invoice-info {
            text-align: right;
        }

        .patient-info {
            text-align: left;
        }
        .invoice-details {
            margin-bottom: 20px;
        }
        .invoice-details p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .total {
            text-align: right;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #555;
            margin-top: 20px;
        }
    </style>
</head>
<body onload="window.print();">
    <div class="invoice-container">
        <!-- Header Section -->
        <div class="invoice-header">
            <h4>{{$invoice->patient->medical->name}}</h4>
            <div class="header-row">
                <div class="patient-info">
                    <p><strong>Patient Name:</strong> {{ $invoice->patient->name }}</p>
                    <p><strong>Passport Number:</strong> {{ $invoice->patient->passport }}</p>
                    <p><strong>Expiration Date:</strong> {{ \Carbon\Carbon::parse($invoice->expiration_date)->format('M d, Y') }}</p>
                </div>
                <div class="invoice-info">
                    <p><strong>Invoice #:</strong> {{ $invoice->id }}</p>
                    <p><strong>Pay Amount:</strong> {{ $invoice->paid }}</p>
                    <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($invoice->created_at)->format('M d, Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Invoice Items Table -->
        <table>
            <thead>
                <tr>
                    <th>Medical Test</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->item as $item)
                    <tr>
                        <td>{{ $item->medicalTest->name }}</td>
                        <td>{{ number_format($item->price, 2) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Total Section -->
        <div class="total">
            <h3>Grand Total : {{ number_format($invoice->subtotal, 2) }}</h3>
        </div>

        <!-- Footer Section -->
        <div class="footer">
            <p>Thank you for your business!</p>
        </div>
    </div>
</body>
</html>
