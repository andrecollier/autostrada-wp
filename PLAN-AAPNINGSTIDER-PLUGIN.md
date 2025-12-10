# Plan: Autostrada Åpningstider Plugin

## Bakgrunn

Åpningstider er i dag spredt på 3 steder som må holdes manuelt synkronisert:
1. **Code Snippets** - PHP-funksjoner som viser "Åpningstid i dag"
2. **Bricks popup-templates** - Hardkodet HTML-tabell med alle ukedager
3. **Avdelingssider** - Referanser til popup template IDs

Dette fører til feil og er vanskelig for kunden å vedlikeholde.

## Nåværende oppsett

| Avdeling | Code Snippet ID | Shortcode | Popup Template ID |
|----------|-----------------|-----------|-------------------|
| Porsgrunn | 7 | `[display_opening_hours]` | 18686 |
| Skien | 9 | `[display_another_opening_hours]` | 18783 |
| Arendal | 10 | `[display_arendal_opening_hours]` | 18798 |
| Notodden | 11 | `[display_notodden_opening_hours]` | 18808 |
| Seljord | 12 | `[display_seljord_opening_hours]` | 18822 |
| Kongsberg | 13 | `[display_kongsberg_opening_hours]` | 18834 |
| Porsche | 14 | `[display_porsche_opening_hours]` | 18845 |
| Xpeng | 24 | `[display_xpeng_opening_hours]` | 21783 |
| Tønsberg | 32 | `[display_opening_hours_tonsberg]` | **MANGLER** |

## Kjente problemer

- Tønsberg mangler egen popup-template (bruker feil template)
- Duplikate/gamle templates som skaper forvirring
- Ingen sentral oversikt for kunden

## Løsning: Ny plugin

### Brukergrensesnitt

```
WordPress Admin → Autostrada → Åpningstider

┌─────────────────────────────────────────────────────────┐
│ Åpningstider                                            │
├─────────────────────────────────────────────────────────┤
│ Velg avdeling: [Dropdown: Porsgrunn ▼]                  │
│                                                         │
│ Mandag:    [08.00] - [17.00]                           │
│ Tirsdag:   [08.00] - [17.00]                           │
│ Onsdag:    [08.00] - [17.00]                           │
│ Torsdag:   [08.00] - [19.00]                           │
│ Fredag:    [08.00] - [17.00]                           │
│ Lørdag:    [10.00] - [14.00]                           │
│ Søndag:    [Stengt    ]                                │
│                                                         │
│ [Lagre endringer]                                       │
└─────────────────────────────────────────────────────────┘
```

### Shortcodes

```php
// Dagens åpningstid (erstatter gamle shortcodes)
[autostrada_hours location="porsgrunn"]
// Output: "Åpningstid i dag: 08.00 - 17.00"

// Full ukeliste (for popup)
[autostrada_hours location="porsgrunn" format="full"]
// Output: HTML-tabell med alle dager

// Bakoverkompatibilitet - gamle shortcodes fortsetter å fungere
```

### Teknisk implementasjon

1. **Database**: `wp_options` med JSON-struktur per avdeling
2. **Admin-side**: React eller vanilla JS for brukergrensesnitt
3. **Shortcodes**: Erstatter Code Snippets-funksjonene
4. **Popup-innhold**: Dynamisk generert HTML (ingen manuell Bricks-template)

### Migrering

1. Les eksisterende åpningstider fra Code Snippets
2. Importer til ny plugin
3. Test at shortcodes fungerer
4. Oppdater Bricks-templates til å bruke nye shortcodes
5. Deaktiver gamle Code Snippets

### Estimert arbeid

- Plugin-utvikling: 4-6 timer
- Migrering og testing: 2-3 timer
- Dokumentasjon: 1 time

**Totalt: 7-10 timer**

---

*Plan opprettet: 2024-12-10*
