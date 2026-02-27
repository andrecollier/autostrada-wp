# Google EU User Consent Policy - Fix Plan for autostrada.no

**Dato:** 2026-02-27
**Frist fra Google:** 26. april 2026
**Client ID:** 691-651-9525

## Bakgrunn

Google har gjennomført en audit av autostrada.no og funnet 3 problemer med EU User Consent Policy. Hvis disse ikke fikses innen fristen, kan Autostrada miste tilgang til personalisering (remarketing) og konverteringsmåling i Google Ads.

---

## Nåværende situasjon

- **GTM:** GTM-PP88VPG via Duracelltomi-plugin (lastes i footer) + duplikat GTM-script i custom head
- **GA4:** G-PPS5GEDFS0 via Google Site Kit
- **Cookie consent:** Cookie Information (dansk CMP) er installert via script i custom head
  - Script: `https://policy.app.cookieinformation.com/uc.js`
  - Språk: `data-culture="NB"` (norsk bokmål)
  - **Google Consent Mode v2:** `data-gcm-version="2.0"` er satt (bra!)
- **Banneret fungerer:** Viser kategorier (Nødvendige, Funksjonelle, Statistiske, Markedsføring) med Avvis/Lagre/Aksepter-knapper

### Identifiserte problemer med nåværende oppsett

**Kritisk: Script-rekkefølge er feil!**
Gjeldende rekkefølge i HTML `<head>`:
1. Linje 7-12: GTM dataLayer init (Duracelltomi)
2. Linje 96-105: Google Site Kit gtag.js + GA4 config (G-PPS5GEDFS0) - **INGEN consent default satt her!**
3. Linje 112-130: GTM container load (Duracelltomi footer) + duplikat GTM i custom head
4. Linje 135: Cookie Information script (`uc.js`)

**Problemet:** GA4 (via Site Kit) og GTM lastes og setter cookies FØR Cookie Information-scriptet får satt consent defaults. Cookie Information med `data-gcm-version="2.0"` skal sette `consent('default', ...)` automatisk, men det skjer for sent fordi gtag allerede har kjørt.

---

## De 3 problemene og løsninger

### Problem 1: Manglende lenke til Google's Business Data Responsibility Site

**Status:** Lenken til https://business.safety.google/privacy/ mangler.

**Banneret viser i dag:**
- "Les mer om informasjonskapsler"
- "Les vår personvernerklæring" (lenker til ekstern personvernerklæring)

**Personvernerklæringen** (autostrada.no/personvernerklaering/) nevner **ikke** Google's Business Data Responsibility Site.

**Løsning (velg én):**
- **Alt A:** Legg til lenken i Cookie Information-banneret (gjøres i Cookie Information dashboard)
- **Alt B:** Legg til lenken i personvernerklæringen på autostrada.no (enklere, siden banneret allerede lenker dit)

**Anbefaling:** Alt B - legg til et avsnitt om Google-tjenester i personvernerklæringen med lenke til https://business.safety.google/privacy/. Tekst som:

> *"Vi bruker Google-tjenester for annonsering og analyse. Les mer om hvordan Google behandler data på [Googles nettsted om ansvar for bedriftsdata](https://business.safety.google/privacy/)."*

---

### Problem 2: Samtykkesignaler er ikke riktig konfigurert

**Status:** Cookie Information har `data-gcm-version="2.0"` satt, som betyr at den *skal* sende Google Consent Mode v2-signaler. Men det ser ut til at dette ikke fungerer korrekt, trolig fordi:

1. **GA4 (Site Kit) kjører uten consent-sjekk** - Google Site Kit setter opp gtag direkte uten å vente på consent
2. **GTM lastes før Cookie Information** - consent defaults er ikke satt når GTM starter
3. **Cookie Information-scriptet lastes for sent** - det bør lastes FØR alle Google-scripts

**Løsning:**
1. **Flytt Cookie Information-scriptet FØRST i `<head>`** - det MÅ lastes før GTM og gtag
2. **Konfigurer Google Site Kit til å respektere consent mode** - eller deaktiver Site Kit sin gtag og la GTM håndtere alt
3. **Verifiser i Cookie Information dashboard** at Google Consent Mode v2 er aktivert

**Teknisk:** Cookie Information med `data-gcm-version="2.0"` setter automatisk:
```javascript
gtag('consent', 'default', {
  'ad_storage': 'denied',
  'ad_user_data': 'denied',
  'ad_personalization': 'denied',
  'analytics_storage': 'denied'
});
```
...men dette MÅ skje FØR `gtag('config', 'G-PPS5GEDFS0')` kjøres.

