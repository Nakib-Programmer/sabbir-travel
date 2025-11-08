import sys
import io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

import sys
import json
import time
import os
from bs4 import BeautifulSoup
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import Select, WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options

# === CONFIG ===
WAFID_URL = "https://wafid.com/search-slip/"
BANGLADESH_VALUE = "15"

# Local ChromeDriver path
BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
CHROMEDRIVER_PATH = os.path.join(BASE_DIR, "drivers", "chromedriver.exe")


def wait_for_captcha_token(driver, input_selector="#id_captcha", timeout=120, poll=0.5):
    """Wait for CAPTCHA token (Google reCAPTCHA v3)."""
    elapsed = 0.0
    try:
        WebDriverWait(driver, 10).until(
            EC.presence_of_element_located((By.CSS_SELECTOR, input_selector))
        )
    except Exception:
        return False

    while elapsed < timeout:
        val = driver.execute_script(
            "return document.querySelector(arguments[0])?.value || '';",
            input_selector,
        )
        if val and len(val) > 20:
            return True
        time.sleep(poll)
        elapsed += poll
    return False


def parse_wafid_html(html):
    """Parse the Wafid HTML response into structured JSON."""
    soup = BeautifulSoup(html, "html.parser")
    data = {}

    # ----- Appointment Slip -----
    slip = {}
    slip_table = soup.select_one(".slip-table")
    if slip_table:
        for tr in slip_table.select("tr"):
            tds = tr.find_all("td")
            if len(tds) == 2:
                slip[tds[0].get_text(strip=True)] = tds[1].get_text(strip=True)
            elif len(tds) == 4:
                slip[tds[0].get_text(strip=True)] = tds[1].get_text(strip=True)
                slip[tds[2].get_text(strip=True)] = tds[3].get_text(strip=True)
        data["appointment_slip"] = slip

    # ----- Medical Center Info -----
    center = {}
    mc_table = soup.select_one(".mc-table")
    if mc_table:
        info_list = []
        hours_list = []
        for tr in mc_table.select("tr"):
            tds = tr.find_all("td")
            if not tds:
                continue
            if len(tds) <= 2 and "Working hours" not in tds[0].get_text():
                text = " ".join(td.get_text(" ", strip=True) for td in tds)
                info_list.append(text)
            elif len(tds) >= 2 and "AM" in tds[1].get_text():
                hours_list.append({
                    "day": tds[0].get_text(strip=True),
                    "time": tds[1].get_text(strip=True),
                })

        barcode = mc_table.select_one(".barcode img")
        barcode_url = barcode["src"] if barcode and barcode.has_attr("src") else None
        generated = mc_table.select_one(".slip-dates__generated")
        valid_till = mc_table.select_one(".slip-dates__valid_till")

        center["info"] = info_list
        center["hours"] = hours_list
        center["barcode"] = barcode_url
        center["generated_date"] = generated.get_text(strip=True) if generated else None
        if valid_till:
            center["valid_till"] = (
                valid_till.get_text(strip=True)
                .replace("Slip is valid only till", "")
                .strip()
            )

        data["medical_center"] = center

    return data


def run(passport, headless=True):
    """Main scraping function with Winsock and Chrome fixes."""
    import os

    # 🩹 Fix common Windows socket issues (WinError 10106)
    os.environ["NO_PROXY"] = "*"
    os.environ["WDM_LOCAL"] = "1"
    os.environ["WDM_SSL_VERIFY"] = "0"
    os.environ["WDM_LOG_LEVEL"] = "0"

    opts = Options()
    if headless:
        opts.add_argument("--headless=new")
    opts.add_argument("--disable-gpu")
    opts.add_argument("--no-sandbox")
    opts.add_argument("--disable-dev-shm-usage")
    opts.add_argument("--disable-blink-features=AutomationControlled")
    opts.add_argument("--disable-features=NetworkService")
    opts.add_argument("--disable-features=NetworkServiceInProcess")
    opts.add_argument("--remote-debugging-port=0")
    opts.add_argument("--disable-extensions")
    opts.add_argument("--start-maximized")
    opts.add_argument("--log-level=3")
    opts.add_experimental_option("excludeSwitches", ["enable-automation"])
    opts.add_experimental_option("useAutomationExtension", False)

    service = Service(CHROMEDRIVER_PATH)

    # Start ChromeDriver
    driver = webdriver.Chrome(service=service, options=opts)

    try:
        driver.get(WAFID_URL)
        WebDriverWait(driver, 20).until(
            EC.presence_of_element_located((By.ID, "id_passport"))
        )
        driver.find_element(By.ID, "id_passport").send_keys(passport)

        # Select nationality
        Select(driver.find_element(By.ID, "id_nationality")).select_by_value(BANGLADESH_VALUE)

        # Wait for captcha
        token_ready = wait_for_captcha_token(driver, "#id_captcha", 60)
        if not token_ready:
            time.sleep(3)

        # Submit form
        driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()

        # Wait for result
        WebDriverWait(driver, 45).until(
            EC.presence_of_element_located((By.CSS_SELECTOR, ".slip-table"))
        )
        time.sleep(2)

        html = driver.page_source
        parsed = parse_wafid_html(html)
        return {"success": True, "passport": passport, "data": parsed, "url": driver.current_url}

    except Exception as e:
        return {"success": False, "error": str(e)}
    finally:
        try:
            driver.quit()
        except:
            pass


if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({"success": False, "error": "passport_required"}))
        sys.exit(1)

    passport = sys.argv[1].strip()
    result = run(passport, headless=True)
    print(json.dumps(result, ensure_ascii=False, indent=2))
