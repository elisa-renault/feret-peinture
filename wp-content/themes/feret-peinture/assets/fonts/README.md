# Local typefaces

Two locally served families, both under SIL Open Font License 1.1:

- **Newsreader** by Production Type; Google Fonts upstream `ofl/newsreader/Newsreader[opsz,wght].ttf`.
- **Source Sans 3** by Adobe; Google Fonts upstream `ofl/sourcesans3/SourceSans3[wght].ttf`.

Downloaded from https://github.com/google/fonts on 2026-09-15. Exact license text is included in `OFL-Newsreader.txt` and `OFL-Source-Sans-3.txt`. Font binaries were subset with fontTools (Latin U+0020–024F, punctuation U+2000–206F, euro and arrows), preserving variable axes, to WOFF2. French accents and typographic apostrophes are retained. Newsreader retains its name. The subset of Source Sans 3 is renamed **Feret Sans** in font metadata and CSS to respect the reserved font name “Source”; original copyright and license notices are preserved. Fonts are not sold by themselves. Both fonts are preloaded in the document head. CSS uses `font-display: optional` to prevent a late font swap and layout shift; fallback families are Georgia and Arial respectively. On a slow first visit, the fallback remains for that page view. No browser request is sent to Google Fonts.

The abstract hero is original CSS/vector composition by Aliant for this project; it is labelled as illustration, never as a real completed project. Favicon and typographic monogram are original provisional artwork, with no claim of registered trademark.
