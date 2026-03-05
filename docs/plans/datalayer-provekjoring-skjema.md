# Plan: dataLayer push for prøvekjøring-skjemaer

## Kontekst
Tracking-spesialist (Joackim) trenger å spore prøvekjøringer per merke i GTM/GA. Ingen dataLayer-tracking eksisterer i dag for Formidable Forms-skjemaer. GTM container `GTM-PP88VPG` er allerede aktiv på siden.

## Oversikt over skjemaer

Det finnes **6 prøvekjøring-skjemaer** på tvers av ulike sider:

| # | Side | URL | Form ID | Merke | Avdeling-felt |
|---|------|-----|---------|-------|---------------|
| 1 | Prøvekjøring (generell) | `/provekjoring/` | 47 | Radio-valg (alle merker) | Conditional radio per merke |
| 2 | Bestill prøvekjøring Volvo | `/om-autostrada/kontakt/bestill-provekjoring-volvo/` | 9 | Volvo (hardkodet) | Radio: Porsgrunn, Arendal |
| 3 | Bestill prøvekjøring Mercedes-Benz | `/om-autostrada/kontakt/bestill-provekjoring-mercedes-benz/` | 42 | Mercedes-Benz (hardkodet) | Select: Notodden, Seljord, Kongsberg |
| 4 | Bestill prøvekjøring Peugeot | `/om-autostrada/kontakt/bestill-provekjoring-peugeot/` | 32 | Peugeot (hardkodet) | Select: Notodden, Kongsberg |
| 5 | Bestill prøvekjøring XPENG | `/om-autostrada/kontakt/bestill-provekjoring-xpeng/` | 41 | XPENG (hardkodet) | Radio: Arendal, Porsgrunn, Tønsberg |
| 6 | Bestill prøvekjøring Kia | `/om-autostrada/kontakt/bestill-provekjoring-kia/` | 44 | Kia (hardkodet) | Select: Seljord, Kongsberg |

## dataLayer-struktur (felles for alle skjemaer)
```javascript
window.dataLayer.push({
  event: 'provekjoring_submit',
  merke: 'Volvo',              // valgt bilmerke
  lokasjon: 'Porsgrunn',       // valgt avdeling (uten prefix)
  epost: 'kunde@example.com',
  telefon: '+4712345678'       // string, +47, ingen mellomrom
});
```

## Feltmapping per skjema

### Form 47 — Prøvekjøring (generell) `/provekjoring/`
| Felt | Key | ID | Type | Innhold |
|------|-----|----|------|---------|
| Velg bilmerke | `8blvc8bf07d23a2` | 677 | radio | Merke-valg |
| Avdeling Volvo | `9icvb` | 701 | radio | Avdeling |
| Avdeling Mercedes | `w65fl` | 704 | radio | Avdeling |
| Avdeling Kia | `75ncp` | 707 | radio | Avdeling |
| Avdeling XPENG | `uaa3r` | 709 | radio | Avdeling |
| Avdeling Peugeot | `nqwfz` | 712 | radio | Avdeling |
| Avdeling Polestar | `dm1ck` | 713 | radio | Avdeling |
| Telefon | `yei32957d5e0273` | 695 | text | Telefonnummer |
| E-post | `vojo37ebe05407a3` | 696 | email | E-postadresse |
| Navn | `wvx2d360b62020c3` | 694 | text | Navn |

**Spesielt:** Merke velges via radio → conditional avdelings-radio vises. Volvo/Polestar redirecter til ekstern booking.

### Form 9 — Volvo `/om-autostrada/kontakt/bestill-provekjoring-volvo/`
| Felt | Key | ID | Type | Innhold |
|------|-----|----|------|---------|
| Avdeling | `car_type7e77c53028` | 119 | radio | Volvo Porsgrunn, Volvo Arendal |
| Telefon | `yei3d5d139f71b` | 130 | text | Telefonnummer |
| E-post | `vojo351a9a3a936` | 131 | email | E-postadresse |
| Navn | `wvx2d3aabbd6067` | 129 | text | Navn |

**Merke:** Hardkodet `"Volvo"`. Lokasjon: strip "Volvo " prefix.

### Form 42 — Mercedes-Benz `/om-autostrada/kontakt/bestill-provekjoring-mercedes-benz/`
| Felt | Key | ID | Type | Innhold |
|------|-----|----|------|---------|
| Forhandler | `w4pi43` | 571 | select | Notodden, Seljord, Kongsberg |
| Telefon | `yei32957d5e0272` | 583 | text | Telefonnummer |
| E-post | `vojo37ebe05407a2` | 584 | email | E-postadresse |
| Navn | `wvx2d360b62020c2` | 582 | text | Navn |

**Merke:** Hardkodet `"Mercedes-Benz"`. Lokasjon: strip "Autostrada " prefix.

### Form 32 — Peugeot `/om-autostrada/kontakt/bestill-provekjoring-peugeot/`
| Felt | Key | ID | Type | Innhold |
|------|-----|----|------|---------|
| Forhandler | `velgforhandler` | 718 | select | Notodden, Kongsberg |
| Telefon | `yei30265a32f05d1d953a46c` | 381 | text | Telefonnummer |
| E-post | `vojo3048498715cb959897bd0` | 382 | email | E-postadresse |
| Navn | `wvx2dba5f3be3339e4db9c000` | 380 | text | Navn |

