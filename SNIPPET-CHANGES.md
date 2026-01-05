# Code Snippet Changes (Not in Git)

Code snippets are stored in the WordPress database (`wp_snippets` table) and are not tracked in Git.

## Recent Changes

### 2026-01-05: Snippet ID 7 - Added test_day parameter

**Snippet:** Åpningstider Autostrada Porsgrunn
**Shortcode:** `[display_opening_hours]`

**Change:** Added `?test_day` URL parameter for testing different days without waiting.

**Usage:**
- Normal: `https://autostrada.no/avdeling/autostrada-porsgrunn/` (shows today)
- Test Saturday: `https://autostrada.no/avdeling/autostrada-porsgrunn/?test_day=Saturday`
- Test Sunday: `https://autostrada.no/avdeling/autostrada-porsgrunn/?test_day=Sunday`

**Valid test_day values:** Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, Sunday

**Code:**
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

    // TEST PARAMETER: Add ?test_day=Saturday to URL to simulate different days
    if (isset($_GET['test_day']) && array_key_exists($_GET['test_day'], $opening_hours)) {
        $current_day = $_GET['test_day'];
    } else {
        $current_day = date('l');
    }

    $current_time = date('g:i A');

    $output = '';

    if (isset($opening_hours[$current_day])) {
        $output .= "<p>Åpningstid i dag: " . $opening_hours[$current_day] . "</p>";
    } else {
        $output .= "<p>Stengt</p>";
    }

    return $output;
}
add_shortcode('display_opening_hours', 'display_today_opening_hours');
```

**Reason:** Allow testing of opening hours display for different days without waiting for actual day.

**Backup location:** `/tmp/snippet_7_original.txt` (on server, not permanent)

---

## How to Update Snippets

Snippets are managed in WordPress Admin or via WP-CLI:

### Via WordPress Admin:
1. Login to WordPress
2. Go to Snippets → All Snippets
3. Edit the snippet
4. Update code and save

### Via WP-CLI:
```bash
ssh kinsta-autostrada "cd public && wp db query \"UPDATE wp_snippets SET code = 'YOUR_CODE_HERE' WHERE id = 7;\""
```

**Important:** Always test snippets on staging before deploying to production.
