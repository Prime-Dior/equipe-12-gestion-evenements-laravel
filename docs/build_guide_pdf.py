"""Build the French presentation guide PDF from EXPLICATION_MEMBRE_A.md."""

from pathlib import Path
import re
from xml.sax.saxutils import escape

from reportlab.lib import colors
from reportlab.lib.enums import TA_CENTER, TA_LEFT
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import ParagraphStyle, getSampleStyleSheet
from reportlab.lib.units import mm
from reportlab.pdfbase import pdfmetrics
from reportlab.pdfbase.ttfonts import TTFont
from reportlab.platypus import (
    BaseDocTemplate,
    Frame,
    PageTemplate,
    PageBreak,
    Paragraph,
    Preformatted,
    Spacer,
    Table,
    TableStyle,
)


ROOT = Path(__file__).resolve().parents[1]
SOURCE = ROOT / "docs" / "EXPLICATION_MEMBRE_A.md"
OUTPUT = ROOT / "docs" / "Guide_Soutenance_Eventia_Primael.pdf"


def register_fonts() -> tuple[str, str, str]:
    fonts = Path("C:/Windows/Fonts")
    regular = fonts / "arial.ttf"
    bold = fonts / "arialbd.ttf"
    italic = fonts / "ariali.ttf"
    if all(path.exists() for path in (regular, bold, italic)):
        pdfmetrics.registerFont(TTFont("GuideArial", str(regular)))
        pdfmetrics.registerFont(TTFont("GuideArial-Bold", str(bold)))
        pdfmetrics.registerFont(TTFont("GuideArial-Italic", str(italic)))
        return "GuideArial", "GuideArial-Bold", "GuideArial-Italic"
    return "Helvetica", "Helvetica-Bold", "Helvetica-Oblique"


REGULAR, BOLD, ITALIC = register_fonts()
GREEN = colors.HexColor("#355d48")
INK = colors.HexColor("#252b27")
MUTED = colors.HexColor("#68736c")
PALE = colors.HexColor("#f1f5f1")
BORDER = colors.HexColor("#d9e1da")


def inline_markup(text: str) -> str:
    text = text.replace("→", "->").replace("←", "<-")
    text = escape(text)
    text = re.sub(
        r"\[([^\]]+)\]\((https?://[^)]+)\)",
        r'<link href="\2" color="#355d48">\1</link>',
        text,
    )
    text = re.sub(r"`([^`]+)`", r'<font name="Courier">\1</font>', text)
    text = re.sub(r"\*\*([^*]+)\*\*", r"<b>\1</b>", text)
    return text


def build_story(markdown: str, styles: dict[str, ParagraphStyle]) -> list:
    lines = markdown.splitlines()
    story = []
    index = 0
    first_heading = True

    while index < len(lines):
        line = lines[index].rstrip()
        if not line:
            index += 1
            continue

        if line.startswith("```"):
            index += 1
            block = []
            while index < len(lines) and not lines[index].startswith("```"):
                block.append(lines[index])
                index += 1
            index += 1
            story.append(Preformatted("\n".join(block), styles["code"]))
            story.append(Spacer(1, 5))
            continue

        if line.startswith("|"):
            rows = []
            while index < len(lines) and lines[index].strip().startswith("|"):
                cells = [cell.strip() for cell in lines[index].strip().strip("|").split("|")]
                if not all(re.fullmatch(r":?-{3,}:?", cell or "-") for cell in cells):
                    rows.append([Paragraph(inline_markup(cell), styles["table_cell"]) for cell in cells])
                index += 1
            if rows:
                widths = [170 * mm / len(rows[0])] * len(rows[0])
                table = Table(rows, colWidths=widths, repeatRows=1, hAlign="LEFT")
                table.setStyle(TableStyle([
                    ("BACKGROUND", (0, 0), (-1, 0), GREEN),
                    ("TEXTCOLOR", (0, 0), (-1, 0), colors.white),
                    ("FONTNAME", (0, 0), (-1, 0), BOLD),
                    ("BACKGROUND", (0, 1), (-1, -1), colors.white),
                    ("GRID", (0, 0), (-1, -1), 0.45, BORDER),
                    ("VALIGN", (0, 0), (-1, -1), "TOP"),
                    ("LEFTPADDING", (0, 0), (-1, -1), 6),
                    ("RIGHTPADDING", (0, 0), (-1, -1), 6),
                    ("TOPPADDING", (0, 0), (-1, -1), 5),
                    ("BOTTOMPADDING", (0, 0), (-1, -1), 5),
                ]))
                story.extend([table, Spacer(1, 8)])
            continue

        heading = re.match(r"^(#{1,3})\s+(.+)$", line)
        if heading:
            level = len(heading.group(1))
            if level == 1 and first_heading:
                story.append(Paragraph(inline_markup(heading.group(2)), styles["GuideTitle"]))
                story.append(Spacer(1, 8))
                first_heading = False
            else:
                if level == 2 and story:
                    story.append(Spacer(1, 7))
                story.append(Paragraph(inline_markup(heading.group(2)), styles[f"GuideHeading{level}"]))
            index += 1
            continue

        if line.startswith("- "):
            item = line[2:]
            story.append(Paragraph(inline_markup(item), styles["bullet"], bulletText="•"))
            index += 1
            continue

        numbered = re.match(r"^(\d+)\.\s+(.+)$", line)
        if numbered:
            story.append(Paragraph(
                inline_markup(numbered.group(2)),
                styles["numbered"],
                bulletText=f"{numbered.group(1)}.",
            ))
            index += 1
            continue

        paragraph = [line]
        index += 1
        while index < len(lines):
            candidate = lines[index].rstrip()
            if (not candidate or candidate.startswith(("#", "```", "|", "- "))
                    or re.match(r"^\d+\.\s+", candidate)):
                break
            paragraph.append(candidate)
            index += 1
        story.append(Paragraph(inline_markup(" ".join(paragraph)), styles["body"]))

    return story


