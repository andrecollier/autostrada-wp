<?php
/**
 * Admin Page for Profile Order Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render the admin page
 */
function pom_render_admin_page() {
    // Get all locations
    $locations = get_terms(array(
        'taxonomy' => 'locations',
        'hide_empty' => false,
        'orderby' => 'name',
        'order' => 'ASC',
    ));

    // Get selected location
    $selected_location = isset($_GET['location']) ? intval($_GET['location']) : 0;

    ?>
    <div class="wrap pom-wrap">
        <h1><?php _e('Sorter Ansatte per Avdeling', 'profile-order-manager'); ?></h1>

        <div class="pom-intro">
            <p><?php _e('Velg en avdeling og dra ansattkortene i ønsket rekkefølge. Endringene lagres automatisk.', 'profile-order-manager'); ?></p>
        </div>

        <div class="pom-location-selector">
            <label for="pom-location"><?php _e('Velg avdeling:', 'profile-order-manager'); ?></label>
            <select id="pom-location" name="location">
                <option value=""><?php _e('— Velg avdeling —', 'profile-order-manager'); ?></option>
                <?php foreach ($locations as $location) : ?>
                    <option value="<?php echo esc_attr($location->term_id); ?>" <?php selected($selected_location, $location->term_id); ?>>
                        <?php echo esc_html($location->name); ?> (<?php echo $location->count; ?> ansatte)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div id="pom-status" class="pom-status" style="display: none;"></div>

        <div id="pom-profiles-container" class="pom-profiles-container">
            <?php if ($selected_location) : ?>
                <?php pom_render_profiles_list($selected_location); ?>
            <?php else : ?>
                <div class="pom-placeholder">
                    <span class="dashicons dashicons-groups"></span>
                    <p><?php _e('Velg en avdeling for å se og sortere ansatte', 'profile-order-manager'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

/**
 * Render the profiles list for a specific location
 */
function pom_render_profiles_list($location_id) {
    // Get saved order for this location
    $saved_order = get_term_meta($location_id, '_pom_profile_order', true);
    if (!is_array($saved_order)) {
        $saved_order = array();
    }

    // Get ALL profiles for this location (not filtered by saved order)
    $args = array(
        'post_type' => 'profiles',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'locations',
                'field' => 'term_id',
                'terms' => $location_id,
            ),
        ),
        'orderby' => 'menu_order title',
        'order' => 'ASC',
    );

    $profiles_query = new WP_Query($args);

    // Sort profiles: those in saved_order first (in order), then new profiles at the end
    $ordered_profiles = array();
    $new_profiles = array();

    while ($profiles_query->have_posts()) {
        $profiles_query->the_post();
        $post_id = get_the_ID();

        if (in_array($post_id, $saved_order)) {
            $ordered_profiles[$post_id] = get_post($post_id);
        } else {
            $new_profiles[$post_id] = get_post($post_id);
        }
    }
    wp_reset_postdata();

    // Sort ordered_profiles by saved_order
    $sorted_profiles = array();
    foreach ($saved_order as $id) {
        if (isset($ordered_profiles[$id])) {
            $sorted_profiles[] = $ordered_profiles[$id];
        }
    }
    // Append new profiles at the end
    foreach ($new_profiles as $profile) {
        $sorted_profiles[] = $profile;
    }

    $profiles = $sorted_profiles;

    if (empty($profiles)) {
        echo '<div class="pom-no-profiles">';
        echo '<p>' . __('Ingen ansatte funnet i denne avdelingen.', 'profile-order-manager') . '</p>';
        echo '</div>';
        return;
    }

    $location = get_term($location_id, 'locations');
    echo '<h2 class="pom-location-title">' . esc_html($location->name) . ' <span class="count">(' . count($profiles) . ' ansatte)</span></h2>';

    echo '<ul id="pom-sortable" class="pom-sortable" data-location="' . esc_attr($location_id) . '">';

    foreach ($profiles as $profile) {
        $profile_id = $profile->ID;
        $thumbnail = get_the_post_thumbnail_url($profile_id, 'thumbnail');
        $subtitle = get_field('profile_sub_title', $profile_id);
        $email = get_field('profile_email', $profile_id);
        $phone = get_field('profile_number', $profile_id);

        ?>
        <li class="pom-profile-item" data-id="<?php echo esc_attr($profile_id); ?>">
            <div class="pom-profile-card">
                <span class="pom-drag-handle dashicons dashicons-menu"></span>

                <div class="pom-profile-image">
                    <?php if ($thumbnail) : ?>
                        <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($profile->post_title); ?>">
                    <?php else : ?>
                        <span class="dashicons dashicons-admin-users"></span>
                    <?php endif; ?>
                </div>

                <div class="pom-profile-info">
                    <strong class="pom-profile-name"><?php echo esc_html($profile->post_title); ?></strong>
                    <?php if ($subtitle) : ?>
                        <span class="pom-profile-title"><?php echo esc_html($subtitle); ?></span>
                    <?php endif; ?>
                    <?php if ($phone) : ?>
                        <span class="pom-profile-phone"><?php echo esc_html($phone); ?></span>
                    <?php endif; ?>
                </div>

                <div class="pom-profile-actions">
                    <a href="<?php echo get_edit_post_link($profile_id); ?>" class="button button-small" target="_blank">
                        <?php _e('Rediger', 'profile-order-manager'); ?>
                    </a>
                </div>
            </div>
        </li>
        <?php
    }

    echo '</ul>';
}

/**
 * AJAX callback to get profiles HTML
 */
function pom_ajax_get_profiles() {
    check_ajax_referer('pom_nonce', 'nonce');

    $location_id = isset($_POST['location_id']) ? intval($_POST['location_id']) : 0;

    if (!$location_id) {
        wp_send_json_error('No location specified');
    }

    ob_start();
    pom_render_profiles_list($location_id);
    $html = ob_get_clean();

    wp_send_json_success(array('html' => $html));
}
