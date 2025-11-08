const puppeteer = require('puppeteer');
(async () => {
  try {
    const browser = await puppeteer.launch();
    await browser.close();
    console.log("SUCCESS: Browser launched and closed.");
  } catch (e) {
    console.error("FAILURE: Browser launch failed with error:", e.message);
  }
})();