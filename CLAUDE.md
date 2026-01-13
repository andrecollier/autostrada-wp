# Autostrada.no - Prosjektinstruksjoner

## Time Tracking

Dette prosjektet bruker time tracking. **Ved oppstart av samtale**, påminn brukeren:

> "Husk: Dette prosjektet har time tracking. Jeg logger arbeid til FAKTURERING.md når oppgaver fullføres."

### Regler for timeføring
- **Minimum 0.5 timer** per påbegynt oppgave
- Timer rundes opp til nærmeste 0.5
- Oppgaver dekket av driftsavtale faktureres ikke (markeres i egen tabell)

### Etter fullført oppgave
1. **Oppdater `FAKTURERING.md`**:
   - Legg til ny rad i "Timer logg"-tabellen med: Dato | Timer | Avdeling | Type | Beskrivelse | Status
   - Oppdater "Sammendrag per avdeling"-tabellen
   - Oppdater "Totalt ufakturert"-summen

2. **Spør om git commit/push**:
   > "Skal jeg committe og pushe endringene til git?"

   Gjør dette ved naturlige stoppunkter - etter fullførte oppgaver eller underveis ved større endringer.

### Avdelinger (for fakturering)
- **Bil AS (100004)** - Generelle oppgaver, felles funksjonalitet
- **Arendal AS (100008)** - Avdelingsspesifikt
- **Kongsberg AS (100006)** - Avdelingsspesifikt
- **Notodden AS (100013)** - Avdelingsspesifikt
- **Porsgrunn AS (100009)** - Avdelingsspesifikt
- **Polestar Porsgrunn AS (TBD)** - Avdelingsspesifikt
- **Seljord AS (100007)** - Avdelingsspesifikt
- **Tønsberg AS (100016)** - Avdelingsspesifikt
- **X AS (100012)** - Avdelingsspesifikt

### Oppgavetyper
- `Oppsett` - Konfigurasjon, infrastruktur
- `Bugfix` - Feilretting
- `Utvikling` - Ny funksjonalitet
- `Innhold` - Tekst, bilder, sider
- `Support` - Feilsøking, rådgivning
- `Drift` - Dekket av driftsavtale (ikke fakturerbart)

## Prosjektinfo

- **Hosting**: Kinsta (SSH: `kinsta-autostrada`)
- **Remote path**: `/www/autostradano_293/public`
- **Plugin-oppdatering**: `ssh kinsta-autostrada "cd /www/autostradano_293/public && wp plugin update --all"`
- **Egenutviklede plugins**: sircon-finn-cars, sircon-library, prefixseo-loopify-form, dynamic-shortcode, tacdis-ecom
