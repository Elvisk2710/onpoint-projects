"""Render the marketing plans to PDFs styled in each site's theme.
Usage: python3 marketing/build_pdfs.py   (needs: pip install markdown weasyprint)
"""
import pathlib, re, markdown
from weasyprint import HTML

ROOT = pathlib.Path(__file__).resolve().parent.parent
OUT = ROOT / "marketing" / "pdf"
OUT.mkdir(exist_ok=True)

THEMES = {
    "onpoint-hygiene-30-day-plan": dict(
        site="onpoint-cleaning", image="office_cleaning.png",
        fonts="family=Plus+Jakarta+Sans:wght@400;500;600;700;800",
        head="'Plus Jakarta Sans'", body="'Plus Jakarta Sans'",
        primary="#68a77c", primary_dark="#5a9169", soft="#f0f9f4",
        accent="#ff8c00", dark="#1f2937", cover_bg="#1f2937",
    ),
    "shieldtrac-security-30-day-plan": dict(
        site="onpoint-security", image="security_guards.png",
        fonts="family=Inter:wght@400;500;600;700",
        head="'Inter'", body="'Inter'",
        primary="#1e40af", primary_dark="#1e3a8a", soft="#e0f2fe",
        accent="#ea580c", dark="#1f2937", cover_bg="#1e3a8a",
    ),
    "onpoint-civils-30-day-plan": dict(
        site="onpoint-engineering", image="team-machinery.png",
        fonts="family=Archivo:wght@500;600;700;800&family=Manrope:wght@400;500;600;700",
        head="'Archivo'", body="'Manrope'",
        primary="#0f1d2e", primary_dark="#0f1d2e", soft="#f4f6f8",
        accent="#f2b705", dark="#1a2330", cover_bg="#0f1d2e",
    ),
}

CSS = """
@import url('https://fonts.googleapis.com/css2?{fonts}&display=swap');
@page {{ size: A4 landscape; margin: 16mm 14mm 16mm 14mm;
  @bottom-left {{ content: "{title}"; font: 8pt {body}, sans-serif; color: #6b7280; }}
  @bottom-right {{ content: counter(page); font: 600 8pt {body}, sans-serif; color: {primary}; }}
  @top-right {{ content: ""; border-top: 3pt solid {accent}; width: 40mm; }} }}
@page cover {{ margin: 0; @bottom-left {{ content: none }} @bottom-right {{ content: none }} @top-right {{ content: none; border: none }} }}
body {{ font-family: {body}, sans-serif; color: {dark}; font-size: 9.5pt; line-height: 1.5; }}
.cover {{ page: cover; height: 210mm; background: {cover_bg}; color: #fff; position: relative; overflow: hidden; }}
.cover .photobg {{ position: absolute; right: 0; top: 0; height: 100%; width: 52%; background: {primary}; }}
.cover img.photo {{ position: absolute; right: 0; bottom: 0; height: 100%; width: 52%; object-fit: cover; }}
.cover .shade {{ position: absolute; right: 0; top: 0; height: 100%; width: 52%;
  background: linear-gradient(90deg, {cover_bg} 0%, rgba(0,0,0,0) 35%); }}
.cover .txt {{ position: absolute; left: 18mm; top: 34mm; width: 48%; }}
.cover .logo {{ width: 18mm; height: 18mm; }}
.cover .kicker {{ margin-top: 14mm; text-transform: uppercase; letter-spacing: 2pt; font-weight: 700; font-size: 10pt; color: {accent}; }}
.cover .name {{ font-family: {head}, sans-serif; font-size: 34pt; line-height: 1.1; margin: 4mm 0 6mm; color: #fff; border: none; }}
.cover .bar {{ width: 30mm; height: 2.5mm; background: {accent}; margin-bottom: 6mm; }}
.cover p {{ font-size: 11pt; color: rgba(255,255,255,.85); }}
.cover .foot strong {{ color: {accent}; }}
.cover .foot {{ position: absolute; left: 18mm; bottom: 16mm; font-size: 9pt; color: rgba(255,255,255,.7); }}
h1 {{ display: none; }}
h2:first-of-type {{ page-break-before: avoid; }}
h2 {{ font-family: {head}, sans-serif; font-size: 20pt; color: #fff; background: {primary};
  padding: 5mm 7mm; margin: 0 0 6mm; border-left: 5mm solid {accent}; page-break-before: always; }}
h3 {{ font-family: {head}, sans-serif; font-size: 13pt; color: {primary_dark}; margin: 7mm 0 3mm;
  padding-bottom: 1.5mm; border-bottom: 1.5pt solid {accent}; page-break-after: avoid; }}
strong {{ color: {primary_dark}; }}
table {{ width: 100%; border-collapse: collapse; margin: 2mm 0 4mm; font-size: 8.6pt; page-break-inside: auto; }}
thead {{ display: table-header-group; }}
th {{ background: {dark}; color: #fff; text-align: left; padding: 2.2mm 2.5mm; font-family: {head}, sans-serif; font-weight: 600; }}
td {{ padding: 2mm 2.5mm; border-bottom: 0.6pt solid #e5e7eb; vertical-align: top; }}
tr {{ page-break-inside: avoid; }}
tbody tr:nth-child(even) td {{ background: {soft}; }}
td:first-child {{ font-weight: 700; color: {primary_dark}; white-space: nowrap; }}
p {{ margin: 2mm 0; }}
ul, ol {{ margin: 2mm 0; padding-left: 6mm; }}
li {{ margin: 1.2mm 0; }}
li::marker {{ color: {accent}; }}
blockquote {{ margin: 3mm 0; padding: 3mm 5mm; background: {soft}; border-left: 3pt solid {accent}; }}
hr {{ display: none; }}
p > strong:first-child {{ color: {primary}; }}
.meta {{ display: none; }}
"""

for name, t in THEMES.items():
    src = (ROOT / "marketing" / f"{name}.md").read_text()
    title = src.splitlines()[0].lstrip("# ").strip()
    company, _, subtitle = title.partition(" — ")
    lines = src.splitlines()[1:]
    info = [l for l in lines[:6] if l.startswith("**")]
    body_md = "\n".join(l for l in lines if l not in info)
    info_html = "<br>".join(markdown.markdown(l)[3:-4] for l in info)
    body_md = re.sub(r"^#(?=\w)", r"\\#", body_md, flags=re.M)
    body_md = re.sub("[\U0001F000-\U0001FFFF\u2600-\u27BF\uFE0F\u2B50\u23F1]", "", body_md)
    html_body = markdown.markdown(body_md, extensions=["tables"])
    html_body = html_body.replace("<table>", "<table>", )
    site = ROOT / t["site"] / "assets"
    css = CSS.format(title=title.replace('"', ""), **t)
    html = f"""<!doctype html><html><head><meta charset="utf-8"><style>{css}</style></head><body>
<section class="cover">
  <div class="photobg"></div><img class="photo" src="{(site / 'images' / t['image']).as_uri()}">
  <div class="shade"></div>
  <div class="txt">
    <img class="logo" src="{(site / 'favicon.svg').as_uri()}">
    <div class="kicker">{subtitle}</div>
    <div class="name">{company}</div>
    <div class="bar"></div>
    <p>Marketing strategy, content pillars, posting schedule and a day-by-day content calendar for the next 30 days.</p>
  </div>
  <div class="foot">{info_html}<br>Prepared November 2026</div>
</section>
{html_body}
</body></html>"""
    out = OUT / f"{name}.pdf"
    HTML(string=html, base_url=str(ROOT)).write_pdf(out)
    print("wrote", out)
