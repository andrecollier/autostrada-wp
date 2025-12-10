<?php

/**
 * Grab all attributes for a given shortcode in a text
 *
 * @uses get_shortcode_regex()
 * @uses shortcode_parse_atts()
 * @param  string $tag   Shortcode tag
 * @param  string $text  Text containing shortcodes
 * @return array  $out   Array of attributes
 */

function wpse172275_get_all_attributes( $tag, $text ) {
    preg_match_all( '/' . get_shortcode_regex() . '/s', $text, $matches );
    $out = array();
    if( isset( $matches[2] ) )
    {
        foreach( (array) $matches[2] as $key => $value )
        {
            if( $tag === $value )
                $out[] = shortcode_parse_atts( $matches[3][$key] );  
        }
    }
    return $out;
}

function get_dealer_id($tag, $content) {

    try {
        $atts = wpse172275_get_all_attributes($tag, $content);

        if (count($atts) == 0) {
            $dealerId = "";
            return $dealerId;
        }

        if ($atts[0] == "") {
            $dealerId = "";
            return $dealerId;
        }

        $dealerId = $atts[0]['dealer'];
        if ($dealerId === "1") {
            $dealerId = "";
        }

    } catch (Exception $e) {

        $dealerId = "";
    }

    return $dealerId;
}

function get_dealer_id_from_short_code($tag, $post) {

    try {
        // Check if shortcode is available in metadata
        $dealerId = get_dealer_id($tag, $post->post_content);
        if ($dealerId != "")
            return $dealerId;

        $meta = get_post_meta($post->ID);

        if( $meta ) {
            foreach( $meta as $key => $item)
                {
                    $dealerId = get_dealer_id($tag, $item[0]);
                    if ($dealerId != "")
                        return $dealerId;
                }
        }

    } catch (Exception $e) {
        $dealerId = "";
    }

    return $dealerId;
}

function has_short_code_in_post($post, $tag) {

    try {

        $has_code = has_shortcode($post->post_content, $tag);
        if ($has_code == true)
            return true;

        $meta = get_post_meta($post->ID);

        if( $meta ) {
            foreach( $meta as $key => $item) {
                $has_code = $has_code | has_shortcode($item[0], $tag);
            }
        }

        return $has_code;
    }
    catch (Exception $e) {
        return false;
    }
}

function has_tacdis_shortcode($post, $tag, $afctag = NULL) {
    
    $has_code = has_short_code_in_post($post, $tag);
    if ($has_code == true)
        return true;

    // afc
    $content = [];
    if (function_exists('get_field') == true) {
        $content = get_field( 'content' );
    }

    if ($afctag == NULL)
        $afctag = $tag;
    
    return multi_search( $content, 'acf_fc_layout', $afctag);
}

function multi_search( $array, $field, $value) {
	if( $array ) {
		foreach( $array as $key => $item)
	   	{
			if ( $item[$field] === $value )
				return true;
	   	}
	}
	return false;
}