# Google EU Consent Policy - Statusrapport

**Dato:** 2026-02-27
**Frist fra Google:** 26. april 2026
**Client ID:** 691-651-9525

---

## Bakgrunn

Google har gjennomført en audit av autostrada.no og funnet 3 problemer med EU User Consent Policy. Uten fiks innen fristen mister Autostrada tilgang til personalisering (remarketing) og konverteringsmåling i Google Ads.

---

## Hva er gjort (WordPress-siden)

### 1. Cookie Information-script flyttet til topp av `<head>`

**Problem:** Cookie Information (consent mode v2) ble lastet ETTER GTM og GA4. Det betydde at consent defaults (`denied`) ikke ble satt før tracking-scripts kjørte, og cookies ble satt uten samtykke.

**Løsning:** Opprettet nytt Code Snippet (snippet #43) som laster Cookie Information med `wp_head` prioritet 0 - før alt annet. Scriptet ble fjernet fra Bricks → Settings → Custom Scripts Header.

**Resultat:** Cookie Information er nå linje 6 i HTML `<head>`, FØR GTM dataLayer (linje 7) og GTM container (linje 107).

### 2. Duplikat GTM-script fjernet

**Problem:** GTM-PP88VPG ble lastet to ganger - én gang via Duracelltomi-plugin og én gang via manuelt script i Bricks Custom Scripts Header.

**Løsning:** Fjernet det manuelle GTM-scriptet og noscript-fallback fra Bricks. Kun Duracelltomi-pluginen håndterer GTM nå.

### 3. Site Kit GA4 frontend snippet deaktivert

**Problem:** Google Site Kit la til `gtag.js` og `gtag('config', 'G-PPS5GEDFS0')` direkte i HTML-en, utenfor GTM. Dette bypasser consent mode fullstendig - cookies ble satt umiddelbart.

**Løsning:** Deaktivert `useSnippet` for GA4 og UA i Site Kit-innstillingene. Site Kit er fortsatt aktiv for dashboard/Search Console, men legger ikke lenger til tracking-script på frontend.

**Konsekvens:** GA4-tracking (G-PPS5GEDFS0) MÅ nå håndteres som en tag i GTM-containeren. Se sjekkliste for tracking-ekspert nedenfor.

### 4. Google Business Data Responsibility-lenke lagt til

**Problem:** Personvernerklæringen manglet lenke til https://business.safety.google/privacy/.

**Løsning:** Lagt til nytt avsnitt "Bruk av Google-tjenester" med lenke til Google Business Data Responsibility Site i begge personvernerklæringer:
- Side 24321: Ekstern personvernerklæring 2024 (/ekstern-personvernerklaering-2024/)
- Side 4514: Personvern (/personvern/)

---

## Nåværende script-rekkefølge på autostrada.no

```
Linje 6:   Cookie Information (uc.js) med data-gcm-version="2.0"  ← FØRST
Linje 7:   GTM dataLayer init (Duracelltomi)
Linje 107: GTM container load GTM-PP88VPG (Duracelltomi)
Linje 113: Google site verification meta tag
```

GA4 gtag.js er FJERNET fra frontend (ikke lenger i HTML).

---

## Sjekkliste for tracking-ekspert

### MÅ sjekkes/gjøres

- [ ] **Verifiser at GA4-tag finnes i GTM-containeren (GTM-PP88VPG)**
  - Measurement ID: `G-PPS5GEDFS0`
  - Hvis GA4 IKKE finnes som tag i GTM: opprett en GA4 Configuration-tag
  - Hvis GA4 allerede finnes: alt er OK, ingen endring nødvendig

- [ ] **Sjekk consent-innstillinger på alle tags i GTM**
  - Alle Google-tags (GA4, Ads, Remarketing) bør ha built-in consent checks aktivert
  - GA4-tag: krever `analytics_storage` consent
  - Google Ads tags: krever `ad_storage`, `ad_user_data`, `ad_personalization` consent

- [ ] **Verifiser consent mode signals i GTM**
  - Cookie Information med `data-gcm-version="2.0"` setter automatisk consent defaults til `denied`
  - Når bruker aksepterer, oppdateres consent til `granted`
  - Bruk Google Tag Assistant (https://tagassistant.google.com/) for å verifisere at:
    - `consent default` viser `denied` for alle consent types ved sidelast
    - `consent update` viser `granted` etter bruker klikker "Aksepter"

- [ ] **Test i Google Tag Assistant**
  - Åpne https://tagassistant.google.com/
  - Koble til autostrada.no
  - Verifiser at consent-hendelser vises korrekt i tidslinjen
  - Sjekk at GA4 og Ads-tags bare fyrer etter consent er gitt

### BØR sjekkes

- [ ] **Cookie Information dashboard** (policy.app.cookieinformation.com)
  - Verifiser at Google Consent Mode v2 er aktivert i dashboardet
  - Sjekk at cookie-kategoriene er korrekt mappet:
    - Statistiske → `analytics_storage`
    - Markedsføring → `ad_storage`, `ad_user_data`, `ad_personalization`
    - Funksjonelle → `functionality_storage`, `personalization_storage`

- [ ] **Google Ads dashboard**
  - Sjekk om consent-advarselen forsvinner etter noen dager
  - Google re-scanner vanligvis innen 1-2 uker

### Test-prosedyre

1. Åpne autostrada.no i inkognitomodus
2. Åpne Chrome DevTools → Application → Cookies
3. **FØR du klikker noe i banneret:** Sjekk at det IKKE er satt Google-cookies (_ga, _gid, _gcl, etc.)
4. Klikk "AVVIS" - verifiser at tracking-cookies fortsatt ikke settes
5. Åpne ny inkognito-fane, gå til autostrada.no
6. Klikk "AKSEPTER" - verifiser at tracking-cookies NÅ settes
7. Sjekk i Google Tag Assistant at consent signals er korrekte

---

## Oversikt over de 3 Google-problemene

| Problem | Vår fix (WordPress) | Tracking-ekspert | Status |
|---------|-------------------|------------------|--------|
| Manglende lenke til Google Business Data Responsibility | Lenke lagt til i begge personvernerklæringer | Ingen handling nødvendig | Ferdig |
| Samtykkesignaler ikke riktig konfigurert | Cookie Information lastes nå FØR GTM, Site Kit gtag fjernet | Verifiser consent-innstillinger på tags i GTM | Krever verifisering |
| Cookies settes før samtykke | Script-rekkefølge fikset, Site Kit gtag fjernet | Test at cookies ikke settes før samtykke | Krever verifisering |
