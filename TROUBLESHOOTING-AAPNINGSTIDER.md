# Feilsøking: Åpningstider viser feil på nettsiden

**Problem:** Autostrada Porsgrunn viser 08:00-17:00 på lørdager, korrekt tid er 11:00-14:00.

**Status:** Snippets og templates er verifisert korrekte.

## Sjekkliste for feilsøking

### 1. Cache-clearing (gjør i denne rekkefølgen)

#### a) WordPress admin
1. Logg inn på WordPress admin
2. Gå til verktøy/plugins som kan ha cache:
   - **WP Rocket** (hvis installert): Settings → Clear Cache
   - **W3 Total Cache** (hvis installert): Performance → Purge All Caches
   - **WP Super Cache** (hvis installert): Settings → Delete Cache
   - **Autoptimize** (hvis installert): Clear cache
3. **Bricks Builder cache**:
   - Bricks → Settings → Performance → Clear Cache
   - Eller legg til `?bricks=run` i URL for å tvinge reload

#### b) Object Cache
```bash
# SSH til serveren og kjør:
wp cache flush
```

#### c) Cloudflare/CDN (hvis brukt)
1. Logg inn på Cloudflare
2. Gå til Caching → Purge Everything
3. Eller purge spesifikk URL for Porsgrunn-siden

#### d) Browser cache
1. Hard refresh: `Cmd+Shift+R` (Mac) / `Ctrl+Shift+R` (Windows)
2. Eller åpne i Incognito/Private mode

### 2. Verifiser hvor åpningstider vises

#### Code Snippet (ID 7)
1. WordPress admin → Snippets → All Snippets
2. Finn snippet med ID 7: `display_opening_hours`
3. Sjekk at lørdag viser: `11.00 - 14.00` (IKKE 08.00-17.00)

#### Bricks Popup Template (ID 18686)
1. WordPress admin → Bricks → Templates
2. Finn template ID 18686
3. Sjekk HTML-tabellen for lørdag
4. Skal vise: `<td>11.00 - 14.00</td>` for lørdag

#### Avdelingssiden (Porsgrunn)
1. Gå til Pages → Finn Porsgrunn avdelingsside
2. Sjekk om åpningstider er hardkodet direkte på siden
3. Sjekk om riktig popup template ID (18686) er koblet

### 3. Test på forskjellige steder

Test hvor åpningstidene vises feil:
- [ ] Hovedsiden (footer?)
- [ ] Avdelingssiden `/avdelinger/porsgrunn/`
- [ ] Popup når man klikker "Åpningstider"
- [ ] Google My Business (ekstern, ikke WordPress)
- [ ] Schema markup / structured data

### 4. Sjekk database direkte

```bash
# SSH til serveren
wp option get 'snippet_code_7'
```

Eller sjekk i databasen:
```sql
SELECT * FROM wp_posts WHERE ID = 18686;
```

## Kjente problemer

Fra `PLAN-AAPNINGSTIDER-PLUGIN.md`:
- Åpningstider er spredt på 3 steder som må holdes manuelt synkronisert
- Code Snippets (ID 7)
- Bricks popup (ID 18686)
- Sidens referanse til popup

**Mulig årsak:** Hvis én av disse 3 stedene ikke er oppdatert, vil feil tid vises.

## Løsning hvis manuell oppdatering ikke hjelper

Hvis alt over er prøvd og problemet består, kan det være:

1. **Hardkodet verdi et annet sted** - Søk i databasen:
```bash
wp db query "SELECT * FROM wp_posts WHERE post_content LIKE '%08:00%' AND post_content LIKE '%17:00%' AND post_content LIKE '%lørdag%';"
```

2. **Schema markup/JSON-LD** - Sjekk kilden på siden:
   - Høyreklikk → "View Page Source"
   - Søk etter "openingHours" eller "@type":"LocalBusiness"

3. **JavaScript som overskriver** - Sjekk browser DevTools Console for errors

## Rask test

For å bekrefte at riktig tid vises:

1. Gå til: `https://autostrada.no/avdelinger/porsgrunn/`
2. Åpne DevTools (F12)
3. Kjør i Console:
```javascript
document.body.innerHTML.includes('08:00') && document.body.innerHTML.includes('17:00')
```
Hvis `true` → Feil tid vises fortsatt
Hvis `false` → Problemet er løst

---

**Opprettet:** 2026-01-05
**Problem rapportert av:** Henrik Taalesen
**Status:** Under feilsøking
