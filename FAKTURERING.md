# Fakturering - Autostrada.no

## Regler

- **Minimum 0.5 timer** per påbegynt oppgave
- Timer rundes opp til nærmeste 0.5
- Oppgaver dekket av **driftsavtale** faktureres ikke (markeres `Drift`)
- Timer fordeles på **avdeling** basert på hva oppgaven gjelder

## Avdelinger

| Kundenr. | Navn | Sted | Faktureres |
|----------|------|------|------------|
| 100004 | Autostrada Bil AS | Porsgrunn | Generelle oppgaver, felles funksjonalitet |
| 100008 | Autostrada Arendal AS | Arendal | Avdelingsspesifikt innhold/sider |
| 100006 | Autostrada Kongsberg AS | Fiskum | Avdelingsspesifikt innhold/sider |
| 100013 | Autostrada Notodden AS | Notodden | Avdelingsspesifikt innhold/sider |
| 100009 | Autostrada Porsgrunn AS | Porsgrunn | Avdelingsspesifikt innhold/sider |
| TBD | Polestar Porsgrunn AS (?) | Porsgrunn | Avdelingsspesifikt innhold/sider |
| 100007 | Autostrada Seljord AS | Seljord | Avdelingsspesifikt innhold/sider |
| 100016 | Autostrada Tønsberg AS | Porsgrunn | Avdelingsspesifikt innhold/sider |
| 100012 | Autostrada X AS | Porsgrunn | Avdelingsspesifikt innhold/sider |

## Timer logg

| Dato | Timer | Avdeling | Type | Beskrivelse | Status |
|------|-------|----------|------|-------------|--------|
| 2025-12-10 | 0.5 | Tønsberg AS (100016) | Bugfix | Fiks åpningstider - shortcode bug, opprettet popup-template, koblet til side | Ufakturert |
| 2025-12-10 | 0.5 | Bil AS (100004) | Bugfix | Polestar-siden krasjet på iPhone - komprimert mobil-bakgrunnsbilde (6.6MB→247KB), ryddet opp i global JS | Ufakturert |
| 2025-12-10 | 0.5 | Polestar Porsgrunn AS (TBD) | Oppsett | Ny avdeling Polestar Porsgrunn - avdelingsside, åpningstider-snippet, popup-template, WP Grid Builder grid (ID 32) | Ufakturert |
| 2025-12-10 | 0.5 | Bil AS (100004) | Bugfix | Div småfikser: Tønsberg popup-tittel, Porsgrunn lørdag 11-14, XPENG popup targetSelector | Ufakturert |
| 2025-12-11 | 0.5 | Bil AS (100004) | Utvikling | Meld din interesse-knapp for bil-sider - dynamisk knapp basert på ACF-felt, fungerer på alle bil-sider | Ufakturert |
| 2025-12-20 | 3.0 | Bil AS (100004) | Utvikling | Porsche Center Porsgrunn - endret bilderekkefølge for fremhevet bilde til samme som FINN.no (første bilde i stedet for siste) på både arkivside og bilsider | Ufakturert |
| 2026-01-05 | 0.5 | Porsgrunn AS (100009) | Bugfix | Porsgrunn åpningstider viste feil lørdag-tid - feilsøkt og fjernet gammel kode, lagt til test-parameter (?test_day) i snippet 7 for enklere testing | Ufakturert |
| 2026-01-13 | 2.0 | Kongsberg AS (100006) | Utvikling | Peugeot forhandler Kongsberg - ny Bricks template car-single-peugeot (ID 30612) med kontaktknapper Notodden+Kongsberg, Formidable Forms avdelingsvalg i prøvekjøring-skjema (form 32+47), email routing til thomas@autostrada.com | Ufakturert |
| 2026-01-27 | 0.5 | Kongsberg AS (100006) | Bugfix | Fiks prøvekjøring e-post action (ID 30616) - feil conditional logic format og manglende bilmerke-betingelse førte til at Thomas fikk XPENG-forespørsler med Peugeot-emne | Ufakturert |
| 2026-01-30 | 0.5 | Bil AS (100004) | Bugfix | Byttet om Bricks-innhold og post_content mellom Arendal (30796) og Seljord (30575) artiklene - innholdet var plassert på feil post, re-serialisert Bricks-data, flushet cache | Ufakturert |
| 2026-02-03 | 0.5 | Tønsberg AS (100016) | Bugfix | Fiks XPENG prøvekjøring e-postrouting (Form 47) - Tønsberg var lagret som "other" i database (bug), la til som vanlig option, oppdatert email action (ID 28586) condition og e-post til tonsberg@autostrada.com, skjult tom radio-knapp med CSS | Ufakturert |

## Driftsavtale (ikke fakturerbart)

| Dato | Avdeling | Type | Beskrivelse |
|------|----------|------|-------------|
| | | | |

---

## Sammendrag per avdeling

| Avdeling | Kundenr. | Timer ufakturert |
|----------|----------|------------------|
| Autostrada Bil AS | 100004 | 5.0 |
| Autostrada Arendal AS | 100008 | 0 |
| Autostrada Kongsberg AS | 100006 | 2.5 |
| Autostrada Notodden AS | 100013 | 0 |
| Autostrada Porsgrunn AS | 100009 | 0.5 |
| Polestar Porsgrunn AS | TBD | 0.5 |
| Autostrada Seljord AS | 100007 | 0 |
| Autostrada Tønsberg AS | 100016 | 1.0 |
| Autostrada X AS | 100012 | 0 |

**Totalt ufakturert: 9.5 timer**

---

## Oppgavetyper

- `Oppsett` - Konfigurasjon, infrastruktur
- `Bugfix` - Feilretting
- `Utvikling` - Ny funksjonalitet
- `Innhold` - Tekst, bilder, sider
- `Support` - Feilsøking, rådgivning
