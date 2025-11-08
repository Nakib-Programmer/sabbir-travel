// File: wafid-scraper.cjs (or .js)

// Import built-in Node modules for path handling
const path = require('path');
const crypto = require('crypto');
const https = require('https'); // Required for API calls
const querystring = require('querystring'); // Required for URL encoding

// Use puppeteer-extra and the stealth plugin for stability
const puppeteer = require('puppeteer-extra');
const StealthPlugin = require('puppeteer-extra-plugin-stealth');

// Enable the stealth plugin
puppeteer.use(StealthPlugin());

// --- CONFIGURATION ---
const API_KEY = 'YOUR_2CAPTCHA_API_KEY'; // <<< !!! REPLACE THIS WITH YOUR ACTUAL 2CAPTCHA API KEY !!!
const API_URL = 'https://2captcha.com/in.php';
const RESULT_URL = 'https://2captcha.com/res.php';
const RECAPTCHA_SITE_KEY = '6LflPAwnAAAAAL2wBGi6tSyGUyj-xFvftINOR9xp'; // Confirmed from HTML
const RECAPTCHA_ACTION = 'form'; // Confirmed from HTML

// Helper function to output JSON and exit
const sendResponse = (success, data, message = '') => {
    // IMPORTANT: All output MUST be a single JSON string sent to stdout.
    console.log(JSON.stringify({ success, data, message }));
    process.exit(success ? 0 : 1);
};

/**
 * Sends ReCAPTCHA details to 2Captcha and polls for the solved token (for v3).
 * @param {string} siteKey - The data-sitekey from the page.
 * @param {string} pageUrl - The URL of the page containing the CAPTCHA.
 * @returns {Promise<string>} The solved ReCAPTCHA response token.
 */
function solveRecaptchaV3(siteKey, pageUrl) {
    if (API_KEY === 'YOUR_2CAPTCHA_API_KEY') {
        return Promise.reject(new Error('2Captcha API key is not set. Please replace the placeholder in the script.'));
    }

    // 1. Send the CAPTCHA details to 2Captcha for ReCaptchaV3
    const submitQuery = querystring.stringify({
        key: API_KEY,
        method: 'userrecaptcha',
        googlekey: siteKey,
        pageurl: pageUrl,
        version: 'v3', // Specify V3
        action: RECAPTCHA_ACTION, // Specify the action
        min_score: 0.1, // Specify the minimum score required
        json: 1, // Request JSON response
    });

    return new Promise((resolve, reject) => {
        https.get(`${API_URL}?${submitQuery}`, (res) => {
            let data = '';
            res.on('data', (chunk) => { data += chunk; });
            res.on('end', () => {
                try {
                    const jsonResponse = JSON.parse(data);
                    if (jsonResponse.status === 1) {
                        const requestId = jsonResponse.request;
                        
                        // 2. Start polling for the result
                        const checkResult = () => {
                            const resultQuery = querystring.stringify({
                                key: API_KEY,
                                action: 'get',
                                id: requestId,
                                json: 1,
                            });

                            https.get(`${RESULT_URL}?${resultQuery}`, (res) => {
                                let resultData = '';
                                res.on('data', (chunk) => { resultData += chunk; });
                                res.on('end', () => {
                                    try {
                                        const resultJson = JSON.parse(resultData);
                                        if (resultJson.status === 1) {
                                            // CAPTCHA solved!
                                            resolve(resultJson.request);
                                        } else if (resultJson.request === 'CAPCHA_NOT_READY' || resultJson.request === 'NO_SLOT_AVAILABLE') {
                                            // Not ready yet, retry in 5 seconds
                                            setTimeout(checkResult, 5000);
                                        } else {
                                            // Error from 2Captcha
                                            reject(new Error(`2Captcha error: ${resultJson.request}`));
                                        }
                                    } catch (e) {
                                        reject(new Error(`Failed to parse 2Captcha result JSON: ${e.message}`));
                                    }
                                });
                            }).on('error', reject);
                        };

                        // Start polling after a short delay
                        setTimeout(checkResult, 10000); // Initial delay of 10 seconds (recommended by 2Captcha)

                    } else {
                        // Error sending CAPTCHA
                        reject(new Error(`2Captcha submission failed: ${jsonResponse.request}`));
                    }
                } catch (e) {
                    reject(new Error(`Failed to parse 2Captcha submission JSON: ${e.message}`));
                }
            });
        }).on('error', reject);
    });
}


