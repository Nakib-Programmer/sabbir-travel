<?php

namespace App\Http\Controllers;

use App\Models\WafidAppointment;
use App\Models\WafidMedicalData;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Mpdf\Mpdf;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class WaffedController extends Controller
{
    public function index(){
        $fit = WafidMedicalData::latest()->get()->toArray();
        return view('backend.wafid.index',compact('fit'));
    }
    public function create(){
        return view('backend.wafid.create');
    }
    public function pdf1(){
        return view('backend.wafid.pdf1');
    }
 public function pdf($id)
{
    $medical = WafidMedicalData::with('ghc')->findOrfail($id);
    $slip = WafidAppointment::where('passport',$medical->passport)->first();
    $exp = Carbon::createFromFormat('d/m/Y', $medical->medical_examination_date)
            ->addMonths(2)
            ->subDay()
            ->format('d/m/Y');

$passportExpiry = Carbon::createFromFormat('d/M/Y', $slip->passport_expiry_date)
                       ->format('d/m/Y');
     return view('backend.wafid.pdf',compact('slip','medical','exp','passportExpiry'));


}

 public function fetch1()
{
    $passport = 'A00894589';

    // 🐍 Use full system Python (recommended)
    // $python = 'C:\Users\User\AppData\Local\Programs\Python\Python313\python.exe';
    $python = 'C:\Users\User\AppData\Local\Programs\Python\Python313\python.exe';

    // Or use virtual environment Python if needed:
    // $python = base_path('venv/Scripts/python.exe');

    $script = base_path('python_scripts/wafid_scraper.py');

    // ✅ Pass full environment to avoid Winsock issue
    $env = [
        'PATH' => getenv('PATH'),
        'SystemRoot' => getenv('SystemRoot'),
        'WINDIR' => getenv('WINDIR'),
        'TEMP' => getenv('TEMP'),
        'TMP' => getenv('TMP'),
    ];

    // 🧠 Create and run process
    $env['PYTHONIOENCODING'] = 'utf-8';
    $process = new Process([$python, $script, $passport], base_path(), $env);
    $process->setTimeout(180);

    $process->run();

    // 🚨 Handle errors
    if (!$process->isSuccessful()) {
        return response()->json([
            'success' => false,
            'error' => $process->getErrorOutput() ?: $process->getOutput(),
        ], 500);
    }

    // 🧩 Parse output
    $output = json_decode($process->getOutput(), true);

    return response()->json($output);
}
    public function fetch($passport)
    {

        // $python = 'C:\Users\Nakib\AppData\Local\Programs\Python\Python313\python.exe';
        $python = 'C:\Users\User\AppData\Local\Programs\Python\Python313\python.exe';
        $script = base_path('python_scripts/wafid_scraper.py');

        $env = [
            'PATH' => getenv('PATH'),
            'SystemRoot' => getenv('SystemRoot'),
            'WINDIR' => getenv('WINDIR'),
            'TEMP' => getenv('TEMP'),
            'TMP' => getenv('TMP'),
            'PYTHONIOENCODING' => 'utf-8',
        ];

        $process = new Process([$python, $script, $passport], base_path(), $env);
        $process->setTimeout(180);
        $process->run();

        if (!$process->isSuccessful()) {
            Log::error('Python scraper failed', [
                'error' => $process->getErrorOutput(),
            ]);

            return response()->json([
                'success' => false,
                'error' => $process->getErrorOutput() ?: $process->getOutput(),
            ], 500);
        }

        $data = json_decode($process->getOutput(), true);

        if (empty($data['success']) || !$data['success']) {
            return response()->json([
                'success' => false,
                'error' => $data['error'] ?? 'Unknown Python error',
            ], 500);
        }

        // ✅ Parse slip + center data
        $slip = $data['data']['appointment_slip'] ?? [];
        $center = $data['data']['medical_center'] ?? [];
        $info = $center['info'] ?? [];

        // ✅ Store or update record
        $record = WafidAppointment::updateOrCreate(
            ['passport' => $passport],
            [
                'merchant_reference'     => $slip['Merchant reference №'] ?? null,
                'gcc_slip_no'            => $slip['GCC Slip №'] ?? null,
                'first_name'             => $slip['First name'] ?? null,
                'last_name'              => $slip['Last name'] ?? null,
                'nationality'            => $slip['Nationality'] ?? 'Bangladesh',
                'national_id'            => $slip['National ID'] ?? null,
                'gender'                 => $slip['Gender'] ?? null,
                'marital_status'         => $slip['Marital status'] ?? null,
                'country_traveling_to'   => $slip['Country traveling to'] ?? null,
                'date_of_birth'          => $slip['Date of Birth'] ?? null,
                'passport_expiry_date'   => $slip['Passport expiry date'] ?? null,
                'passport_issue_place'   => $slip['Passport issues place'] ?? null,
                'passport_issue_date'    => $slip['Passport issue date'] ?? null,
                'applied_position'       => $slip['Applied position'] ?? null,
                'payment_status'         => $slip['Payment status'] ?? null,
                'amount'                 => $slip['Amount'] ?? null,
                'appointment_type'       => $slip['Appointment Type'] ?? null,
                'medical_center_name'    => $info[0] ?? null,
                'medical_center_address' => $info[1] ?? null,
                'medical_center_phone'   => $info[2] ?? null,
                'medical_center_email'   => $info[3] ?? null,
                'medical_center_website' => $info[4] ?? null,
                'barcode'                => $center['barcode'] ?? null,
                'generated_date'         => $center['generated_date'] ?? null,
                'valid_till'             => $center['valid_till'] ?? null,
            ]
        );

        // 🎯 Final JSON Response
        return response()->json([
            'success' => true,
            'message' => 'Data fetched and stored successfully.',
            'record' => $record,
            'url' => $data['url'] ?? null,
        ]);
    }

    public function fetchMedicalStatus(Request $request)
    {
        $passport = $request->input('passport');
        // $python = 'C:\Users\Nakib\AppData\Local\Programs\Python\Python313\python.exe';
         $python = 'C:\Users\User\AppData\Local\Programs\Python\Python313\python.exe';
        $script = base_path('python_scripts/wafid_medical_status.py');

        $env = [
            'PATH' => getenv('PATH'),
            'SystemRoot' => getenv('SystemRoot'),
            'WINDIR' => getenv('WINDIR'),
            'TEMP' => getenv('TEMP'),
            'TMP' => getenv('TMP'),
            'PYTHONIOENCODING' => 'utf-8',
        ];

        $process = new Process([$python, $script, $passport], base_path(), $env);
        $process->setTimeout(180);
        $process->run();

        if (!$process->isSuccessful()) {
            return response()->json([
                'success' => false,
                'error' => $process->getErrorOutput() ?: $process->getOutput(),
            ], 500);
        }

        $output = json_decode($process->getOutput(), true);

        if (!isset($output['success']) || !$output['success'] || empty($output['data'])) {
            return response()->json([
                'success' => false,
                'error' => $output['error'] ?? 'No data found or invalid response',
            ], 400);
        }

        $data = $output['data'];
        
        if($data['status'] == 'Fit'){
            $this->fetch($passport);
        }

        // 🧠 Update existing record or create new one
        $record = WafidMedicalData::updateOrCreate(
            ['passport' => $data['passport'] ?? $passport],
            [
                'status' => $data['status'] ?? null,
                'pdf_url' => $data['pdf_url'] ?? null,
                'print_url' => $data['print_url'] ?? null,
                'photo' => $data['photo'] ?? null,
                'name' => $data['name'] ?? null,
                'phone' => $data['phone'] ?? null,
                'gender' => $data['gender'] ?? null,
                'age' => $data['age'] ?? null,
                'passport_expiry_on' => $data['passport_expiry_on'] ?? null,
                'nationality_name' => $data['nationality__name'] ?? null,
                'applied_position_name' => $data['applied_position__name'] ?? null,
                'marital_status' => $data['marital_status'] ?? null,
                'traveled_country_name' => $data['traveled_country__name'] ?? null,
                'height' => $data['height'] ?? null,
                'medical_center' => $data['medical_center'] ?? null,
                'weight' => $data['weight'] ?? null,
                'medical_examination_date' => $data['medical_examination_date'] ?? null,
                'BMI' => $data['BMI'] ?? null,
                'blood_group' => $data['blood_group'] ?? null,
            ]
        );

        if ($data['status'] == 'Fit') {
            return redirect()->route('wafid-slip.index')->with('open_print_id', $record->id);
        } else {
            return redirect()->route('wafid-slip.index');
        }
    }









        private $pythonPath;
    private $scriptPath;

    public function __construct()
    {
        $this->pythonPath = 'C:\Users\User\AppData\Local\Programs\Python\Python313\python.exe';
        $this->scriptPath = base_path('python_scripts/booking.py');
    }

    /**
     * Get all booking data
     */
    public function booking()
    {
        try {
            $result = $this->runPythonScript();

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'error' => $result['error']
                ], 500);
            }

            return response()->json($result['data']);

        } catch (\Exception $e) {
            Log::error('Booking scraper error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search medical centers by location
     */
    public function searchMedicalCenters(Request $request)
    {
        $request->validate([
            'country' => 'required|string|max:10',
            'city' => 'required|string|max:10',
            'country_traveling_to' => 'required|string|max:10'
        ]);

        try {
            $result = $this->runPythonScript([
                'search',
                $request->country,
                $request->city,
                $request->country_traveling_to
            ]);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'error' => $result['error']
                ], 500);
            }

            return response()->json($result['data']);

        } catch (\Exception $e) {
            Log::error('Medical center search error', [
                'error' => $e->getMessage(),
                'params' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cities by country
     */
    public function getCitiesByCountry(Request $request)
    {
        $request->validate([
            'country' => 'required|string|max:10'
        ]);

        try {
            $result = $this->runPythonScript([
                'cities',
                $request->country
            ]);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'error' => $result['error']
                ], 500);
            }

            return response()->json([
                'success' => true,
                'cities' => $result['data']['cities'] ?? []
            ]);

        } catch (\Exception $e) {
            Log::error('Get cities error', [
                'error' => $e->getMessage(),
                'country' => $request->country
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get working hours for a medical center
     */
    public function getWorkingHours(Request $request)
    {
        $request->validate([
            'medical_center_id' => 'required|string'
        ]);

        try {
            $result = $this->runPythonScript([
                'working_hours',
                $request->medical_center_id
            ]);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'error' => $result['error']
                ], 500);
            }

            return response()->json([
                'success' => true,
                'working_hours' => $result['data']['working_hours'] ?? null
            ]);

        } catch (\Exception $e) {
            Log::error('Get working hours error', [
                'error' => $e->getMessage(),
                'mc_id' => $request->medical_center_id
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Run Python script with arguments
     */
    private function runPythonScript(array $args = [])
    {
        // Build command
        $command = array_merge([$this->pythonPath, $this->scriptPath], $args);

        // Set environment variables
        $env = [
            'PATH' => getenv('PATH'),
            'SystemRoot' => getenv('SystemRoot'),
            'WINDIR' => getenv('WINDIR'),
            'TEMP' => getenv('TEMP'),
            'TMP' => getenv('TMP'),
            'PYTHONIOENCODING' => 'utf-8',
        ];

        // Create and run process
        $process = new Process($command, base_path(), $env);
        $process->setTimeout(180);

        try {
            $process->mustRun();

            $output = $process->getOutput();
            $data = json_decode($output, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response from Python script: ' . json_last_error_msg());
            }

            return [
                'success' => $data['success'] ?? false,
                'data' => $data,
                'error' => $data['error'] ?? null
            ];

        } catch (ProcessFailedException $e) {
            Log::error('Python process failed', [
                'command' => $command,
                'output' => $process->getOutput(),
                'error' => $process->getErrorOutput()
            ]);

            return [
                'success' => false,
                'data' => null,
                'error' => $process->getErrorOutput() ?: $process->getOutput()
            ];
        }
    }

    /**
     * Test endpoint to verify Python integration
     */
    public function testConnection()
    {
        try {
            $result = $this->runPythonScript();

            return response()->json([
                'success' => true,
                'message' => 'Python integration working!',
                'metadata' => $result['data']['metadata'] ?? null
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
