<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\MedicalTest;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index(){
        $data = Invoice::query()
            ->with('patient'); // Eager load the patient relationship

        if (Auth::user()->type == 0) {
            // Apply condition on the patient relationship
            $data->whereHas('patient', function ($query) {
                $query->where('user_id', Auth::id());
            });
        }

        $data = $data->get(); // Retrieve the data
        return view('backend.invoice.index',compact('data'));
    }
    public function create()
    {
        $medicalTests = MedicalTest::all();
        $patients = Patient::whereDoesntHave('invoices', function ($query) {
            // Filter out invoices created within the last 30 days
            $query->where('created_at', '>=', now()->subDays(30));
        })->orWhereHas('invoices', function ($query) {
            // Ensure invoices are older than 30 days (expired)
            $query->where('created_at', '<', now()->subDays(30));
        })->get();
        return view('backend.invoice.create', compact('medicalTests','patients'));
    }
    public function store(Request $request){
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'subtotal' => 'required|numeric',
            'expiration_date' => 'required|date',
        ]);
        $invoice = Invoice::create([
            'patient_id' => $request->input('patient_id'),
            'subtotal' => $request->input('subtotal'),
            'due' => $request->input('subtotal'),
            'user_id' => Auth::user()->id,
            'expiration_date' => $request->input('expiration_date'),
        ]);
        if($request->input('tests')){
            foreach ($request->input('tests') as $test) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'medical_test_id' => $test['medical_test_id'],
                    'price' => $test['price'],
                    'quantity' => $test['quantity'],
                    'total' => $test['total'],
                ]);
            }
        }
        
        return redirect()->route('invoice.index')->with('success', 'Invoice created successfully!');
    }

    public function edit($id){
        $invoice = Invoice::with('item')->findOrFail($id);
        $medicalTests = MedicalTest::all();
        $patients = Patient::whereDoesntHave('invoices', function ($query) {
            $query->where('created_at', '>=', now()->subDays(30));
        })
        ->orWhereHas('invoices', function ($query) {
            $query->where('created_at', '<', now()->subDays(30));
        })
        ->get();

        $currentPatient = Patient::find($invoice->patient_id);
        if ($currentPatient) {
            $patients->push($currentPatient);
        }
        return view('backend.invoice.edit', compact('invoice', 'medicalTests', 'patients'));
    }

    public function update(Request $request, Invoice $invoice){
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'subtotal' => 'required|numeric',
            'tests' => 'array',
            'tests.*.medical_test_id' => 'required|exists:medical_tests,id',
            'tests.*.price' => 'required|numeric',
            'tests.*.quantity' => 'required|integer|min:1',
            'tests.*.total' => 'required|numeric',
        ]);

        // Update invoice details
        $invoice->update([
            'patient_id' => $request->input('patient_id'),
            'subtotal' => $request->input('subtotal'),
            'due' => $request->input('subtotal'),
            'expiration_date' => $request->input('expiration_date'),
            'user_id' => Auth::user()->id, // Update the user if necessary
        ]);

        $existingTestIds = $invoice->item->pluck('medical_test_id')->toArray(); // Get current invoice items

        $newTestIds = array_column($request->input('tests', []), 'medical_test_id'); // Get new tests to update

        // Loop through the tests from the request and update or create
        foreach ($request->input('tests', []) as $test) {
            $invoice->item()->updateOrCreate(
                ['invoice_id' => $invoice->id, 'medical_test_id' => $test['medical_test_id']], 
                [
                    'price' => $test['price'],
                    'quantity' => $test['quantity'],
                    'total' => $test['total'],
                ]
            );
        }

        // Delete old items that are no longer in the request
        $invoice->item()->whereNotIn('medical_test_id', $newTestIds)->delete();

        return redirect()->route('invoice.index')->with('success', 'Invoice updated successfully!');
    }

    public function print($id){
        $invoice = Invoice::with(['item','patient.medical'])->where('id',$id)->first();
        return view('backend.invoice.pint', compact('invoice'));
    }
    public function destroy($id){
        Invoice::where('id',$id)->delete();
        return redirect()->route('invoice.index');
    }
    public function dueInvoice(){
        $data = Invoice::query()
            ->with('patient') // Eager load the patient relationship
            ->where('due', '>', 0); // Only select invoices with a due amount

        if (Auth::user()->type == 0) {
            $data->whereHas('patient', function ($query) {
                $query->where('user_id', Auth::id());
            });
        }

        $data = $data->get(); // Retrieve the data

        return view('backend.invoice.due-invoice', compact('data'));
    }
    public function paidInvoice(){
        $data = Invoice::query()
            ->with('patient') // Eager load the patient relationship
            ->where('due', 0); // Only select invoices with a due amount

        if (Auth::user()->type == 0) {
            $data->whereHas('patient', function ($query) {
                $query->where('user_id', Auth::id());
            });
        }
        

        $data = $data->get(); // Retrieve the data

        return view('backend.invoice.due-invoice', compact('data'));
    }
}
