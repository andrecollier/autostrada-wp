<?php

namespace sircon\finncars;


use sircon\Options;

$prfxso_leasing = false;
if(!empty($item['sales_form']) && $item['sales_form'] == 'Leasing'){
	$prfxso_leasing = true;
	echo "<style>.finn_postsfilters_head.desktop_hidden,.finn_postsfilters_head.desktop_show{display:none !important;}</style>";
}

/* ---------- kontakt-overstyring (dealer lookup) ---------- */
$raw      = Options::get_option( FinnCars::OPTIONSPAGE_ID, 'dealers' );
$dealers  = json_decode( $raw, true ) ?: [];

// Fallback = API data
$customEmail = $item['contact']['email']         ?? '';
$customPhone = $item['contact']['phone']['work'] ?? $item['contact']['phone']['mobile'] ?? '';
$dealerName  = $item['contact']['name']          ?? 'Kontaktinformasjon';

// Rens telefonnummer (fjern +47 og mellomrom)
if ($customPhone) {
    $customPhone = str_replace(['+47', ' ', '-'], '', $customPhone);
    $customPhone = preg_replace('/^47/', '', $customPhone); // Fjern 47 på starten
    // Formater som XX XX XX XX
    if (strlen($customPhone) == 8) {
        $customPhone = substr($customPhone, 0, 2) . ' ' . substr($customPhone, 2, 2) . ' ' . substr($customPhone, 4, 2) . ' ' . substr($customPhone, 6, 2);
    }
}

$matchFound = false;

// DEBUG: Logg hva vi starter med
echo "<!-- DEALER MATCHING DEBUG: API dealer name: '" . $dealerName . "' -->";

// 1) Primært: match på Org ID
$orgId = $item['dealer']['orgId'] ?? null;
if ( $orgId ) {
    echo "<!-- TRYING ORG ID MATCH: API OrgID = '" . $orgId . "' -->";
    foreach ( $dealers as $d ) {
        if ( isset($d['orgId']) && (string)$d['orgId'] === (string)$orgId ) {
            echo "<!-- ORGID MATCH FOUND: " . $d['name'] . " (Config OrgID: " . $d['orgId'] . ") -->";
            $dealerName  = $d['name']  ?? $dealerName;
            if (!empty($d['email'])) $customEmail = $d['email'];
            if (!empty($d['phone'])) $customPhone = $d['phone'];
            $matchFound = true;
            break;
        }
    }
    if (!$matchFound) {
        echo "<!-- NO ORG ID MATCH FOUND FOR: " . $orgId . " -->";
    }
}

// 2) Sekundært: forbedret navn-matching med mer presis logikk
if (!$matchFound && $dealerName) {
    // DEBUG: Vis alle tilgjengelige dealers og deres org IDs
    echo "<!-- AVAILABLE DEALERS IN CONFIG: ";
    foreach ($dealers as $idx => $d) {
        echo "[$idx] Name: '" . ($d['name'] ?? 'N/A') . "', OrgID: '" . ($d['orgId'] ?? 'N/A') . "' | ";
    }
    echo " -->";
    echo "<!-- API DATA: Dealer name: '" . $dealerName . "', OrgID: '" . ($orgId ?? 'N/A') . "' -->";
    
    foreach ( $dealers as $d ) {
        $dealerConfigName = $d['name'] ?? '';
        $apiDealerName = $dealerName;
        
        echo "<!-- CHECKING: '" . $apiDealerName . "' vs '" . $dealerConfigName . "' (Config OrgID: " . ($d['orgId'] ?? 'N/A') . ") -->";
        
        $matches = false;
        
        // Normaliserer navnene for sammenligning (fjerner ekstra mellomrom, gjør til lowercase)
        $normalizedApiName = strtolower(trim($apiDealerName));
        $normalizedConfigName = strtolower(trim($dealerConfigName));
        
        // SPESIFIKKE MATCHES - mer presise regler
        
        // 0. Spesifikke person-navn som representerer avdelinger
        if ($normalizedApiName === 'geir arne svartdal' && stripos($normalizedConfigName, 'notodden') !== false) {
            $matches = true;
            echo "<!-- MATCH: Geir Arne Svartdal -> Notodden -->";
        }
        
        // 1. Porsche Center (må matche først for å unngå konflikt med Porsgrunn)
        elseif ((stripos($normalizedApiName, 'porsche') !== false && stripos($normalizedApiName, 'center') !== false) && 
            (stripos($normalizedConfigName, 'porsche') !== false && stripos($normalizedConfigName, 'center') !== false)) {
            $matches = true;
            echo "<!-- MATCH: Porsche Center -->";
        }
        
        // 2. Autostrada Porsgrunn (ikke Porsche Center)
        elseif ((stripos($normalizedApiName, 'porsgrunn') !== false && stripos($normalizedApiName, 'porsche') === false) && 
                (stripos($normalizedConfigName, 'porsgrunn') !== false && stripos($normalizedConfigName, 'porsche') === false)) {
            $matches = true;
            echo "<!-- MATCH: Autostrada Porsgrunn -->";
        }
        
        // 3. Autostrada Arendal
        elseif ((stripos($normalizedApiName, 'arendal') !== false) && 
                (stripos($normalizedConfigName, 'arendal') !== false)) {
            $matches = true;
            echo "<!-- MATCH: Arendal -->";
        }
        
        // 4. Autostrada Notodden
        elseif ((stripos($normalizedApiName, 'notodden') !== false) && 
                (stripos($normalizedConfigName, 'notodden') !== false)) {
            $matches = true;
            echo "<!-- MATCH: Notodden -->";
        }
        
        // 5. Autostrada Seljord
        elseif ((stripos($normalizedApiName, 'seljord') !== false) && 
                (stripos($normalizedConfigName, 'seljord') !== false)) {
            $matches = true;
            echo "<!-- MATCH: Seljord -->";
        }
        
        // 6. Autostrada Kongsberg ELLER API navnet er "Salgsavdeling" (som ofte refererer til Kongsberg)
        elseif (((stripos($normalizedApiName, 'kongsberg') !== false) || (stripos($normalizedApiName, 'salgsavdeling') !== false)) && 
                (stripos($normalizedConfigName, 'kongsberg') !== false)) {
            $matches = true;
            echo "<!-- MATCH: Kongsberg/Salgsavdeling -->";
        }
        
        // 7. Autostrada X / Xpeng
        elseif (((stripos($normalizedApiName, 'xpeng') !== false) || (stripos($normalizedApiName, ' x ') !== false) || (stripos($normalizedApiName, 'autostrada x') !== false)) && 
                (stripos($normalizedConfigName, 'x') !== false && stripos($normalizedConfigName, 'autostrada') !== false)) {
            $matches = true;
            echo "<!-- MATCH: X/Xpeng -->";
        }
        
        // 8. Fallback: Eksakt match på org ID hvis det finnes i API dataene
        // Dette bør egentlig ha fungert i første omgang, men som backup
        elseif (isset($item['dealer']['orgId']) && isset($d['orgId']) && 
                (string)$item['dealer']['orgId'] === (string)$d['orgId']) {
            $matches = true;
            echo "<!-- MATCH: Fallback OrgID match -->";
        }
        
        if ($matches) {
            echo "<!-- NAME MATCH FOUND: " . $dealerConfigName . " -->";
            $dealerName = $d['name'];
            if (!empty($d['email'])) $customEmail = $d['email'];
            if (!empty($d['phone'])) $customPhone = $d['phone'];
            $matchFound = true;
            break;
        }
    }
}

