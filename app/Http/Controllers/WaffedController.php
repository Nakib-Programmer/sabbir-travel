<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class WaffedController extends Controller
{
    // WARNING: In a real application, remove the Request parameter only if 
    // you are 100% sure you do not need it. I'm adding it back to be safe.
    public function searchSlip(Request $request = null)
    {
        // Path used in previous successful communication
        $nodeExecutablePath = 'C:\Program Files\nodejs\node.exe';

        // Using hardcoded test values
        $passport = 'A00894589'; 
        $nationality = '15'; 
        
        $cacheKey = 'wafid_' . md5($passport . $nationality);
        
        try {
            // 1. Check cache first
            if (Cache::has($cacheKey)) {
                $cachedData = Cache::get($cacheKey);
                return response()->json([
                    'success' => true,
                    'data' => $cachedData,
                    'message' => 'Appointment details retrieved successfully',
                    'cached' => true
                ]);
            }
            
            $scriptPath = base_path('wafid-scraper.cjs'); 

            $process = new Process([
                $nodeExecutablePath,
                $scriptPath,
                $passport,
                $nationality
            ]);

            $process->setWorkingDirectory(base_path());
            $process->setTimeout(60); 

            try {
                $process->run();
            } catch (\Exception $e) {
                // Catches failures BEFORE the process starts
                return response()->json([
                    'success' => false,
                    'message' => "Scraper process failed to start: {$e->getMessage()}",
                    'data' => null
                ], 500);
            }

            // ----------------------------------------------------------------------
            // THE CRITICAL FIX: Decode the output regardless of success status
            // because the error message (JSON) is always written to STDOUT.
            // ----------------------------------------------------------------------
            $output = $process->getOutput();
            $result = json_decode($output, true);

            // 2. Handle invalid JSON response (unexpected crash or partial output)
            if (!$result || !isset($result['success']) || !is_array($result)) {
                Log::error('Invalid JSON output from Puppeteer', ['output' => $output, 'exit_code' => $process->getExitCode()]);
                
                // If we get invalid JSON, but the process failed, return the raw error stream if available.
                $rawError = $process->getErrorOutput();
                $message = 'Invalid or empty JSON response from scraper.';
                if ($rawError) {
                    $message .= ' Raw Error Stream: ' . trim($rawError);
                }
                
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'data' => null
                ], 500);
            }
            
            // 3. Handle success=false from the JS script (this includes CAPTCHA and scraping errors)
            if (!$result['success']) {
                // This is where your CAPTCHA REQUIRED message will be handled
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Scraper failed: Unknown reason.',
                    'data' => null
                ]);
            }
            
            // 4. Handle successful scrape (found data)
            $appointmentData = $this->formatAppointmentData($result['data']);
            Cache::put($cacheKey, $appointmentData, 3600);
            
            return response()->json([
                'success' => true,
                'data' => $appointmentData,
                'message' => 'Appointment details retrieved successfully',
                'cached' => false
            ]);
            
        } catch (ProcessFailedException $e) {
            Log::error('ProcessFailedException in WaffedController', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error: Process execution failed.',
                'data' => null
            ], 500);
        } catch (\Exception $e) {
            Log::error('General Exception in WaffedController', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Format and clean appointment data
     * (This function is unchanged and works perfectly)
     */
    private function formatAppointmentData($rawData)
    {
        $formatted = [];
        
        $fieldMappings = [
            'name' => ['name', 'applicant name', 'candidate name', 'full name', 'worker name'],
            'passport' => ['passport', 'passport no', 'passport number', 'passport no.'],
            'appointment_date' => ['appointment date', 'date', 'appointment', 'scheduled date', 'test date'],
            'appointment_time' => ['appointment time', 'time', 'scheduled time', 'test time'],
            'center' => ['center', 'location', 'venue', 'center name', 'test center', 'medical center'],
            'reference' => ['reference', 'reference no', 'ref no', 'application no', 'slip no', 'registration no', 'wafid slip no'],
            'status' => ['status', 'appointment status'],
            'mobile' => ['mobile', 'phone', 'contact', 'mobile no', 'phone number'],
            'email' => ['email', 'email address'],
            'nationality' => ['nationality', 'country'],
            'profession' => ['profession', 'occupation', 'job title'],
            'medical_type' => ['medical type', 'examination type', 'test type'],
        ];
        
        foreach ($rawData as $key => $value) {
            if (empty($value) || $value === 'null' || $value === null) {
                continue;
            }
            
            $keyLower = strtolower(trim($key));
            $matched = false;
            
            foreach ($fieldMappings as $standardKey => $possibleKeys) {
                foreach ($possibleKeys as $possibleKey) {
                    if (stripos($keyLower, $possibleKey) !== false || $keyLower === $possibleKey) {
                        $formatted[$standardKey] = $this->cleanValue($value);
                        $matched = true;
                        break 2;
                    }
                }
            }
            
            if (!$matched) {
                $cleanKey = $this->slugify($key);
                if ($cleanKey && strlen($cleanKey) > 1) {
                    $formatted[$cleanKey] = $this->cleanValue($value);
                }
            }
        }
        
        if (empty($formatted)) {
            $formatted['raw_data'] = $rawData;
        }
        
        return $formatted;
    }

    /**
     * Clean and sanitize value
     * (This function is unchanged)
     */
    private function cleanValue($value)
    {
        if (!is_string($value)) {
            return $value;
        }
        
        $value = trim($value);
        $value = preg_replace('/\s+/', ' ', $value);
        $value = strip_tags($value);
        $value = preg_replace('/[\x00-\x1F\x7F]/u', '', $value);
        
        return $value;
    }

    /**
     * Convert key to slug format
     * (This function is unchanged)
     */
    private function slugify($text)
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '_', $text);
        $text = trim($text, '_');
        return $text;
    }

    /**
     * Clear cache for a specific passport
     * (This function is unchanged)
     */
    public function clearCache(Request $request)
    {
        $request->validate([
            'passport' => 'required|string|max:50',
        ]);
        
        $passport = $request->passport;
        $nationality = '15';
        $cacheKey = 'wafid_' . md5($passport . $nationality);
        
        Cache::forget($cacheKey);
        
        return response()->json([
            'success' => true,
            'message' => 'Cache cleared successfully'
        ]);
    }
}