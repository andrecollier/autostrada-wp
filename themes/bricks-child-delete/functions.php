<?php 
/**
 * Register/enqueue custom scripts and styles
 */
add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_style( 'bricks-child', get_stylesheet_uri(), ['bricks-frontend'], filemtime( get_stylesheet_directory() . '/style.css' ) );
} );

/**
 * Register custom elements
 */
add_action( 'init', function() {
  $element_files = [
    __DIR__ . '/elements/title.php',
  ];

  foreach ( $element_files as $file ) {
    \Bricks\Elements::register_element( $file );
  }
}, 11 );

/**
 * Filter which elements to show in the builder
 * 
 * Simple outcomment (prefix: //) the elements you don't want to use in Bricks
 */
function bricks_filter_builder_elements( $elements ) {
	$elements = [
		// Basic
		// 'container', // since 1.2
		// 'heading',
		'text',
		'button',
		'icon',
		'image',
		'video',

		// General
		'divider',
		'icon-box',
		'list',
		'accordion',
		'tabs',
		'form',
		'map',
		'alert',
		'animated-typing',
		'countdown',
		'counter',
		'pricing-tables',
		'progress-bar',
		'pie-chart',
		'team-members',
		'testimonials',
		'html',
		'code',
		'logo',

		// Media
		'image-gallery',
		'audio',
		'carousel',
		'slider',
		'svg',

		// Social
		'social-icons',
		'facebook-page',
		'instagram-feed',

		// WordPress
		'wordpress',
		'posts',
		'nav-menu',
		'sidebar',
		'search',
		'shortcode',

		// Single
		'post-title',
		'post-excerpt',
		'post-meta',
		'post-content',
		'post-sharing',
		'post-related-posts',
		'post-author',
		'post-comments',
		'post-taxonomy',
		'post-navigation',

		// Hidden in builder panel
		'section',
		'row',
		'column',
	];

	return $elements;
}
// add_filter( 'bricks/builder/elements', 'bricks_filter_builder_elements' );

/**
 * Add text strings to builder
 */
add_filter( 'bricks/builder/i18n', function( $i18n ) {
  // For element category 'custom'
  $i18n['custom'] = esc_html__( 'Custom', 'bricks' );

  return $i18n;
} );

/**
 * Custom save messages
 */
add_filter( 'bricks/builder/save_messages', function( $messages ) {
	// First option: Add individual save message
	$messages[] = 'Yasss';

	// Second option: Replace all save messages
	$messages = [
		'Done',
		'Cool',
		'High five!',
	];

  return $messages;
} );

/**
 * Customize standard fonts
 */
// add_filter( 'bricks/builder/standard_fonts', function( $standard_fonts ) {
// 	// First option: Add individual standard font
// 	$standard_fonts[] = 'Verdana';

// 	// Second option: Replace all standard fonts
// 	$standard_fonts = [
// 		'Georgia',
// 		'Times New Roman',
// 		'Verdana',
// 	];

//   return $standard_fonts;
// } );

/** 
 * Add custom map style
 */
// add_filter( 'bricks/builder/map_styles', function( $map_styles ) {
//   // Shades of grey (https://snazzymaps.com/style/38/shades-of-grey)
//   $map_styles['shadesOfGrey'] = [
//     'label' => esc_html__( 'Shades of grey', 'bricks' ),
//     'style' => '[ { "featureType": "all", "elementType": "labels.text.fill", "stylers": [ { "saturation": 36 }, { "color": "#000000" }, { "lightness": 40 } ] }, { "featureType": "all", "elementType": "labels.text.stroke", "stylers": [ { "visibility": "on" }, { "color": "#000000" }, { "lightness": 16 } ] }, { "featureType": "all", "elementType": "labels.icon", "stylers": [ { "visibility": "off" } ] }, { "featureType": "administrative", "elementType": "geometry.fill", "stylers": [ { "color": "#000000" }, { "lightness": 20 } ] }, { "featureType": "administrative", "elementType": "geometry.stroke", "stylers": [ { "color": "#000000" }, { "lightness": 17 }, { "weight": 1.2 } ] }, { "featureType": "landscape", "elementType": "geometry", "stylers": [ { "color": "#000000" }, { "lightness": 20 } ] }, { "featureType": "poi", "elementType": "geometry", "stylers": [ { "color": "#000000" }, { "lightness": 21 } ] }, { "featureType": "road.highway", "elementType": "geometry.fill", "stylers": [ { "color": "#000000" }, { "lightness": 17 } ] }, { "featureType": "road.highway", "elementType": "geometry.stroke", "stylers": [ { "color": "#000000" }, { "lightness": 29 }, { "weight": 0.2 } ] }, { "featureType": "road.arterial", "elementType": "geometry", "stylers": [ { "color": "#000000" }, { "lightness": 18 } ] }, { "featureType": "road.local", "elementType": "geometry", "stylers": [ { "color": "#000000" }, { "lightness": 16 } ] }, { "featureType": "transit", "elementType": "geometry", "stylers": [ { "color": "#000000" }, { "lightness": 19 } ] }, { "featureType": "water", "elementType": "geometry", "stylers": [ { "color": "#000000" }, { "lightness": 17 } ] } ]'
//   ];

