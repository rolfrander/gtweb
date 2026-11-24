# Notekartotek – project summary

## What this is
`index.html` is a single standalone page (no external libraries, works offline) that shows a sheet-music
catalogue ("notekartotek") as a searchable, filterable, sortable table. It was generated from
`Notekartotek.csv` (UTF-8 with BOM, 11 columns, Norwegian headers). The data now lives **inside the HTML** and is
maintained by hand there; the CSV is only the original source. Do not regenerate the HTML from the CSV without
asking, as that would overwrite hand edits.

## Files
- `Notekartotek.csv`  – original export (845 data rows).
- `index.html` – the app: CSS, embedded JSON data, JavaScript.

## Data
- Embedded in `<script id="data" type="application/json">`, one object per line, easy to edit by hand.
- Keys: `navn`, `arkivRef`, `sjanger`, `komponist`, `arr`, `besetning`, `komplett`, `grad`, `beskrivelse`, `sistEndret`.
- Types: `komplett` is a boolean; `arkivRef` and `grad` are numbers where possible (two `arkivRef` values are text:
  "B 06", "K19"); empty fields are `""`; `sistEndret` is a `YYYY-MM-DD HH:MM:SS` string.
- The CSV column "Tid" was removed completely (column and data), as irrelevant.
- If JSON is edited by hand, `</` inside strings must be written `<\/`.

## Code structure (script section of the HTML)
- `COLUMNS` array: key, label, type (`text|number|bool|date`), optional `filter: 'select'`, optional `detail: true`.
  Adding or renaming a field means updating `COLUMNS` and the JSON together.
- `detail: true` columns (Komplett, Intern beskrivelse, Sist endret) are not table columns; they appear under a row
  when it is expanded. They are still covered by global search and Excel export, but have no header filter or sort.
- `state` holds the global query, per-column filters and multi-column sort; `getRows()` = filter + sort.
- Search: global box (all words must match, matches highlighted) plus per-column filters (substring, or dropdown
  for Sjanger, Besetning, Komplett, Grad).
- Sort: click header (asc, desc, off); Shift+click adds secondary columns; Norwegian-aware, numeric-aware
  collation; empty values always last.
- Rows expand/collapse on click or Enter/Space; open rows are tracked in a `WeakSet` and survive re-render.
- Export: "Eksporter til Excel" builds a real `.xlsx` in the browser (hand-written minimal ZIP + SpreadsheetML,
  inline strings, bold frozen header, autofilter, column widths) from the currently filtered and sorted rows. It
  includes all columns, detail columns too.
- Print: "Skriv ut" prints the filtered and sorted rows (A4 landscape, repeating header, filters and buttons
  hidden, summary line with active search, filters and sort).

## Besetning clean-up (done)
Distinct values reduced from 125 to 69. Rules applied:
- Case and whitespace variants merged; "Bands" normalised to "Band".
- A value that starts with another value (at a word boundary) merges into the shorter one. Exception: "Flex <number>"
  values stay distinct, since the number matters.
- "Full Band", "Concert Band" and "Janitsjar" are equivalent and now all read **Concert Band**.
- Typos fixed, e.g. Symohonic, Youn Band, Young Bang, Flexi 5 (to Flex 5), Fan-way (to Fun-Way).
- "Younger Band" became "Young Band"; "Janitsjarkorps" became "Concert Band".
- Known information loss by design: e.g. "Harmonie-Fanfare" became "Harmonie", and "Janitsjar og Brass" and
  "Full Band og Symphonic Band" became "Concert Band".
- Deliberately left alone (not requested): "Music for beginners - Debut" vs "Debut", Blasorchester / Grosses
  Blasorchester, and "Band" vs "Concert Band".
- 372 rows have an empty Besetning.

## Preferences and conventions
- UI language is Norwegian (labels, buttons: "Nullstill", "Eksporter til Excel", "Skriv ut").
- Keep everything in the one HTML file, with no external dependencies.
- The user is Norwegian, and writes short instructions; ask before large or lossy data changes and show the mapping.

## Status
Not tested in a real browser or in Excel; only syntax-checked and the xlsx structure validated with Node and
Python (zip integrity and XML well-formedness). Worth verifying the Excel file opens cleanly and the print preview
looks right.
