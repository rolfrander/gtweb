# Websider for Godlia/Trasop skolers Musikkorps

## Maler

Sidene er laget i HTML-mal "massively" fra [HTML5Up](https://html5up.net/massively)
og malsystemet [Hugo](https://gohugo.io/). Dette er vesentlig mer avansert enn vi trenger, men jeg fant dette og gadd ikke lete lenger...

## Innhold

Teksten på sidene kommer fra filer i katalogen `content`, disse har følgende struktur:

* Header, markert med `---` før og etter, dette er "key-value" i YAML-format
* Innhold, dette er markdown, kombinert med hugo-makroer (markert med `{{}}`) og ren HTML.

Hver `.md`-fil i `content`-katalogen danner en egen katalog med en index.html i seg (bortsett fra `_index.md` som havner på rot-katalogen).

Relevante felter i header er:

* title: definerer overskrift og tekst i menyen
* menu: parametre for hvordan siden inkluderes i hovedmenyen. Hvis det ikke er noe `menu`-felt, kommer siden ikke i menyen. Se [hugo dokumentasjonen](https://gohugo.io/templates/menu-templates/#menu-entries-from-the-page-s-front-matter) for detaljer om hvordan menykonfigurasjonen fungerer.
* description: er en kort tekst som havner i description-tag i html-header, samt brukes på forsiden
* excludeFirstPage: alle sider blir inkludert i forsiden, med mindre dette er satt til `true`.
* bilde: filnavn til bilde som brukes på generert side. Filnavnet er relativt til `static`-katalogen
* forsidebilde: dersom oppgitt blir dette brukt på forsiden. Alternativt brukes samme som `bilde`.

Teksten følger normal [markdown](https://en.wikipedia.org/wiki/Markdown).
Epost-adresser blir angitt med makro (i hugo-terminologi, en "shortcode") som dette: `{{<email styret>}}`, som kombineres med ` @godliatrasop.no` (her blir det også lagt inn litt css- og javascript-magi som er ment å gjøre det litt vanskeligere for spammere å høste epost-adresser, aner ikke om dette virker...). Hvis man har behov for å inkludere epost-adresser i andre domener kan man parametrisere dette som `{{<email user="bruker" domain="domene">}}`.

## Formattering

Css er generert på bakgrunn av [scss](https://sass-lang.com/) som ligger i `themes/massively/assets/sass`.

## Maler

Hovedstrukturen på Hugo-malene er slik:
* `\_default/baseof.html` definerer overordnet struktur for alle genererte html-sider. Denne inneholder referanser til blokker som blir definert andre steder.
* `_default/list.html` og `_default/single.html` definerer strukturen for liste-sider eller enkeltartikler. Det er kun den siste som brukes i praksis av oss. Disse arver fra "baseof" og definerer hva som skal være innholdet i blokkene referert i baseof.
* `partials` inneholder fragmenter av html som blir inkludert i de andre sidene
* `shortcodes` definerer makroer som kan refereres fra markdown

Det er ikke gjort en veldig god jobb med å skille maler fra innhold, så det er sikkert deler av malverket som er knyttet spesielt til godliatrasop.no.

For eksempel skjemaet for registrering av nye meldlemmer ligger i `\_default/medlemskjema.html`, mens javascript-koden som understøtter skjemaet ligger under `static` på toppnivå.