//   return $map_styles;
// } );

/*
register_post_type( 'bil', array(
    'has_archive' => false,
) ); */

function register_custom_field_email_block( $blocks ) {

	// "custom_field_email_block" corresponds to the block slug.
	$blocks['custom_field_email_block'] = [
		'name'            => 'Custom Field - Email',
		'render_callback' => 'render_custom_field_email_block',
	];

	return $blocks;

}
add_filter( 'wp_grid_builder/blocks', 'register_custom_field_email_block' );

function render_custom_field_email_block() {

	// Object can be a post, term or user.
	$object = wpgb_get_object();

	// If this is not a post (you may change this condition for user or term).
	if ( ! isset( $object->post_type ) ) {
		return;
	}

	// You have to change "custom_field_name" by yours.
	$email = get_post_meta( $object->ID, 'profile_email', true );

	if ( empty( $email ) ) {
		return;
	}

	$email = antispambot( $email );

	printf(
		'<a href="%s">%s</a>',
		esc_url( 'mailto:' . $email ),
		esc_html( $email )
	);
}

/**
 * Meld din interesse-knapp for bil-sider
 * Henter URL fra ACF-felt og viser knapp på frontend
 */
add_action( 'wp_footer', function() {
	// Kun på bil post type
	if ( ! is_singular( 'bil' ) ) {
		return;
	}

	$interesse_url = get_field( 'meld_din_interesse_url:' );

	// Hvis feltet er tomt, ikke vis noe
	if ( empty( $interesse_url ) ) {
		return;
	}

	$interesse_url = esc_url( $interesse_url );
	?>
	<style>
	.meld-din-interesse-knapp {
		display: inline-flex !important;
		align-items: center !important;
		justify-content: center !important;
		padding: 10px 20px !important;
		font-size: 13px !important;
		font-weight: 600 !important;
		border-radius: 12px !important;
		margin-top: 10px !important;
		background: #cb0d2a !important;
		border: 1.5px solid #cb0d2a !important;
		color: #fff !important;
		text-decoration: none !important;
		transition: all 0.3s ease !important;
		font-family: inherit !important;
		cursor: pointer !important;
		width: auto !important;
		box-sizing: border-box !important;
		align-self: stretch !important;
		min-height: 40px !important;
		line-height: 1.2 !important;
	}
	.meld-din-interesse-knapp:hover {
		opacity: 0.9 !important;
		text-decoration: none !important;
		color: #fff !important;
		border: 1.5px solid #000 !important;
		background: #000 !important;
	}
	@media (max-width: 478px) {
		.meld-din-interesse-knapp {
			width: 100% !important;
			height: 50px !important;
		}
	}
	</style>
	<script>
	(function() {
		var interesseUrl = <?php echo json_encode( $interesse_url ); ?>;

		function addMeldDinInteresseButton() {
			// Prøv først spesifikk container ID
			var container = document.querySelector("#brxe-dsadfq");

			// Fallback: finn container ved å lete etter Kontakt-knapper
			if (!container) {
				var buttons = document.querySelectorAll('a');
				for (var i = 0; i < buttons.length; i++) {
					if (buttons[i].textContent.includes('Kontakt Kongsberg') ||
						buttons[i].textContent.includes('Kontakt Notodden') ||
						buttons[i].textContent.includes('Kontakt Seljord')) {
						container = buttons[i].closest('div');
						break;
					}
				}
			}

			// Fallback: finn Bestill prøvekjøring-knappen
			if (!container) {
				var buttons = document.querySelectorAll('a');
				for (var i = 0; i < buttons.length; i++) {
					if (buttons[i].textContent.includes('Bestill prøvekjøring')) {
						container = buttons[i].closest('div');
						break;
					}
				}
			}

			if (!container) {
				console.log('Meld din interesse: Container ikke funnet');
				return false;
			}

			// Sjekk om knappen allerede eksisterer
			if (container.querySelector(".meld-din-interesse-knapp")) {
				return true;
			}

			// Opprett knappen
			var link = document.createElement("a");
			link.href = interesseUrl;
			link.target = "_blank";
			link.rel = "noopener";
			link.className = "meld-din-interesse-knapp";
			link.textContent = "Meld din interesse";

			container.appendChild(link);
			console.log('Meld din interesse-knapp lagt til');
			return true;
		}

		// Kjør ved DOMContentLoaded og med forsinkelse for å håndtere Bricks lazy loading
		document.addEventListener("DOMContentLoaded", addMeldDinInteresseButton);
		setTimeout(addMeldDinInteresseButton, 1000);
		setTimeout(addMeldDinInteresseButton, 3000);
	})();
	</script>
	<?php
}, 100 );