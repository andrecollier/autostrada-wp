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
| 100007 | Autostrada Seljord AS | Seljord | Avdelingsspesifikt innhold/sider |
| 100016 | Autostrada Tønsberg AS | Porsgrunn | Avdelingsspesifikt innhold/sider |
| 100012 | Autostrada X AS | Porsgrunn | Avdelingsspesifikt innhold/sider |

## Timer logg

| Dato | Timer | Avdeling | Type | Beskrivelse | Status |
|------|-------|----------|------|-------------|--------|
| 2025-12-10 | 0.5 | Tønsberg AS (100016) | Bugfix | Fiks åpningstider - shortcode bug, opprettet popup-template, koblet til side | Ufakturert |
| 2025-12-10 | 0.5 | Bil AS (100004) | Bugfix | Polestar-siden krasjet på iPhone - komprimert mobil-bakgrunnsbilde (6.6MB→247KB), ryddet opp i global JS | Ufakturert |
| 2025-12-10 | 0.5 | Bil AS (100004) | Oppsett | Ny avdeling Polestar Porsgrunn - avdelingsside, åpningstider-snippet, popup-template, WP Grid Builder grid (ID 32) | Ufakturert |
| 2025-12-10 | 0.5 | Bil AS (100004) | Bugfix | Div småfikser: Tønsberg popup-tittel, Porsgrunn lørdag 11-14, XPENG popup targetSelector | Ufakturert |

## Driftsavtale (ikke fakturerbart)

| Dato | Avdeling | Type | Beskrivelse |
|------|----------|------|-------------|
| | | | |

---

## Sammendrag per avdeling

| Avdeling | Kundenr. | Timer ufakturert |
|----------|----------|------------------|
| Autostrada Bil AS | 100004 | 1.5 |
| Autostrada Arendal AS | 100008 | 0 |
| Autostrada Kongsberg AS | 100006 | 0 |
| Autostrada Notodden AS | 100013 | 0 |
| Autostrada Porsgrunn AS | 100009 | 0 |
| Autostrada Seljord AS | 100007 | 0 |
| Autostrada Tønsberg AS | 100016 | 0.5 |
| Autostrada X AS | 100012 | 0 |

**Totalt ufakturert: 2.0 timer**

---

## Oppgavetyper

- `Oppsett` - Konfigurasjon, infrastruktur
- `Bugfix` - Feilretting
- `Utvikling` - Ny funksjonalitet
- `Innhold` - Tekst, bilder, sider
- `Support` - Feilsøking, rådgivning
