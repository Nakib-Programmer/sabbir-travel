<?php

namespace App\Jobs;

use Laravel\Dusk\Browser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchWafidSlipJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const SEARCH_URL = 'https://wafid.com/search-slip/';

    public function __construct(private string $passport, private string $nationality = '15')
    {
    }

    public function handle()
    {
        Browser::browse(function (Browser $browser) {
            $browser->visit(self::SEARCH_URL)
                    ->waitFor('input[name="passport"]', 10)
                    ->type('passport', $this->passport)
                    ->select('nationality', $this->nationality)
                    ->pause(2000)
                    ->waitFor('button[type="submit"]', 5)
                    ->press('Search')
                    ->pause(5000)
                    ->waitFor('body', 10);

            $pageText = $browser->text('body');

            if (str_contains($pageText, 'Medical Examination Appointment Slip') || str_contains($pageText, 'Appointment Slip')) {
                $result = $this->parseAppointmentSlip($browser);
            } else {
                $result = [
                    'success' => false,
                    'message' => 'No appointment slip found for the provided details.',
                ];
            }
            Log::info('Wafid slip result:', $result);

            // TODO: Save $result in DB for later retrieval
            // Example:
            // WafidSlipResult::create([
            //     'passport' => $this->passport,
            //     'nationality' => $this->nationality,
            //     'result' => json_encode($result),
            // ]);
        });
    }

    private function parseAppointmentSlip(Browser $browser): array
    {
        try {
            $html = $browser->driver->getPageSource();

            return [
                'success' => true,
                'data' => [
                    'merchant_reference' => $this->extractTextByLabel($browser, 'Merchant reference'),
                    'slip_no' => $this->extractTextByLabel($browser, 'Slip №'),
                    'first_name' => $this->extractTextByLabel($browser, 'First name'),
                    'last_name' => $this->extractTextByLabel($browser, 'Last name'),
                    'nationality' => $this->extractTextByLabel($browser, 'Nationality'),
                    'national_id' => $this->extractTextByLabel($browser, 'National ID'),
                    'gender' => $this->extractTextByLabel($browser, 'Gender'),
                    'marital_status' => $this->extractTextByLabel($browser, 'Marital status'),
                    'country_traveling_to' => $this->extractTextByLabel($browser, 'Country traveling to'),
                    'date_of_birth' => $this->extractTextByLabel($browser, 'Date of Birth'),
                    'passport_no' => $this->extractTextByLabel($browser, 'Passport №'),
                    'passport_expiry_date' => $this->extractTextByLabel($browser, 'Passport expiry date'),
                    'passport_issue_place' => $this->extractTextByLabel($browser, 'Passport issues place'),
                    'passport_issue_date' => $this->extractTextByLabel($browser, 'Passport issue date'),
                    'applied_position' => $this->extractTextByLabel($browser, 'Applied position'),
                    'payment_status' => $this->extractTextByLabel($browser, 'Payment status'),
                    'amount' => $this->extractTextByLabel($browser, 'Amount'),
                    'appointment_type' => $this->extractTextByLabel($browser, 'Appointment Type'),
                    'generated_date' => $this->extractTextByLabel($browser, 'Generated data'),
                    'medical_center' => $this->extractMedicalCenterInfo($browser),
                ],
                'raw_html' => $html,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to parse appointment slip: ' . $e->getMessage(),
            ];
        }
    }

    private function extractTextByLabel(Browser $browser, string $label): ?string
    {
        try {
            $script = "
                var result = '';
                var walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, null, false);
                var node;
                while(node = walker.nextNode()) {
                    if (node.textContent.includes('{$label}')) {
                        var text = node.parentElement.textContent;
                        var parts = text.split('{$label}');
                        if (parts.length > 1) {
                            result = parts[1].trim().split('\\n')[0].trim();
                            break;
                        }
                    }
                }
                return result;
            ";
            $value = $browser->driver->executeScript($script);
            return $value ?: null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function extractMedicalCenterInfo(Browser $browser): array
    {
        try {
            $script = "
                var info = { name: '', address: '', phone: '', email: '', website: '', working_hours: {} };
                var text = document.body.textContent;
                var match = text.match(/Medical center information([\\s\\S]*?)(?=Generated data|$)/);
                if(match){
                    var section = match[1];
                    var phoneMatch = section.match(/\\+\\d{10,15}/);
                    if(phoneMatch) info.phone = phoneMatch[0];
                    var emailMatch = section.match(/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}/);
                    if(emailMatch) info.email = emailMatch[0];
                    var websiteMatch = section.match(/https?:\\/\\/[^\\s]+/);
                    if(websiteMatch) info.website = websiteMatch[0];
                    var days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                    days.forEach(function(day){
                        var regex = new RegExp(day+'([^A-Z]+)','i');
                        var m = section.match(regex);
                        if(m) info.working_hours[day]=m[1].trim();
                    });
                    var lines = section.split('\\n').filter(l=>l.trim());
                    if(lines.length>0){ info.name = lines[0].trim(); 
                        if(lines.length>1){ 
                            var addr=[]; 
                            for(var i=1;i<lines.length;i++){
                                if(lines[i].includes('+')||lines[i].includes('@')||lines[i].includes('http')) break;
                                addr.push(lines[i].trim());
                            }
                            info.address = addr.join(', ');
                        }
                    }
                }
                return info;
            ";
            return $browser->driver->executeScript($script);
        } catch (\Exception $e) {
            return ['name'=>null,'address'=>null,'phone'=>null,'email'=>null,'website'=>null,'working_hours'=>[]];
        }
    }
}