// DEBUG: Hvis ingen match ble funnet, logg hva vi har
if (!$matchFound) {
    echo "<!-- NO MATCH FOUND FOR: API='" . $dealerName . "', OrgID='" . ($item['dealer']['orgId'] ?? 'N/A') . "' -->";
    echo "<!-- WILL USE DEFAULT API VALUES -->";
}

echo "<!-- FINAL RESULT: '" . $dealerName . "', '" . $customEmail . "', '" . $customPhone . "' -->";
/* ---------- /kontakt-overstyring ---------- */
?>

<script>
// AGGRESSIV OVERSKRIVELSE av hardkodet kontaktinfo
(function() {
    console.log('=== STARTING AGGRESSIVE CONTACT OVERRIDE ===');
    
    // VÅRE DYNAMISKE VERDIER
    var email = "<?php echo $customEmail; ?>";
    var phone = "<?php echo $customPhone; ?>";
    var dealerName = "<?php echo $dealerName; ?>";
    
    console.log('Using values:', {email, phone, dealerName});
    
    function replaceContactInfo() {
        console.log('Running contact replacement...');
        
        // 1. Erstatt alle email-linker
        document.querySelectorAll('a[href*="volvo@autostrada.com"]').forEach(function(el) {
            console.log('Replacing email link:', el.href);
            el.href = 'mailto:' + email;
            el.textContent = email;
        });
        
        // 2. Erstatt alle telefon-linker  
        document.querySelectorAll('a[href*="35505000"], a[href*="35 50 50 00"]').forEach(function(el) {
            console.log('Replacing phone link:', el.href);
            el.href = 'tel:' + phone.replace(/\s/g, '');
            el.textContent = phone;
        });
        
        // 3. Erstatt ren tekst som inneholder email
        document.querySelectorAll('*').forEach(function(el) {
            if (el.children.length === 0 && el.textContent.includes('volvo@autostrada.com')) {
                console.log('Replacing email text in:', el.textContent);
                el.textContent = el.textContent.replace('volvo@autostrada.com', email);
            }
            if (el.children.length === 0 && el.textContent.includes('35 50 50 00')) {
                console.log('Replacing phone text in:', el.textContent);
                el.textContent = el.textContent.replace('35 50 50 00', phone);
            }
        });
        
        // 4. Erstatt alle kontaktboks-overskrifter
        document.querySelectorAll('.sfc-meta h2').forEach(function(el) {
            if (el.textContent.includes('Kontaktinformasjon')) {
                console.log('Replacing contact header');
                el.textContent = dealerName;
            }
        });
        
        // 5. Spesielt for footer-knapper
        document.querySelectorAll('.footer-cta-buttons a').forEach(function(link) {
            if (link.href.includes('tel:')) {
                link.href = 'tel:' + phone.replace(/\s/g, '');
            }
            if (link.href.includes('mailto:')) {
                link.href = 'mailto:' + email;
            }
        });
        
        // 6. Forbedre knapp-tekster og styling
        document.querySelectorAll('.red-button').forEach(function(btn) {
            if (btn.href.includes('tel:')) {
                btn.textContent = 'Ring oss';
                btn.style.borderRadius = '24px';
            }
        });
        
        document.querySelectorAll('.white-button').forEach(function(btn) {
            if (btn.href.includes('mailto:')) {
                btn.textContent = 'Send oss en e-post';
                btn.style.borderRadius = '24px';
            }
        });
        
        console.log('Contact replacement completed');
    }
    
    // Kjør umiddelbart
    replaceContactInfo();
    
    // Kjør når DOM er klar
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', replaceContactInfo);
    }
    
    // Kjør når siden er ferdig lastet
    window.addEventListener('load', function() {
        setTimeout(replaceContactInfo, 100);
        setTimeout(replaceContactInfo, 500);
        setTimeout(replaceContactInfo, 1000);
    });
    
    // Kontinuerlig overvåkning
    setInterval(function() {
        if (document.querySelector('a[href*="volvo@autostrada.com"]') || 
            (document.body.textContent && document.body.textContent.includes('volvo@autostrada.com'))) {
            console.log('Hardcoded values detected again, replacing...');
            replaceContactInfo();
        }
    }, 2000);
    
    // SKJUL DUPLIKAT KONTAKTBOKS PÅ MOBIL
    function hideDuplicateContactBox() {
        if (window.innerWidth <= 768) {
            console.log('Hiding duplicate contact box on mobile...');
            
            // Find alle kontaktbokser
            var contactBoxes = document.querySelectorAll('.sfc-meta[data-sfc-contact]');
            console.log('Found', contactBoxes.length, 'contact boxes');
            
            // Skjul den siste/nederste kontaktboksen
            if (contactBoxes.length > 1) {
                var lastContactBox = contactBoxes[contactBoxes.length - 1];
                lastContactBox.style.display = 'none';
                lastContactBox.style.visibility = 'hidden';
                lastContactBox.style.opacity = '0';
                lastContactBox.style.height = '0';
                lastContactBox.style.overflow = 'hidden';
                console.log('Hidden the last contact box');
            }
            
            // Alternativ: Skjul kontaktbokser med open_once klassen
            var openOnceBoxes = document.querySelectorAll('.sfc-meta.open_once[data-sfc-contact]');
            openOnceBoxes.forEach(function(box) {
                box.style.display = 'none';
                console.log('Hidden open_once contact box');
            });
            
            // FIKS CTA-KNAPPER TEKST PÅ MOBIL - MER AGGRESSIV
            var emailButtons = document.querySelectorAll('.footer-cta-buttons .black-button, .footer-cta-buttons a[href*="mailto:"]');
            emailButtons.forEach(function(btn) {
                if (btn.href && btn.href.includes('mailto:') && (btn.textContent.includes('@') || btn.textContent.length > 15)) {
                    btn.textContent = 'Send e-post';
                    console.log('Fixed email button text to: Send e-post');
                }
            });
            
            // FIKS OGSÅ ANDRE EMAIL-KNAPPER
            var allEmailLinks = document.querySelectorAll('a[href*="mailto:"]');
            allEmailLinks.forEach(function(link) {
                if (link.classList.contains('black-button') || link.classList.contains('tracking-email-us-footer')) {
                    if (link.textContent.includes('@') || link.textContent.length > 15) {
                        link.textContent = 'Send e-post';
                        console.log('Fixed email link text');
                    }
                }
            });
        }
    }
    
    // Kjør umiddelbart og ved resize
    hideDuplicateContactBox();
    window.addEventListener('resize', hideDuplicateContactBox);
    window.addEventListener('load', function() {
        setTimeout(hideDuplicateContactBox, 500);
        setTimeout(hideDuplicateContactBox, 1000);
        setTimeout(hideDuplicateContactBox, 2000);
    });
    
    // KONTINUERLIG OVERVÅKNING AV CTA-KNAPPER - BÅDE MOBIL OG DESKTOP
    setInterval(function() {
        // FIKS EMAIL-KNAPPER PÅ BÅDE MOBIL OG DESKTOP
        var emailButtons = document.querySelectorAll('.footer-cta-buttons a[href*="mailto:"], .black-button[href*="mailto:"]');
        emailButtons.forEach(function(btn) {
            if (btn.textContent.includes('@') || btn.textContent.length > 15) {
                btn.textContent = 'Send e-post';
                console.log('Fixed email button text to: Send e-post');
            }
        });
        
        // SPESIELT FOR DESKTOP - også sjekk tracking-email-us-footer klassen
        var footerEmailButtons = document.querySelectorAll('.tracking-email-us-footer');
        footerEmailButtons.forEach(function(btn) {
            if (btn.href && btn.href.includes('mailto:') && (btn.textContent.includes('@') || btn.textContent.length > 15)) {
                btn.textContent = 'Send e-post';
                console.log('Fixed footer email button text');
            }
        });
    }, 1000);
    
})();
</script>