(async () => {
    // Retrieve arguments passed from PHP
    const [_, __, passport, nationality] = process.argv;

    if (!passport || !nationality) {
        sendResponse(false, null, 'Passport number and nationality are required.');
        return;
    }

    let browser = null;

    // Generate a unique directory name for this specific run
    const tempBasePath = path.join(process.cwd(), 'puppeteer_chrome_data');
    const uniqueId = crypto.randomBytes(8).toString('hex');
    const localProfileDir = path.join(tempBasePath, `profile_${uniqueId}`);

    // Variable to hold the solved token
    let captchaToken = null;

    try {
        
        // 1. Launch the browser using puppeteer-extra
        browser = await puppeteer.launch({
            headless: 'new',
            args: [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
                
                '--disable-gpu', 
                '--no-zygote', 
                '--disable-breakpad', 
                '--mute-audio',
                
                '--no-default-browser-check',           
                '--no-first-run',                       
                '--disable-background-networking',      
                '--disable-dev-tools', 
            ],
            userDataDir: localProfileDir,
        });
        const page = await browser.newPage();

        await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');

        // 2. Go to the page
        await page.goto('https://wafid.com/search-slip/', {
            waitUntil: 'networkidle0',
            timeout: 30000
        });

        // 3. --- SOLVE CAPTCHA ---
        console.log('Solving ReCAPTCHA v3... This may take 15-30 seconds.');
        // Pass the hardcoded site key from the HTML you provided
        captchaToken = await solveRecaptchaV3(RECAPTCHA_SITE_KEY, page.url());
        console.log('ReCAPTCHA solved. Token retrieved.');

        // 4. Fill the form fields
        await page.type('#id_passport', passport); // Passport input field
        await page.select('#id_nationality', nationality); // Nationality dropdown

        // 5. Inject the CSRF token and the solved CAPTCHA token into the hidden fields
        // The CSRF token must still be extracted dynamically
        const csrfToken = await page.evaluate(() => document.querySelector('input[name="csrfmiddlewaretoken"]').value);

        if (!csrfToken) {
            sendResponse(false, null, 'Could not find CSRF token on the page.');
            return;
        }

        await page.evaluate((csrf, captcha) => {
            // Set CSRF Token
            document.querySelector('input[name="csrfmiddlewaretoken"]').value = csrf;
            
            // Set ReCAPTCHA Token in the correct hidden field (name="captcha")
            const recaptchaResponseInput = document.querySelector('input[name="captcha"][type="hidden"]');
            
            if (recaptchaResponseInput) {
                recaptchaResponseInput.value = captcha;
            } else {
                // Fallback if the hidden input isn't found (though it should be based on the HTML provided)
                console.error('ReCAPTCHA hidden input (name="captcha") not found!');
            }
        }, csrfToken, captchaToken);


        // 6. Submit the form by clicking the search button
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 30000 }),
            page.click('button[type="submit"]')
        ]);

        // 7. --- SCRAPE RESULTS PAGE ---
        const resultData = await page.evaluate(() => {
            // Check for success message or data table
            const dataTable = document.querySelector('.appointment-details table tbody');
            if (dataTable) {
                // Extract data from the table
                const rows = dataTable.querySelectorAll('tr');
                const data = {};
                rows.forEach(row => {
                    const key = row.querySelector('th')?.textContent.trim().replace(/:$/, '');
                    const value = row.querySelector('td')?.textContent.trim();
                    if (key) {
                        data[key] = value;
                    }
                });
                return { 
                    success: true, 
                    data: data, 
                    message: 'Appointment found successfully.' 
                };
            }

            // Check for error/not found message
            const errorMessageElement = document.querySelector('.alert.alert-danger');
            let errorMessage = null;

            if (errorMessageElement) {
                errorMessage = errorMessageElement.textContent.trim() || errorMessageElement.innerText.trim();
            } else {
                // Check for a generic "no results" message
                const allPElements = Array.from(document.querySelectorAll('p'));
                const noResultsElement = allPElements.find(p => p.textContent.includes('No Results Found'));
                if (noResultsElement) {
                     errorMessage = noResultsElement.textContent.trim();
                }
            }
            
            if (errorMessage) {
                return { 
                    success: false, 
                    data: null, 
                    message: errorMessage 
                };
            }

            // Fallback for unexpected page structure
            return { 
                success: false, 
                data: null, 
                message: 'Submitted form, but failed to parse expected results or error page structure.' 
            };
        });

        sendResponse(resultData.success, resultData.data, resultData.message);

    } catch (err) {
        // Log the detailed error using the SUCCESS=false JSON structure
        sendResponse(false, null, `Scraping failed during launch or execution: ${err.message}.`);

    } finally {
        if (browser) {
            try {
                await browser.close();
            } catch (e) {
                // Ignore close error
            }
        }
    }
})();
