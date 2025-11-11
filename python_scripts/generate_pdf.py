import pdfkit
import sys
import os

# Path to wkhtmltopdf
WKHTMLTOPDF_PATH = r"C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf.exe"

config = pdfkit.configuration(wkhtmltopdf=WKHTMLTOPDF_PATH)

# Your Laravel route that returns the HTML
html_url = "http://127.0.0.1:8001/pdf"

# Output path
output_path = os.path.join(os.getcwd(), "storage", "app", "public", "medical_report.pdf")

# Generate PDF
pdfkit.from_url(html_url, output_path, configuration=config)

print("✅ PDF generated successfully at:", output_path)
