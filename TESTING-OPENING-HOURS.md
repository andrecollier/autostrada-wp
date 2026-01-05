# Testing åpningstider - Simulere lørdag

Siden `display_opening_hours` shortcode bruker PHP `date()` som kjører server-side, kan vi ikke mocke dato i browser. Her er 3 måter å teste på:

## Metode 1: Quick verify (raskeste)

Kjør test-scriptet for å verifisere at alle 3 steder har riktig lørdag-tid:

```bash
chmod +x test-opening-hours.sh
./test-opening-hours.sh
```

Dette sjekker:
- ✅ Tooltip ID 10198
- ✅ Bricks Template ID 18686
- ✅ Code Snippet ID 7

## Metode 2: Playwright test med mock dato (for client-side testing)

**OBS:** Dette fungerer bare hvis nettsiden bruker JavaScript til å vise åpningstider. Hvis den bruker PHP server-side rendering, vil ikke mocking av Date i browseren fungere.

Installer Playwright først (hvis ikke gjort):
```bash
npm install -D @playwright/test
npx playwright install
```

Kjør test:
```bash
npx playwright test test-saturday-hours.js --headed
```

Dette vil:
- Mocke system-dato til lørdag 11. januar 2026
- Åpne Porsgrunn-siden
- Ta screenshot (`porsgrunn-saturday-test.png`)
- Verifisere at "11:00-14:00" vises (og IKKE "10:00-14:00")

## Metode 3: Legg til test-parameter i snippet (best for testing)

Oppdater Code Snippet ID 7 midlertidig med en test-parameter:

```php
function display_today_opening_hours() {
    $opening_hours = array(
        'Monday' => '08.00 - 17.00',
        'Tuesday' => '08.00 - 17.00',
        'Wednesday' => '08.00 - 17.00',
        'Thursday' => '08.00 – 19.00',
        'Friday' => '08.00 - 17.00',
        'Saturday' => '11.00 - 14.00',
        'Sunday' => 'Stengt',
    );

    // TEST PARAMETER: ?test_day=Saturday
    if (isset($_GET['test_day']) && array_key_exists($_GET['test_day'], $opening_hours)) {
        $current_day = $_GET['test_day'];
    } else {
        $current_day = date('l');
    }

    if (isset($opening_hours[$current_day])) {
        $output = "<p>Åpningstid i dag: " . $opening_hours[$current_day] . "</p>";
    } else {
        $output = "<p>Stengt</p>";
    }

    return $output;
}
add_shortcode('display_opening_hours', 'display_today_opening_hours');
```

Deretter test med:
```
https://autostrada.no/avdelinger/porsgrunn/?test_day=Saturday
```

### Oppdater snippet via WP-CLI:

```bash
ssh kinsta-autostrada "cd public && wp db query \"UPDATE wp_snippets SET code = '<DIN NYE KODE HER>' WHERE id = 7;\""
```

**HUSK Å FJERNE TEST-PARAMETEREN I PRODUKSJON!**

## Metode 4: Bare vent til lørdag 😄

Den mest pålitelige testen er å faktisk vente til lørdag og sjekke nettsiden!

Neste lørdag er: **11. januar 2026**

---

## Hva er allerede verifisert?

✅ Tooltip ID 10198 er oppdatert til 11:00-14:00
✅ Bricks Template ID 18686 har 11.00 - 14.00
✅ Code Snippet ID 7 har 'Saturday' => '11.00 - 14.00'

Alle 3 kilder er korrekte i databasen.
