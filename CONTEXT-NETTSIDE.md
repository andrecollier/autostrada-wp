# Autostrada.no - Teknisk Kontekst

## Arkitektur Oversikt

### Nybil-sider (Custom Post Type: "bil")
- **Bygget med:** Bricks Builder (UI-basert, ikke PHP templates)
- **Kontakt-knapper:** Definert i Bricks templates, ikke i koden her
- **"Meld din interesse":** Injiseres via JavaScript i `themes/bricks-child-delete/functions.php`

### Bruktbil-sider (FINN.no integrasjon)
- **Plugin:** `plugins/sircon-finn-cars/`
- **API:** Henter biler fra FINN.no
- **Templates:** `plugins/sircon-finn-cars/templates/single.php` (enkeltbil), `archive.php` (liste)
- **Dealer-matching:** Basert på orgId eller navn fra FINN API

### Avdelinger/Dealers
Konfigurert i `plugins/sircon-finn-cars/includes/optionspage.setup.php`:

| Avdeling | Status |
|----------|--------|
| Autostrada Porsgrunn | Aktiv |
| Autostrada Arendal | Aktiv |
| Porsche Center Porsgrunn | Aktiv |
| Autostrada Notodden | Aktiv (Peugeot) |
| Autostrada Seljord | Aktiv |
| Autostrada Kongsberg | Aktiv (Peugeot - NY!) |
| Autostrada X (Xpeng) | Aktiv |

### Prøvekjøring-skjemaer
- **Plugin:** Formidable Forms (hovedsystem for prøvekjøring)
- **Loopify:** Brukes for nyhetsbrev, ikke prøvekjøring

#### Formidable Forms per merke:
| Form ID | Navn | E-post actions |
|---------|------|----------------|
| 32 | Bestill prøvekjøring Peugeot | Notodden (rune.johansen@autostrada.com) |
| 9 | Bestill prøvekjøring Volvo | - |
| 41 | Bestill prøvekjøring XPENG | - |
| 44 | Bestill prøvekjøring KIA | - |
| 42 | Bestill prøvekjøring Mercedes | - |

#### Formidable Email Actions (routing):
Hver avdeling har sin egen email action med conditions:
- Autostrada Notodden
- Autostrada Kongsberg
- Autostrada Seljord
- XPENG Porsgrunn/Arendal/Tønsberg
- etc.

## Merke → Avdeling Kobling

| Merke | Avdeling(er) |
|-------|--------------|
| Volvo | Porsgrunn, Arendal |
| Porsche | Porsche Center Porsgrunn |
| Mercedes-Benz | Porsgrunn, Kongsberg, Notodden |
| Peugeot | Notodden, **Kongsberg (NY!)** |
| Kia | Notodden |
| Xpeng | Autostrada X (flere lokasjoner) |
| Polestar | Porsgrunn |

## Viktige filer

### Kontakt-funksjonalitet
- `themes/bricks-child-delete/functions.php` - "Meld din interesse" knapp (linje 202-333)
- `plugins/sircon-finn-cars/templates/single.php` - Bruktbil kontakt (linje 14-162)

### Prøvekjøring
- `plugins/prefixseo-loopify-form/prefixseo-loopify-form.php` - Loopify skjema
- Prøvekjøring-sider per merke (WordPress pages):
  - ID 4549: Bestill prøvekjøring Peugeot
  - ID 4587: Bestill prøvekjøring Volvo
  - ID 4534: Bestill prøvekjøring Mercedes Benz
  - ID 15316: Bestill prøvekjøring Xpeng
  - ID 23966: Bestill prøvekjøring Kia

### Dealer-konfigurasjon
- `plugins/sircon-finn-cars/includes/optionspage.setup.php` (linje 366-404)

## Bricks Builder

Nybil-sidene er bygget i Bricks Builder UI. For å endre "Kontakt oss"-knapper må vi:
1. Logge inn på WordPress admin
2. Redigere Bricks template for bil-sider
3. Eller: Bruke Code Snippets plugin for å injisere JavaScript/PHP

**Code Snippets plugin** er installert og kan brukes for å legge til funksjonalitet uten å endre Bricks templates direkte.