**Merke:** Hardkodet `"Peugeot"`. Lokasjon: strip "Autostrada " prefix.

### Form 41 — XPENG `/om-autostrada/kontakt/bestill-provekjoring-xpeng/`
| Felt | Key | ID | Type | Innhold |
|------|-----|----|------|---------|
| Avdeling | `hqix8` | 669 | radio | XPENG Arendal, XPENG Porsgrunn, XPENG Tønsberg |
| Telefon | `yei3aae92fa99c2` | 550 | text | Telefonnummer |
| E-post | `vojo38146d3a5202` | 551 | email | E-postadresse |
| Navn | `wvx2dbb92c5e5692` | 549 | text | Navn |

**Merke:** Hardkodet `"XPENG"`. Lokasjon: strip "XPENG " prefix.

### Form 44 — Kia `/om-autostrada/kontakt/bestill-provekjoring-kia/`
| Felt | Key | ID | Type | Innhold |
|------|-----|----|------|---------|
| Avdeling | `lh1lq` | 663 | select | Seljord, Kongsberg |
| Telefon | `yei3aae92fa99c4` | 618 | text | Telefonnummer |
| E-post | `vojo38146d3a5204` | 619 | email | E-postadresse |
| Navn | `wvx2dbb92c5e5694` | 617 | text | Navn |

**Merke:** Hardkodet `"Kia"`. Lokasjon: strip "Autostrada " prefix.

## Teknisk tilnærming

**Metode: Code Snippets plugin** (allerede installert, snippet 42 styrer felt-visibility)

### Implementasjon — ett felles snippet
Legg til **ett** Code Snippet (PHP som outputter JS) som håndterer alle 6 skjemaer:

1. Kun kjører på sider med prøvekjøring-skjemaer (sjekk URL eller form ID)
2. Lytter på Formidable Forms sin `frmFormComplete` jQuery event
3. Identifiserer hvilket skjema som ble sendt (form ID fra event)
4. Henter verdier basert på riktig feltmapping for det skjemaet
5. Formaterer telefonnummer: fjern mellomrom, legg til +47 prefix hvis mangler
6. Renser lokasjon: fjerner "Autostrada ", "Volvo ", "XPENG " etc. prefix
7. Setter merke: hardkodet for merkespesifikke skjemaer, fra radio for form 47
8. Pusher til dataLayer

### Pseudokode
```javascript
jQuery(document).on('frmFormComplete', function(event, form, response) {
  var formId = jQuery(form).find('input[name="form_id"]').val();

  var config = {
    '47': { merkeField: '8blvc8bf07d23a2', telefonField: 'yei32957d5e0273', epostField: 'vojo37ebe05407a3', merke: null },
    '9':  { merkeField: null, telefonField: 'yei3d5d139f71b', epostField: 'vojo351a9a3a936', merke: 'Volvo' },
    '42': { merkeField: null, telefonField: 'yei32957d5e0272', epostField: 'vojo37ebe05407a2', merke: 'Mercedes-Benz' },
    '32': { merkeField: null, telefonField: 'yei30265a32f05d1d953a46c', epostField: 'vojo3048498715cb959897bd0', merke: 'Peugeot' },
    '41': { merkeField: null, telefonField: 'yei3aae92fa99c2', epostField: 'vojo38146d3a5202', merke: 'XPENG' },
    '44': { merkeField: null, telefonField: 'yei3aae92fa99c4', epostField: 'vojo38146d3a5204', merke: 'Kia' }
  };
  // ... lookup + push
});
```

### Forskjeller mellom skjemaene
| Utfordring | Løsning |
|------------|---------|
| Form 47 har merke-valg, resten hardkodet | Config-objekt med `merke: null` vs `merke: 'Volvo'` |
| Avdeling er radio (form 9, 41, 47) vs select (32, 42, 44) | Sjekk `input:checked` og `select option:selected` |
| Avdelings-verdier har ulike prefix | Strip alt før siste ord, eller bruk regex `/^(Autostrada\|Volvo\|XPENG\|Kia)\s+/` |
| Form 47 har conditional visibility på avdeling | Finn synlig avdelings-container, les checked radio |

## Verifisering
For **hver** av de 6 sidene:
1. Gå til siden
2. Åpne browser DevTools > Console
3. Fyll ut skjemaet med testdata og send
4. Sjekk at `provekjoring_submit`-event dukker opp i dataLayer med riktige verdier:
   - `merke` er korrekt (hardkodet eller fra valg)
   - `lokasjon` er ren (uten "Autostrada"/"Volvo" prefix)
   - `telefon` er formatert som `+47XXXXXXXX`
   - `epost` er korrekt
5. Test med GTM Preview mode

## Tidsestimat

| Oppgave | Timer |
|---------|-------|
| Form 47 (generell prøvekjøring) — oppsett og testing | 1.0 |
| Form 9, 42, 32, 41, 44 (5 merkespesifikke skjemaer) — utvidelse av samme snippet | 1.0 |
| **Totalt** | **2.0** |

Merknad: Siden alle skjemaer bruker Formidable Forms og samme event-mønster, bygges ett felles snippet. Hovedjobben er form 47 (mest kompleks med conditional fields). De 5 merkespesifikke skjemaene er enklere (hardkodet merke, ett avdelings-felt) og håndteres ved å utvide config-objektet i samme snippet.