<style>
/* Forbedret styling for kontaktknapper og mobil layout */
.red-button, .white-button {
    border-radius: 24px !important;
    font-weight: 600 !important;
    text-align: center !important;
    text-decoration: none !important;
    display: inline-block !important;
    padding: 12px 20px !important;
    font-size: 16px !important;
}

.red-button {
    background-color: #cb0d2a !important;
    color: white !important;
    border: none !important;
}

.white-button {
    background-color: white !important;
    color: #cb0d2a !important;
    border: 2px solid #cb0d2a !important;
}

.red-button:hover, .white-button:hover {
    opacity: 0.9 !important;
}

/* Fiks hvit gap på mobil */
@media (max-width: 768px) {
    /* SPESIFIKK CSS FOR DENNE SIDEN - FIKS PADDING/MARGIN */
    body.page-id-374 .sfc-archive.alignwide, 
    body.page-id-374 .sfc-single {
        margin: 40px auto !important;
        max-width: 1440px !important;
        padding-left: 20px !important;
        padding-right: 20px !important;
    }
    
    .sfc-single {
        background-color: #f5f5f5 !important;
    }
    
    .sfc-meta.sfc_side_meta {
        background-color: #fefcf7 !important;
        margin-bottom: 0 !important;
    }
    
    .sfc-single > div {
        margin-bottom: 0 !important;
    }
    
    .sfc-meta {
        background-color: #fefcf7 !important;
        border-radius: 15px !important;
        margin-bottom: 10px !important;
    }
    
    /* SKJUL KONTAKTBOKSER MED DISSE KLASSENE PÅ MOBIL */
    .mobile-hidden {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        height: 0 !important;
        overflow: hidden !important;
    }
    
    .hide-on-mobile {
        display: none !important;
    }
    
    .desktop_show[data-sfc-contact] {
        display: none !important;
    }
    
    .sfc-meta.open_once[data-sfc-contact] {
        display: none !important;
    }
}

/* Fiks CTA-knapper på mobil - MER SPESIFIKK CSS */
@media (max-width: 768px) {
    .footer-cta-buttons a {
        display: block !important;
        padding: 12px 20px !important;
        font-size: 16px !important;
        font-weight: 600 !important;
        text-align: center !important;
        text-decoration: none !important;
        border-radius: 12px !important;
        margin: 0 5px !important;
    }
    
    .footer-cta-buttons .red-button {
        background-color: #cb0d2a !important;
        color: white !important;
        border: none !important;
    }
    
    .footer-cta-buttons .black-button {
        background-color: black !important;
        color: white !important;
        border: none !important;
    }
    
    .footer-cta-buttons {
        flex-direction: row !important;
        gap: 10px !important;
    }
}
</style>

<script>
	/*
	<?php //var_dump($item); ?>
	*/