def page_chrome(canvas, document):
    canvas.saveState()
    width, height = A4
    canvas.setStrokeColor(BORDER)
    canvas.setLineWidth(0.6)
    canvas.line(18 * mm, 15 * mm, width - 18 * mm, 15 * mm)
    canvas.setFont(REGULAR, 8)
    canvas.setFillColor(MUTED)
    canvas.drawString(18 * mm, 10 * mm, "Eventia · Guide de soutenance · Équipe 12")
    canvas.drawRightString(width - 18 * mm, 10 * mm, str(document.page))
    canvas.restoreState()


def main():
    styles = getSampleStyleSheet()
    styles.add(ParagraphStyle(
        name="GuideTitle", fontName=BOLD, fontSize=22, leading=27,
        textColor=GREEN, alignment=TA_CENTER, spaceAfter=12,
    ))
    styles.add(ParagraphStyle(
        name="GuideHeading2", fontName=BOLD, fontSize=15, leading=19,
        textColor=GREEN, spaceBefore=10, spaceAfter=6, keepWithNext=True,
    ))
    styles.add(ParagraphStyle(
        name="GuideHeading3", fontName=BOLD, fontSize=11.5, leading=15,
        textColor=INK, spaceBefore=7, spaceAfter=4, keepWithNext=True,
    ))
    styles.add(ParagraphStyle(
        name="body", fontName=REGULAR, fontSize=9.3, leading=13.5,
        textColor=INK, alignment=TA_LEFT, spaceAfter=6,
    ))
    styles.add(ParagraphStyle(
        name="bullet", parent=styles["body"], leftIndent=15, firstLineIndent=-10,
        bulletIndent=1, spaceAfter=3,
    ))
    styles.add(ParagraphStyle(
        name="numbered", parent=styles["body"], leftIndent=20, firstLineIndent=-16,
        bulletIndent=1, spaceAfter=4,
    ))
    styles.add(ParagraphStyle(
        name="table_cell", fontName=REGULAR, fontSize=7.7, leading=10,
        textColor=INK,
    ))
    styles.add(ParagraphStyle(
        name="code", fontName="Courier", fontSize=7.7, leading=10,
        textColor=INK, backColor=PALE, borderColor=BORDER,
        borderWidth=0.5, borderPadding=6, leftIndent=5, rightIndent=5,
        spaceBefore=4, spaceAfter=7,
    ))

    document = BaseDocTemplate(
        str(OUTPUT), pagesize=A4,
        leftMargin=19 * mm, rightMargin=19 * mm,
        topMargin=18 * mm, bottomMargin=22 * mm,
        title="Guide de soutenance Eventia — Primael KPATINDE",
        author="Primael KPATINDE — Équipe 12",
    )
    frame = Frame(document.leftMargin, document.bottomMargin,
                  document.width, document.height, id="main")
    document.addPageTemplates([PageTemplate(id="pages", frames=frame, onPage=page_chrome)])
    document.build(build_story(SOURCE.read_text(encoding="utf-8"), styles))
    print(f"PDF créé : {OUTPUT} ({OUTPUT.stat().st_size} octets)")


if __name__ == "__main__":
    main()
