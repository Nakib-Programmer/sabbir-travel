import sys
import io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

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
WAFID_URL = "https://wafid.com/medical-status-search/"
BANGLADESH_VALUE = "15"

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


def parse_medical_status_html(html):
    """Parse medical status modal card and extract all fields."""
    soup = BeautifulSoup(html, "html.parser")
    card = soup.select_one(".medical-status-modal-acceptance, .medical-status-modal-rejection")
    if not card:
        return {"error": "No medical status card found"}

    result = {}

    # --- Status ---
    status_el = card.select_one(".left.floated span")
    result["status"] = status_el.get_text(strip=True) if status_el else None

    # --- PDF URLs ---
    pdf_btn = card.select_one("a.ui.button.primary")
    if pdf_btn and pdf_btn.has_attr("onclick"):
        onclick = pdf_btn["onclick"]
        if "download" in onclick:
            result["pdf_url"] = "https://wafid.com" + onclick.split("window.open('")[1].split("'")[0]

    iframe = card.select_one("iframe#print_pdf")
    if iframe and iframe.has_attr("src"):
        result["print_url"] = "https://wafid.com" + iframe["src"]

    # --- Profile Image ---
    img = card.select_one(".profile-picture")
    if img and img.has_attr("src"):
        result["photo"] = img["src"]

    # --- All form fields ---
    inputs = card.select("input.custom-form")
    for inp in inputs:
        name = inp.get("id") or inp.get("name")
        val = inp.get("value", "").strip()
        if name:
            result[name] = val

    return result


def run(passport, headless=True):
    """Main scraping function."""
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
    driver = webdriver.Chrome(service=service, options=opts)

    try:
        driver.get(WAFID_URL)

        # Wait for form to load
        WebDriverWait(driver, 20).until(
            EC.presence_of_element_located((By.ID, "id_passport"))
        )

        # Select by passport
        passport_radio = driver.find_element(By.CSS_SELECTOR, 'input[value="passport"]')
        driver.execute_script("arguments[0].click();", passport_radio)

        # Input passport
        driver.find_element(By.ID, "id_passport").send_keys(passport)

        # Select nationality
        Select(driver.find_element(By.ID, "id_nationality")).select_by_value(BANGLADESH_VALUE)

        # Wait for captcha
        wait_for_captcha_token(driver, "#id_captcha", 60)

        # Submit
        driver.find_element(By.ID, "med-status-form-submit").click()

        # Wait for modal result
        WebDriverWait(driver, 45).until(
            EC.presence_of_element_located((By.CSS_SELECTOR, ".medical-status-modal-acceptance, .medical-status-modal-rejection"))
        )
        time.sleep(2)

        html = driver.page_source
        parsed = parse_medical_status_html(html)
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