---

### Problem 3: Cookies plasseres før samtykke

**Status:** Direkte konsekvens av Problem 2 - fordi GA4 og GTM lastes før Cookie Information, settes tracking-cookies umiddelbart uten å vente på samtykke.

**Løsning:** Fikses automatisk når Problem 2 løses (riktig script-rekkefølge).

---

## Handlingsplan

### Steg 1: Fiks script-rekkefølge (viktigst!)

Cookie Information-scriptet MÅ lastes FØRST, før alt annet. Nåværende plassering i custom head er for sent.

**Mulige tilnærminger:**

**Alt A: Flytt i custom head/code snippets**
- Flytt `<script id="CookieConsent" ...>` til helt øverst i `<head>`, FØR GTM og Site Kit
- Dette krever enten en code snippet med høy prioritet eller endring i header.php

**Alt B: Konfigurer via Duracelltomi GTM-plugin**
- Duracelltomi-pluginen har `integrate-cookiebot: false` i innstillingene
- Den har IKKE direkte Cookie Information-integrasjon, men consent mode kan konfigureres
- Alternativt: Håndter all tracking via GTM i stedet for Site Kit

**Alt C: Deaktiver Google Site Kit sin gtag-output**
- La GTM håndtere all GA4-sporing i stedet
- Fjern duplikat gtag.js fra Site Kit
- Konfigurer GA4 som tag i GTM med consent-sjekk

**Anbefaling:** Alt A + C kombinert:
1. Flytt Cookie Information til toppen av `<head>`
2. Deaktiver Site Kit sin frontend gtag (behold for Search Console)
3. Sørg for at GA4 kjøres via GTM med consent-triggers

### Steg 2: Legg til Google Business Data Responsibility-lenke

- Legg til i personvernerklæringen (side på autostrada.no)
- Tekst om Google-tjenester + lenke til https://business.safety.google/privacy/

### Steg 3: Fjern duplikat GTM-script

Det er to GTM-loads:
1. Duracelltomi-plugin (korrekt)
2. Manuelt script i custom head (duplikat)

Fjern duplikaten - bruk kun Duracelltomi-pluginen.

### Steg 4: Verifiser i Cookie Information dashboard

- Logg inn på Cookie Information (policy.app.cookieinformation.com)
- Sjekk at Google Consent Mode v2 er aktivert
- Sjekk at riktige cookie-kategorier er mappet til Google consent types
- Verifiser at autostrada.no-domenet er registrert

### Steg 5: Test og verifiser

- Test i inkognitomodus med Chrome DevTools
- Sjekk at ingen tracking-cookies settes før samtykke
- Bruk Google Tag Assistant for å verifisere consent signals
- Sjekk at `consent('default', ...)` fyres FØR `config`-kall

---

## Hva kan gjøres her (WP-CLI/kode) vs. andre systemer

### Vi kan gjøre via WP-CLI / kode:
- Flytte Cookie Information-script til toppen av `<head>` (code snippet / theme header)
- Fjerne duplikat GTM-script
- Legge til tekst i personvernerklæringen
- Konfigurere Duracelltomi GTM-plugin innstillinger
- Deaktivere Site Kit frontend tracking

### Må gjøres i andre systemer:
- **Cookie Information dashboard** - Verifisere/aktivere Google Consent Mode v2
- **GTM web-grensesnitt** - Sjekke at consent-innstillinger er riktige for tags
- **Google Ads** - Verifisere at advarselen forsvinner etter fix

---

## Viktige lenker

- Google's Business Data Responsibility Site: https://business.safety.google/privacy/
- Google Consent Mode dokumentasjon: https://developers.google.com/tag-platform/security/guides/consent
- Cookie Information support: https://support.cookieinformation.com/
- EU User Consent Policy: https://www.google.com/about/company/user-consent-policy/

---

## Estimert arbeid

| Hva | Timer |
|-----|-------|
| Fiks script-rekkefølge + fjern duplikat GTM | 0.5 |
| Oppdater personvernerklæring med Google-lenke | 0.5 |
| Verifisering Cookie Information dashboard + GTM | 0.5 |
| Testing og validering | 0.5 |
| **Totalt** | **2.0** |

**Avdeling:** Bil AS (100004) - felles funksjonalitet for hele autostrada.no