</script>
<div class="sfc-single">

	<div class="column_a">
		<?php if( $item['images'] !== NULL){ ?>
		<div class="sfc-slideshow" data-slides="<?php echo count($item['images']); ?>">
			<?php foreach ((array) $item['images'] as $index => $media) { ?>
				<div class="sfc-slide<?php echo ($index === 0) ? ' current' : ''; ?>" data-index="<?php echo ($index + 1); ?>">
					<figure style="background-image:url(<?php echo $media['url']; ?>);">
						<img src="<?php echo $media['url']; ?>" alt="Slide image" />
						<!--<figcaption><?php echo $media['txt']; ?></figcaption>-->
					</figure>
				</div>
			<?php } ?>
			<div class="slideshow-nav">
				<button class="nav-btn nav-prev" data-direction="prev"></button>
				<button class="nav-btn nav-next" data-direction="next"></button>
			</div>
		</div>
	<?php }else{
	echo "<center style='color:red'>Ingen bilder funnet</center>";
} ?>
		<div class="related_above_information_container">
			<?php if ($item['sold'] === 'true') { ?>
				<div class="status-label-sold"><?php _e('Sold', 'sircon-finn-cars'); ?></div>
			<?php } ?>
			<h1><?php echo $item['title']; ?></h1>
			<div class="sfc-summary">
				<?php echo wpautop($item['summary']);?>
			</div>
			<?php if (!empty($item['price']['main']) && !$prfxso_leasing) {

				?>
				<div class="sfc-price">
					<h4><?php _e('TOTALPRIS:', 'sircon-finn-cars'); ?> <br /><span class="item_amount"><?php echo number_format($item['price']['main'], 0, '', ' '); ?></span>,-</h4>
				</div>
				<div class="sfc-price">
					<h4><?php _e('M&#197;NEDLIG:', 'sircon-finn-cars'); ?> <br /><span class="item_month_amount">0</span>,-</h4>
				</div>

			<?php }else{
				?>
				<div class="sfc-price">
					<h4><?php _e('Månedspris:', 'sircon-finn-cars'); ?> <br /><span><?php echo number_format($item['price']['lease_price_monthly'], 0, '', ' '); ?></span>,-</h4>
				</div>
				<div class="sfc-price">
					<h4><?php _e('Innskudd', 'sircon-finn-cars'); ?> <br /><span><?php echo number_format($item['price']['lease_price_initial'], 0, '', ' '); ?></span>,-</h4>
				</div>
			<?php } ?>

			<div class="sfc-registration_fee">
				Omregistrering 4.034,- (avgifter) / Pris eks omreg. <?php
					$pwr = ( intval($item['price']['main']) - 4034 );
					echo number_format($pwr, 0, '', '.');
				?>,-
			</div>
		</div>
		<div class="column_b">
			<div class="finn_postsfilters_head desktop_hidden">
				<div class="finn_postsfilters_head_label">
					<h3>L&#229;nekalkulator</h3><br />
					<span>Endre verdiene for å kalkulere veiledende m&#229;nedspris.*</span>
				</div>
				<div class="finn_postsfilters_head_slide_one">
					<div class="gb_rangeslider_new_container">
					<label>Kontantbel&#248;p:</label> <span id="amount_kontant">150000</span> <span>,-</span><br />
					<div class="calculate_percentage">EGENKAPITAL: <span id="amount_percentage">0</span>%</div>
					<input id="finn_slider_one_new" type="range" min="0" max="10" value="150000" step="1">
					</div>
					<!--<div id="finn_slider_one"></div>-->
				</div>
				<div class="finn_postsfilters_head_slide_two">
					<div class="gb_rangeslider_new_container">
						<label>Nedbetalingstid: </label> <span id="amount_kontant">10</span> <span>&#229;r</span><br />
						<input id="finn_slider_two_new" type="range" min="0" max="10" value="10" step="1"><br />
					</div>
				</div>
				<label class="lab">Nominell rente: <span id="interest_rate"><?php echo  number_format(get_option('gb_sultan_billink_interest_rate', true), 2, '.', ''); ?></span> <span>%</span></label>
				<label class="lab">Du m&#229; l&#229;ne: <span>kr</span> <span id="amount_left">0</span> <span>,-</span></label><br />
				<small>* = Kalkulatoren er veiledene</small>
			</div>
			
			<div class="sfc-meta desktop_hidden sfc_side_meta" data-sfc-contact>
                <h2><?php echo esc_html( $dealerName ); ?></h2>
               <ul style="display:block;">
                    <?php if ($customEmail): ?>
                        <li><strong>E-post:</strong>
                            <a href="mailto:<?php echo esc_attr($customEmail); ?>">
                                <?php echo esc_html($customEmail); ?>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if ($customPhone): ?>
                        <li><strong>Telefon:</strong>
                            <a href="tel:<?php echo esc_attr($customPhone); ?>">
                                <?php echo esc_html($customPhone); ?>
                            </a>
                        </li>
                    <?php endif; ?>

                    <li style="margin-top:15px;">
                        <div style="display:flex;width: 100%; gap:5px;">
                            <?php if ($customPhone): ?>
                                <a href="tel:<?php echo esc_attr($customPhone); ?>" class="red-button tracking-call-us-mobile" style="flex:1;">
                                    Ring oss
                                </a>
                            <?php endif; ?>
                            <?php if ($customEmail): ?>
                                <a href="mailto:<?php echo esc_attr($customEmail); ?>" class="white-button tracking-email-us-mobile" style="flex:1;">
                                    Send oss en e-post
                                </a>
                            <?php endif; ?>
                        </div>
                    </li>
                </ul>
            </div>
			
			<div class="sfc-meta desktop_hidden sfc_side_meta">
				<h2><?php _e('Spesifikasjoner', 'sircon-finn-cars'); ?></h2>

				<ul <?php if($prfxso_leasing){ echo 'style="display:block;"';} ?>>
					<li>
						<?php if (!empty($item['year'])) { ?>
							<span class="sfc-meta-label"><?php _e('Year', 'sircon-finn-cars'); ?></span>
							<span class="sfc-meta-value"><?php echo $item['year']; ?></span>
						<?php } ?>
					</li>
					<?php if (!empty($item['registration_number'])) { ?>
    					<li>
    							<span class="sfc-meta-label"><?php _e('Reg.nr.', 'sircon-finn-cars'); ?></span>
    							<span class="sfc-meta-value"><?php echo $item['registration_number']; ?></span>
    					</li>
    				<?php }else{
    					?>
    					<li>
    							<span class="sfc-meta-label"><?php _e('Reg.nr.', 'sircon-finn-cars'); ?></span>
    							<span class="sfc-meta-value"><?php echo '-'; ?></span>
    					</li>
    					<?php
    				} ?>
					<li>
						<?php if (!empty($item['first_registration'])) { ?>
							<span class="sfc-meta-label"><?php _e('First registration', 'sircon-finn-cars'); ?></span>
							<span class="sfc-meta-value"><?php echo $item['first_registration']; ?></span>
						<?php } ?>
					</li>
					<li>
						<?php if (!empty($item['mileage'])) { ?>
							<span class="sfc-meta-label"><?php _e('Mileage', 'sircon-finn-cars'); ?></span>
							<span class="sfc-meta-value"><?php echo number_format($item['mileage'], 0, '', ' '); ?> km</span>
						<?php } ?>
					</li>
					<li>
						<?php if (!empty($item['exterior_color'])) { ?>
							<span class="sfc-meta-label"><?php _e('Exterior color', 'sircon-finn-cars'); ?></span>
							<span class="sfc-meta-value"><?php echo $item['exterior_color']; ?></span>
						<?php } ?>
					</li>
					<li>
						<?php if (!empty($item['interior_color'])) { ?>
							<span class="sfc-meta-label"><?php _e('Interior color', 'sircon-finn-cars'); ?></span>
							<span class="sfc-meta-value"><?php echo $item['interior_color']; ?></span>
						<?php } ?>
					</li>
					<li>
						<?php if (!empty($item['transmission'])) { ?>
							<span class="sfc-meta-label"><?php _e('Transmission', 'sircon-finn-cars'); ?></span>
							<span class="sfc-meta-value"><?php echo $item['transmission']; ?></span>
						<?php } ?>
					</li>
					<li>
						<?php if (!empty($item['wheel_drive'])) { ?>
							<span class="sfc-meta-label"><?php _e('Wheel drive', 'sircon-finn-cars'); ?></span>
							<span class="sfc-meta-value"><?php echo $item['wheel_drive']; ?></span>
						<?php } ?>
					</li>
					<li>
						<?php if (!empty($item['engine']['fuel'])) { ?>
							<span class="sfc-meta-label"><?php _e('Fuel', 'sircon-finn-cars'); ?></span>
							<span class="sfc-meta-value"><?php echo $item['engine']['fuel']; ?></span>
						<?php } ?>
					</li>
					<li>
						<?php if (!empty($item['engine']['effect'])) { ?>
							<span class="sfc-meta-label"><?php _e('Effect', 'sircon-finn-cars'); ?></span>
							<span class="sfc-meta-value"><?php echo $item['engine']['effect']; ?></span>
						<?php } ?>
					</li>
					<li>
						<?php if (!empty($item['engine']['volume'])) { ?>
							<span class="sfc-meta-label"><?php _e('Cylinder volume', 'sircon-finn-cars'); ?></span>
							<span class="sfc-meta-value"><?php echo $item['engine']['volume']; ?></span>
						<?php } ?>
					</li>
					<li>
						<?php if (!empty($item['weight']['main'])) { ?>
							<span class="sfc-meta-label"><?php _e('Weight', 'sircon-finn-cars'); ?></span>
							<span class="sfc-meta-value"><?php echo number_format($item['weight']['main'], 0, '', ' '); ?> kg</span>
						<?php } ?>
					</li>
					<li>
						<?php if (!empty($item['co2_emissions'])) { ?>
							<span class="sfc-meta-label"><?php _e('CO2 emissions', 'sircon-finn-cars'); ?></span>
							<span class="sfc-meta-value"><?php echo $item['co2_emissions'] ?> g/km</span>
						<?php } ?>
					</li>
					<li>
						<?php if (!empty($item['seats'])) { ?>
							<span class="sfc-meta-label"><?php _e('Seats', 'sircon-finn-cars'); ?></span>
							<span class="sfc-meta-value"><?php echo $item['seats'] ?></span>
						<?php } ?>
					</li>
					<li>
						<?php if (!empty($item['body_type'])) { ?>
							<span class="sfc-meta-label"><?php _e('Body type', 'sircon-finn-cars'); ?></span>
							<span class="sfc-meta-value"><?php echo $item['body_type'] ?></span>
						<?php } ?>
					</li>
					<li>
						<?php if (!empty($item['doors'])) { ?>
							<span class="sfc-meta-label"><?php _e('Doors', 'sircon-finn-cars'); ?></span>
							<span class="sfc-meta-value"><?php echo $item['doors'] ?></span>
						<?php } ?>
					</li>
					<li>
						<?php if (!empty($item['trunk_size'])) { ?>
							<span class="sfc-meta-label"><?php _e('Trunk size', 'sircon-finn-cars'); ?></span>
							<span class="sfc-meta-value"><?php echo $item['trunk_size'] ?></span>
						<?php } ?>
					</li>
					<li>
						<?php if (!empty($item['car_location'])) { ?>
							<span class="sfc-meta-label"><?php _e('Car location', 'sircon-finn-cars'); ?></span>
							<span class="sfc-meta-value"><?php echo $item['car_location'] ?></span>
						<?php } ?>
					</li>
					<li>
						<?php if (!empty($item['sales_form'])) { ?>
							<span class="sfc-meta-label"><?php _e('Sales form', 'sircon-finn-cars'); ?></span>
							<span class="sfc-meta-value"><?php echo $item['sales_form'] ?></span>
						<?php } ?>
					</li>
					<li>
						<?php if (!empty($item['registration_class'])) { ?>
							<span class="sfc-meta-label"><?php _e('Tax class', 'sircon-finn-cars'); ?></span>
							<span class="sfc-meta-value"><?php echo $item['registration_class'] ?></span>
						<?php } ?>
					</li>
				</ul>
			</div>
		    
			<div class="sfc-equipment desktop_hidden sfc_side_meta">
				<?php
				if (!empty($item['equipment'])) {
					echo '<h2>' . __('Equipment', 'sircon-finn-cars') . '</h2>';
					echo '<ul>';
					foreach ((array) $item['equipment'] as $equipment) {
						echo '<li>' . $equipment . '</li>';
					}

					echo '</ul>';
				}
				?>
			</div>
		</div>

		<div class="related_informations">
			<?php

			if( array_key_exists('year',$item) ){

				?>
				<div class="related_information_item">
					<div class="related_information_item_icon">
						<img src="<?php echo WP_PLUGIN_URL_ME; ?>/sircon-finn-cars/images/calender.png" class="related_information_item_img" />
					</div>
					<div class="related_information_item_text">
						<label class="related_information_item_label">MODELL&#197;R</label>
						<span class="related_information_item_info"><?php echo $item['year'];?></span>
					</div>
				</div>
				<?php
			}
			?>

			<?php
			if( array_key_exists('warranty_distance',$item) ){

				?>
				<div class="related_information_item">
					<div class="related_information_item_icon">
						<img src="<?php echo WP_PLUGIN_URL_ME; ?>/sircon-finn-cars/images/meter.png" class="related_information_item_img" />
					</div>
					<div class="related_information_item_text">
						<label class="related_information_item_label">KILOMETER</label>
						<span class="related_information_item_info"><?php echo number_format($item['mileage'], 0, '', ' '); ?> km</span>
					</div>
				</div>
				<?php
			}
			?>

			<?php
			if( array_key_exists('transmission',$item) ){

				?>
				<div class="related_information_item">
					<div class="related_information_item_icon">
						<img src="<?php echo WP_PLUGIN_URL_ME; ?>/sircon-finn-cars/images/type.png" class="related_information_item_img" />
					</div>
					<div class="related_information_item_text">
						<label class="related_information_item_label">GIRKASSE</label>
						<span class="related_information_item_info"><?php echo $item['transmission'];?></span>
					</div>
				</div>
				<?php
			}
			?>
			<br class="desktop_show"/><br class="desktop_show" />
			<?php
			if( array_key_exists('engine',$item) ){
				if( array_key_exists('fuel',$item['engine']) ){
					?>
					<div class="related_information_item">
						<div class="related_information_item_icon">
							<img src="<?php echo WP_PLUGIN_URL_ME; ?>/sircon-finn-cars/images/fuel.png" class="related_information_item_img" />
						</div>
						<div class="related_information_item_text">
							<label class="related_information_item_label">DRIVSTOFF</label>
							<span class="related_information_item_info"><?php echo $item['engine']['fuel'];?></span>
						</div>
					</div>
					<?php
				}
			}
			?>

			<?php
			if( array_key_exists('wheel_drive',$item) ){

				?>
				<div class="related_information_item">
					<div class="related_information_item_icon">
						<img src="<?php echo WP_PLUGIN_URL_ME; ?>/sircon-finn-cars/images/suspension.png" class="related_information_item_img" />
					</div>
					<div class="related_information_item_text">
						<label class="related_information_item_label">HJULDRIFT</label>
						<span class="related_information_item_info"><?php echo $item['wheel_drive'];?></span>
					</div>
				</div>
				<?php
			}
			?>

			<?php
			if( array_key_exists('engine',$item) ){
				if( array_key_exists('effect',$item['engine']) ){

					?>
					<div class="related_information_item">
						<div class="related_information_item_icon">
							<img src="<?php echo WP_PLUGIN_URL_ME; ?>/sircon-finn-cars/images/power.png" class="related_information_item_img" />
						</div>
						<div class="related_information_item_text">
							<label class="related_information_item_label">EFFEKT</label>
							<span class="related_information_item_info"><?php echo $item['engine']['effect'];?> HK</span>
						</div>
					</div>
					<?php
				}
			}
			?>
		</div>

		<div class="related_above_information_container">
			<div class="sfc_guarantee">
				<b>Garanti:</b> <?php echo $item['warranty_summary']; ?>
			</div>

			<div class="sfc-description">
				<h2><?php _e('Description', 'sircon-finn-cars'); ?></h2>
				<?php echo wpautop($item['description']);?>
			</div>
			<div class="sfc-finn-id">
				<?php _e('FINN-code', 'sircon-finn-cars'); ?>: <?php echo $item['id']; ?>
			</div>
			<div class="sfc-external-ref">
				<?php _e('Reference', 'sircon-finn-cars'); ?>: <?php echo $item['external_ref']; ?>
			</div>
		</div>
	</div>
	<div class="column_b">
		<div class="finn_postsfilters_head desktop_show">
			<div class="finn_postsfilters_head_label">
				<h3>L&#229;nekalkulator</h3><br />
				<span>Endre verdiene for å kalkulere veiledende m&#229;nedspris.*</span>
			</div>
			<div class="finn_postsfilters_head_slide_one">
				<div class="gb_rangeslider_new_container">
				<label>Kontantbel&#248;p:</label> <span id="amount_kontant">150000</span> <span>,-</span><br />
				<div class="calculate_percentage">EGENKAPITAL: <span id="amount_percentage">0</span>%</div>
				<input id="finn_slider_one" type="range" min="0" max="10" value="150000" step="5000">
				</div>
				<!--<div id="finn_slider_one"></div>-->
			</div>
			<div class="finn_postsfilters_head_slide_two">
				<div class="gb_rangeslider_new_container">
					<label>Nedbetalingstid: </label> <span id="amount_kontant">10</span> <span>&#229;r</span><br />
					<input id="finn_slider_two" type="range" min="0" max="10" value="10" step="1"><br />
					<label>Nominell rente: <span id="interest_rate"><?php echo  number_format(get_option('gb_sultan_billink_interest_rate', true), 2, '.', ''); ?></span> <span>%</span></label>
					<label>Du m&#229; l&#229;ne: <span>kr</span> <span id="amount_left">0</span> <span>,-</span></label><br />
					<small>* = Kalkulatoren er veiledene</small>
				</div>
			</div>
		</div>
		
		<div class="sfc-meta desktop_show sfc_side_meta hide-on-mobile mobile-hidden" data-sfc-contact>
            <h2><?php echo esc_html( $dealerName ); ?></h2>
           <ul style="display:block;">
                    <?php if ($customEmail): ?>
                        <li><strong>E-post:</strong>
                            <a href="mailto:<?php echo esc_attr($customEmail); ?>">
                                <?php echo esc_html($customEmail); ?>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if ($customPhone): ?>
                        <li><strong>Telefon:</strong>
                            <a href="tel:<?php echo esc_attr($customPhone); ?>">
                                <?php echo esc_html($customPhone); ?>
                            </a>
                        </li>
                    <?php endif; ?>

                    <li style="margin-top:15px;">
                        <div style="display:flex;width: 100%; gap:5px;">
                            <?php if ($customPhone): ?>
                                <a href="tel:<?php echo esc_attr($customPhone); ?>" class="red-button tracking-call-us-desktop" style="flex:1;">
                                    Ring oss
                                </a>
                            <?php endif; ?>
                            <?php if ($customEmail): ?>
                                <a href="mailto:<?php echo esc_attr($customEmail); ?>" class="white-button tracking-email-us-desktop" style="flex:1;">
                                    Send oss en e-post
                                </a>
                            <?php endif; ?>
                        </div>
                    </li>
                </ul>
        </div>
		
		<div class="sfc-meta desktop_show sfc_side_meta">
			<h2><?php _e('Spesifikasjoner', 'sircon-finn-cars'); ?></h2>
			<ul>
				<li>
					<?php if (!empty($item['year'])) { ?>
						<span class="sfc-meta-label"><?php _e('Year', 'sircon-finn-cars'); ?></span>
						<span class="sfc-meta-value"><?php echo $item['year']; ?></span>
					<?php } ?>
				</li>

				<?php if (!empty($item['registration_number'])) { ?>
					<li>
							<span class="sfc-meta-label"><?php _e('Reg.nr.', 'sircon-finn-cars'); ?></span>
							<span class="sfc-meta-value"><?php echo $item['registration_number']; ?></span>
					</li>
				<?php }else{
					?>
					<li>
							<span class="sfc-meta-label"><?php _e('Reg.nr.', 'sircon-finn-cars'); ?></span>
							<span class="sfc-meta-value"><?php echo '-'; ?></span>
					</li>
					<?php
				} ?>

				<li>
					<?php if (!empty($item['first_registration'])) { ?>
						<span class="sfc-meta-label"><?php _e('First registration', 'sircon-finn-cars'); ?></span>
						<span class="sfc-meta-value"><?php echo $item['first_registration']; ?></span>
					<?php } ?>
				</li>
				<li>
					<?php if (!empty($item['mileage'])) { ?>
						<span class="sfc-meta-label"><?php _e('Mileage', 'sircon-finn-cars'); ?></span>
						<span class="sfc-meta-value"><?php echo number_format($item['mileage'], 0, '', ' '); ?> km</span>
					<?php } ?>
				</li>
				<li>
					<?php if (!empty($item['exterior_color'])) { ?>
						<span class="sfc-meta-label"><?php _e('Exterior color', 'sircon-finn-cars'); ?></span>
						<span class="sfc-meta-value"><?php echo $item['exterior_color']; ?></span>
					<?php } ?>
				</li>
				<li>
					<?php if (!empty($item['interior_color'])) { ?>
						<span class="sfc-meta-label"><?php _e('Interior color', 'sircon-finn-cars'); ?></span>
						<span class="sfc-meta-value"><?php echo $item['interior_color']; ?></span>
					<?php } ?>
				</li>
				<li>
					<?php if (!empty($item['transmission'])) { ?>
						<span class="sfc-meta-label"><?php _e('Transmission', 'sircon-finn-cars'); ?></span>
						<span class="sfc-meta-value"><?php echo $item['transmission']; ?></span>
					<?php } ?>
				</li>
				<li>
					<?php if (!empty($item['wheel_drive'])) { ?>
						<span class="sfc-meta-label"><?php _e('Wheel drive', 'sircon-finn-cars'); ?></span>
						<span class="sfc-meta-value"><?php echo $item['wheel_drive']; ?></span>
					<?php } ?>
				</li>
				<li>
					<?php if (!empty($item['engine']['fuel'])) { ?>
						<span class="sfc-meta-label"><?php _e('Fuel', 'sircon-finn-cars'); ?></span>
						<span class="sfc-meta-value"><?php echo $item['engine']['fuel']; ?></span>
					<?php } ?>
				</li>
				<li>
					<?php if (!empty($item['engine']['effect'])) { ?>
						<span class="sfc-meta-label"><?php _e('Effect', 'sircon-finn-cars'); ?></span>
						<span class="sfc-meta-value"><?php echo $item['engine']['effect']; ?></span>
					<?php } ?>
				</li>
				<li>
					<?php if (!empty($item['engine']['volume'])) { ?>
						<span class="sfc-meta-label"><?php _e('Cylinder volume', 'sircon-finn-cars'); ?></span>
						<span class="sfc-meta-value"><?php echo $item['engine']['volume']; ?></span>
					<?php } ?>
				</li>
				<li>
					<?php if (!empty($item['weight']['main'])) { ?>
						<span class="sfc-meta-label"><?php _e('Weight', 'sircon-finn-cars'); ?></span>
						<span class="sfc-meta-value"><?php echo number_format($item['weight']['main'], 0, '', ' '); ?> kg</span>
					<?php } ?>
				</li>
				<li>
					<?php if (!empty($item['co2_emissions'])) { ?>
						<span class="sfc-meta-label"><?php _e('CO2 emissions', 'sircon-finn-cars'); ?></span>
						<span class="sfc-meta-value"><?php echo $item['co2_emissions'] ?> g/km</span>
					<?php } ?>
				</li>
				<li>
					<?php if (!empty($item['seats'])) { ?>
						<span class="sfc-meta-label"><?php _e('Seats', 'sircon-finn-cars'); ?></span>
						<span class="sfc-meta-value"><?php echo $item['seats'] ?></span>
					<?php } ?>
				</li>
				<li>
					<?php if (!empty($item['body_type'])) { ?>
						<span class="sfc-meta-label"><?php _e('Body type', 'sircon-finn-cars'); ?></span>
						<span class="sfc-meta-value"><?php echo $item['body_type'] ?></span>
					<?php } ?>
				</li>
				<li>
					<?php if (!empty($item['doors'])) { ?>
						<span class="sfc-meta-label"><?php _e('Doors', 'sircon-finn-cars'); ?></span>
						<span class="sfc-meta-value"><?php echo $item['doors'] ?></span>
					<?php } ?>
				</li>
				<li>
					<?php if (!empty($item['trunk_size'])) { ?>
						<span class="sfc-meta-label"><?php _e('Trunk size', 'sircon-finn-cars'); ?></span>
						<span class="sfc-meta-value"><?php echo $item['trunk_size'] ?></span>
					<?php } ?>
				</li>
				<li>
					<?php if (!empty($item['car_location'])) { ?>
						<span class="sfc-meta-label"><?php _e('Car location', 'sircon-finn-cars'); ?></span>
						<span class="sfc-meta-value"><?php echo $item['car_location'] ?></span>
					<?php } ?>
				</li>
				<li>
					<?php if (!empty($item['sales_form'])) { ?>
						<span class="sfc-meta-label"><?php _e('Sales form', 'sircon-finn-cars'); ?></span>
						<span class="sfc-meta-value"><?php echo $item['sales_form'] ?></span>
					<?php } ?>
				</li>
				<li>
					<?php if (!empty($item['registration_class'])) { ?>
						<span class="sfc-meta-label"><?php _e('Tax class', 'sircon-finn-cars'); ?></span>
						<span class="sfc-meta-value"><?php echo $item['registration_class'] ?></span>
					<?php } ?>
				</li>
			</ul>
		</div>
		
		<div class="sfc-equipment desktop_show sfc_side_meta">
			<?php
			if (!empty($item['equipment'])) {
				echo '<h2>' . __('Equipment', 'sircon-finn-cars') . '</h2>';
				echo '<ul>';
				foreach ((array) $item['equipment'] as $equipment) {
					echo '<li>' . $equipment . '</li>';
				}

				echo '</ul>';
			}
			?>
		</div>
	</div>
