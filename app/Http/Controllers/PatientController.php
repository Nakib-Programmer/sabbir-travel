<?php

namespace App\Http\Controllers;

use App\Models\Medical;
use App\Models\Patient;
use App\Models\Reference;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class PatientController extends Controller
{
    public function index(){
        $data = Patient::with(['reference','medical','invoices','user']);

        if (Auth::user()->type == 0) {
            $data->where('user_id', Auth::id());
        }
        $data = $data->get()->toArray();
        return view('backend.patient.index',compact('data'));
    }

    public function create(){
        $ref = Reference::where('user_id', Auth::id())->get();
        $medi = Medical::get();
        return view('backend.patient.create',compact('ref','medi'));
    }
    public function store(Request $request){
        $rules = [
            'name' => 'required|string|max:255',
            'serial_no' => 'required',
            'passport' => 'required|unique:patients,passport',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        try {
            $data = $request->only(['name', 'passport', 'date', 'ref_id', 'medical_id','serial_no','country']);
            if($request->hasFile('slip')){
                $file = $request->file('slip');
                $invoice_file = time() . '_'. $file->getClientOriginalName();
                $file->move(public_path('invoice'),$invoice_file);
                $data['slip'] = $invoice_file;
            }
            if($request->hasFile('passport_image')){
                $file = $request->file('passport_image');
                $invoice_file = time() . '_'. $file->getClientOriginalName();
                $file->move(public_path('passportImage'),$invoice_file);
                $data['passport_image'] = $invoice_file;
            }
            $data['user_id'] = Auth::user()->id;
            Patient::create($data);
            return redirect()->route('patient.index');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while storing data: ' . $e->getMessage())
                ->withInput();
        }
    }
    public function edit($id){
        $patient = Patient::where('id',$id)->first();
        $medi = Medical::get();
        $ref = Reference::where('user_id', Auth::id())->get();
        return view('backend.patient.edit',compact('patient','medi','ref'));
    }
    public function show($id){
        $patient = Patient::where('id',$id)->first();
        $medi = Medical::get();
        $ref = Reference::get();
        return view('backend.patient.show',compact('patient','medi','ref'));
    }
    public function destroy($id){
        Patient::where('id',$id)->delete();
        return redirect()->route('patient.index');
    }
    public function update(Request $request, $id){
        $rules = [
            'name' => 'required|string|max:255',
            'serial_no' => 'required',
            'passport' => 'required|unique:patients,passport,' . $id, // Allow same passport for current record
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $patient = Patient::findOrFail($id); // Retrieve the patient

            $data = $request->only(['name', 'passport', 'date', 'ref_id', 'medical_id', 'serial_no', 'country']);

            // Handle new slip upload and delete old one
            if ($request->hasFile('slip')) {
                if ($patient->slip && file_exists(public_path('invoice/' . $patient->slip))) {
                    unlink(public_path('invoice/' . $patient->slip)); // Delete old slip
                }
                $file = $request->file('slip');
                $invoice_file = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('invoice'), $invoice_file);
                $data['slip'] = $invoice_file;
            }

            // Handle new passport image upload and delete old one
            if ($request->hasFile('passport_image')) {
                if ($patient->passport_image && file_exists(public_path('passportImage/' . $patient->passport_image))) {
                    unlink(public_path('passportImage/' . $patient->passport_image)); // Delete old passport image
                }
                $file = $request->file('passport_image');
                $passport_file = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('passportImage'), $passport_file);
                $data['passport_image'] = $passport_file;
            }

            $data['user_id'] = Auth::user()->id;

            $patient->update($data); // Update patient record

            return redirect()->route('patient.index')->with('success', 'Patient updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while updating data: ' . $e->getMessage())
                ->withInput();
        }
    }
    public function test() {
          $client = new Client([
        'base_uri' => 'https://wafid.com',
        'headers' => [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/141.0.0.0 Safari/537.36',
        ]
    ]);

    // Step 1: Get CSRF token
    $res = $client->get('/search-slip/');
    $html = (string) $res->getBody();

    preg_match('/name="csrfmiddlewaretoken" value="(.*?)"/', $html, $matches);
    $csrfToken = $matches[1] ?? null;

    if (!$csrfToken) {
        return response()->json(['error' => 'CSRF token not found']);
    }

    // Step 2: Submit form
    $response = $client->post('/search-slip/', [
        'form_params' => [
            'csrfmiddlewaretoken' => $csrfToken,
            'passport' => 'A00894589',
            'nationality' => '15', // Bangladesh value
            'captcha' => 'DUMMY_OR_HANDLE_RECAPTCHA', // Optional, Google reCAPTCHA token
        ],
        'headers' => [
            'Referer' => 'https://wafid.com/search-slip/',
        ]
    ]);

    $body = (string) $response->getBody();

    // Step 3: Return the result
    return $body;
}
    public function withoutInvoce(){
        $user = Auth::user(); 

        $query = Patient::with(['reference', 'medical','user'])
        ->doesntHave('invoices'); 
        if ($user->type !== 1) {
            $query->where('user_id', $user->id);
        }

        $data = $query->get();

        return view('backend.patient.withoutinvoice', compact('data'));
    }
}
