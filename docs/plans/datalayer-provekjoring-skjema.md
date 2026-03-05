# Plan: dataLayer push for provekjoring-skjema

## Kontekst
Tracking-spesialist (Joackim) trenger a spore provekjoringer per merke i GTM/GA. Ingen dataLayer-tracking eksisterer i dag for Formidable Forms-skjemaer. GTM container `GTM-PP88VPG` er allerede aktiv pa siden.

## Hva skal gjores
Legge til en JavaScript-snippet som lytter pa Formidable Forms form submit (form ID 47) pa `/provekjoring/` og pusher en `dataLayer.push()` med:

- **event**: `provekjoring_submit`
- **merke**: valgt bilmerke (fra radio-felt `8blvc8bf07d23a2`)
- **lokasjon**: valgt avdeling (fra conditionally visible radio-felt basert pa merke)
- **epost**: kundens e-post (fra felt `vojo37ebe05407a3`)
- **telefon**: som string, formatert med +47 prefix, uten mellomrom (fra felt `yei32957d5e0273`)

## Teknisk tilnaerming
**Metode: Code Snippets plugin** (allerede installert, brukes for lignende formal - snippet 42 styrer allerede felt-visibility pa denne siden)

### Implementasjon
Legg til et nytt Code Snippet (PHP som outputter JS) som:

1. Kun kjorer pa `/provekjoring/`-siden
2. Lytter pa Formidable Forms sin `frmFormComplete` jQuery event
3. Leser verdiene fra skjemaet ved submit
4. Formaterer telefonnummer: fjern mellomrom, legg til +47 prefix hvis mangler
5. Finner riktig lokasjon basert pa hvilket avdelings-radio som er synlig/valgt
6. Pusher til dataLayer

### dataLayer-struktur
```javascript
window.dataLayer.push({
  event: 'provekjoring_submit',
  merke: 'Volvo',           // valgt bilmerke
  lokasjon: 'Porsgrunn',    // valgt avdeling
  epost: 'kunde@example.com',
  telefon: '+4712345678'    // string, +47, ingen mellomrom
});
```

### Relevante Formidable-felt
| Felt | Key | Type | Innhold |
|------|-----|------|---------|
| Velg bilmerke | `8blvc8bf07d23a2` | radio | Merke-valg |
| Velg avdeling Volvo | `9icvb` | radio | Avdeling |
| Velg avdeling Mercedes | `w65fl` | radio | Avdeling |
| Velg avdeling Kia | `75ncp` | radio | Avdeling |
| Velg avdeling XPENG | `uaa3r` | radio | Avdeling |
| Velg avdeling Peugeot | `nqwfz` | radio | Avdeling |
| Telefon | `yei32957d5e0273` | text | Telefonnummer |
| E-post | `vojo37ebe05407a3` | email | E-postadresse |

### Fil som endres
Ingen lokal fil - snippet legges til via WP CLI:
```bash
ssh kinsta-autostrada "cd /www/autostradano_293/public && wp eval '...'"
```
Eller manuelt via WP Admin > Code Snippets.

## Verifisering
1. Ga til autostrada.no/provekjoring/
2. Apne browser DevTools > Console
3. Skriv `dataLayer` for a se navarende state
4. Fyll ut skjemaet med testdata og send
5. Sjekk at `provekjoring_submit`-event dukker opp i dataLayer med riktige verdier
6. Verifiser at telefon er formatert som `+47XXXXXXXX` (string, ingen mellomrom)
7. Test med GTM Preview mode for a bekrefte at eventet fanges opp

## Tidsestimat
**0.5 timer** - Enkelt JS-snippet, kjent monster, eksisterende infrastruktur.
