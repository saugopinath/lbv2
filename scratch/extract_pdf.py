import pypdf
import os

pdf_path = "Annapurna Yojana Family Level Data Collection Form final.pdf"
output_path = "scratch/pdf_text.txt"

os.makedirs("scratch", exist_ok=True)

reader = pypdf.PdfReader(pdf_path)
print(f"Total pages: {len(reader.pages)}")

with open(output_path, "w", encoding="utf-8") as f:
    for idx, page in enumerate(reader.pages):
        text = page.extract_text()
        f.write(f"--- PAGE {idx + 1} ---\n")
        f.write(text)
        f.write("\n\n")

print(f"PDF text extracted to {output_path}")