</div>

<style>
/* Forbedret styling for kontaktknapper og mobil layout */
.red-button, .white-button {
    border-radius: 12px !important;
    font-weight: 600 !important;
    text-align: center !important;
    text-decoration: none !important;
    display: inline-block !important;
    padding: 12px 20px !important;
    font-size: 16px !important;
    transition: none !important;
}

.red-button {
    background-color: #cb0d2a !important;
    color: white !important;
    border: none !important;
}

.white-button {
    background-color: white !important;
    color: #000 !important;
    border: 2px solid #000 !important;
}

.red-button:hover, .white-button:hover {
    /* Ingen hover-effekt */
    background-color: #cb0d2a !important;
    color: white !important;
    opacity: 1 !important;
    transform: none !important;
}

.white-button:hover {
    background-color: white !important;
    color: #000 !important;
    border: 2px solid #000 !important;
    opacity: 1 !important;
    transform: none !important;
}

/* Fiks hvit gap på mobil */
@media (max-width: 768px) {
    .sfc-single {
        background-color: #f5f5f5 !important;
    }
    
    .sfc-meta.sfc_side_meta {
        background-color: #fefcf7 !important;
        margin-bottom: 0 !important;
    }
    
    .sfc-single > div {
        margin-bottom: 0 !important;
    }
    
    .sfc-meta {
        background-color: #fefcf7 !important;
        border-radius: 15px !important;
        margin-bottom: 10px !important;
    }
}

    .footer-wrapper {
        width: 100%;
        display: block;
        margin-bottom: -50px;
    }

    .footer-cta {
        width: 100%;
        background-color: #f9f9f9;
    }

    .footer-cta .footer-cta-inner {
        max-width: 1440px;
        margin: auto;
        padding: 80px 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .footer-cta .footer-cta-text-header {
        font-size: 42px;
        font-weight: 700;
        color: black;
    }

    .footer-cta .footer-cta-text-body {
        color: black;
        font-size: 16px;
        font-weight: 400;
    }

    .footer-cta .footer-cta-side {
        margin-left: 50px;
    }

    .footer-cta-buttons {
        display: flex;
    }

    .footer-cta-side a {
        margin-left: 10px;
    }

    .black-button {
        display: block;
        color: white !important;
        font-size: 16px;
        font-weight: 600;
        background-color: black;
        padding: 12px 20px;
        text-align: center;
        border: none;
        text-decoration: none;
        border-radius: 12px !important;
        transition: none !important;
    }

    .black-button:hover {
        /* Ingen hover-effekt */
        background-color: black !important;
        color: white !important;
        opacity: 1 !important;
        transform: none !important;
    }

    /* Fiks CTA-knapper på mobil */
    @media (max-width: 768px) {
        .footer-cta .footer-cta-inner {
            padding: 40px 30px !important; /* Redusert padding fra 60px til 40px */
        }
        
        .footer-cta-buttons {
            display: flex !important;
            flex-direction: row !important;
            gap: 10px !important;
            width: 100% !important;
        }
        
        .footer-cta-buttons a {
            flex: 1 !important;
            display: block !important;
            padding: 12px 15px !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            text-align: center !important;
            text-decoration: none !important;
            border-radius: 12px !important;
            margin: 0 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        
        .footer-cta-buttons .red-button {
            background-color: #cb0d2a !important;
            color: white !important;
            border: none !important;
        }
        
        .footer-cta-buttons .black-button {
            background-color: black !important;
            color: white !important;
            border: none !important;
            font-size: 13px !important;
        }
    }

    @media only screen and (max-width: 600px) {
        .footer-cta .footer-cta-inner {
            padding: 60px;
            flex-direction: column;
            align-items: start;
            justify-content: start;
        }

        .footer-cta-text {
            margin-bottom: 10px;
        }

        .footer-cta .footer-cta-side {
            margin-left: 0;
        }

        .footer-cta-side a {
            margin-left: 0;
            margin-right: 10px;
        }
    }
</style>

<div class="footer-wrapper">
    <div class="footer-cta">
        <div class="footer-cta-inner">
            <div class="footer-cta-text">
                <h4 class="footer-cta-text-header">
                    Har du noen spørsmål om denne bilen?
                </h4>
                <h4 class="footer-cta-text-body">
                    Vi er her for å hjelpe deg.
                </h4>
            </div>
            <div class="footer-cta-side">
                <div class="footer-cta-buttons">
                    <?php if ($customPhone): ?>
                        <a href="tel:<?php echo esc_attr($customPhone); ?>" class="red-button tracking-call-us-footer">
                            Ring oss
                        </a>
                    <?php endif; ?>

                    <?php if ($customEmail): ?>
                        <a href="mailto:<?php echo esc_attr($customEmail); ?>" class="black-button tracking-email-us-footer">
                            Send e-post
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function($){
		$('footer .sfc-single').remove();
		$('footer .footer-wrapper').remove();
	});
</script>