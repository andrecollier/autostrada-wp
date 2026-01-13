# Plan: Peugeot forhandler i Kongsberg

## Bakgrunn
Autostrada har blitt Peugeot-forhandler i Kongsberg (i tillegg til Notodden). Kunden har allerede lagt til "Kontakt Kongsberg" flere steder selv.

---

## DEL 1: Bricks Template - Peugeot nybil-sider

### Nåværende situasjon
- Peugeot-modeller bruker "Car Single" (ID 1101) - generell template
- Viser "Kontakt oss" (generell knapp) + "Bestill prøvekjøring"

### Ønsket resultat
- "Kontakt Notodden" + "Kontakt Kongsberg" + "Bestill prøvekjøring"

### Løsning: Klone template
**Steg i Bricks Builder:**

1. **Gå til:** Bricks → Templates → Car Single (ID 1101)

2. **Klone template:**
   - Høyreklikk → Duplicate
   - Navngi: "Car Single Peugeot"

3. **Sett conditions:**
   - Template Settings → Conditions
   - Post Type: `bil`
   - Taxonomy: `merke` = `Peugeot`

4. **Endre kontaktknapper:**
   - Finn "Kontakt oss" knappen
   - Erstatt med to knapper:

   | Knapp | Tekst | Link |
   |-------|-------|------|
   | 1 | Kontakt Notodden | `/avdeling/autostrada.notodden/` |
   | 2 | Kontakt Kongsberg | `/avdeling/autostrada-kongsberg/` |

5. **Lagre og publiser ny template**

6. **Oppdater original Car Single template (ID 1101):**
   - Gå tilbake til Car Single (ID 1101)
   - Template Settings → Conditions
   - Legg til ekskludering: Taxonomy `merke` ≠ `Peugeot`
   - (Eller sjekk om Bricks automatisk prioriterer den mer spesifikke templaten)

### Peugeot-modeller som vil bruke ny template
| ID | Modell |
|----|--------|
| 22668 | Peugeot E-3008 |
| 23781 | Peugeot E-5008 SUV 4x4 |
| 5030 | Peugeot E-2008 SUV |
| 5022 | Peugeot E-208 |
| 5381 | Peugeot Partner |
| 10442 | Peugeot Boxer |
| 5371 | Peugeot Expert |
| 30088 | Peugeot E-Partner |

---

## DEL 2: Formidable Forms - Prøvekjøring

### Nåværende situasjon
- Form 32: "Bestill prøvekjøring Peugeot"
- Sender kun til Notodden: `rune.johansen@autostrada.com`
- Ingen avdelingsvalg

### Ønsket resultat
- Bruker velger avdeling (Notodden/Kongsberg)
- E-post routes til riktig person

### Løsning: Legg til avdelingsvalg + email action

**Steg i Formidable Forms:**

1. **Gå til:** Formidable → Forms → Bestill prøvekjøring Peugeot (ID 32)

2. **Legg til nytt felt:**
   - Type: Radio buttons
   - Label: "Velg avdeling"
   - Alternativer:
     - `notodden` = Autostrada Notodden
     - `kongsberg` = Autostrada Kongsberg
   - Required: Ja

3. **Oppdater eksisterende email action (Notodden):**
   - Gå til: Settings → Actions → "Autostrada Notodden"
   - Legg til condition: Avdeling = "notodden"

4. **Legg til ny email action (Kongsberg):**
   - Dupliser "Autostrada Notodden" action
   - Endre navn til: "Autostrada Kongsberg"
   - Endre e-post til: `thomas@autostrada.com`
   - Endre condition: Avdeling = "kongsberg"

5. **Test skjemaet**

---

## DEL 3: Sjekkliste andre steder

Kunden nevnte: "Kan du se over flere steder også hvis det trengs?"

**Sjekk disse stedene:**
- [ ] Hovedside Peugeot (`/peugeot/`) - har allerede Kontakt Notodden/Kongsberg?
- [ ] Footer/navigasjon - lenker til begge avdelinger?
- [ ] Andre Peugeot-relaterte sider

---

## Oppsummering

| Oppgave | Hvor | Hvem |
|---------|------|------|
| Klone Car Single → Car Single Peugeot | Bricks | Du (WP admin) |
| Sette conditions for Peugeot | Bricks | Du (WP admin) |
| Endre "Kontakt oss" til to knapper | Bricks | Du (WP admin) |
| Legge til avdelingsvalg i form | Formidable | Du (WP admin) |
| Legge til Kongsberg email action | Formidable | Du (WP admin) |
| Veilede/dokumentere | Her | Claude |

---

## E-post kontakter

| Avdeling | Kontakt | E-post |
|----------|---------|--------|
| Peugeot Notodden | Rune Johansen | rune.johansen@autostrada.com |
| Peugeot Kongsberg | Thomas (?) | thomas@autostrada.com |

---

## Status
**Pågående** - Venter på gjennomgang av plan
