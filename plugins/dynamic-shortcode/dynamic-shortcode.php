<?php
/*
Plugin Name: Dynamic Shortcode
Plugin URI:  https://wpexperto.es/dynamic-shortcodes/
Description: A small plugin which allows you to use shortcodes as the attribute of other shortcode, shortcode inside shortcode, dynamic shortcode.
Version:     1.0
Author:      Amir JM
Author URI:  https://wpexperto.es/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

class dynamic_shortcode_plugin {

	public function __construct(){
		add_action('init', array($this,'register_square_shortcode'));	
	}
	
	// Add Dynamic Shortcode
	public function register_square_shortcode() {
		add_shortcode( 'dy_shortcode', array($this,'dynamic_shortcode_square'));
	}

	// Render Dynamic Shortcode
	public function dynamic_shortcode_square( $atts, $content = null ) {
	
		$shortcode_name='';
		$shortcode_atts='';
		$shortcode = '';
		
		foreach ($atts as $key => $item) {
			if ($key=='sc') $shortcode_name = $item;
			elseif ($key=='at') $shortcode_atts=$item;
			else $shortcode .= $key.'="'.$item.'" ';		
		}
		
		$var=explode(',',$content);
		$shortcode2 = '';
		$i = 1;
		$len = count($var);
		
		foreach($var as $row) {
			if ($i != $len) $comma = ',';
			else $comma = '';
			$shortcode2 .= do_shortcode($row).$comma;
			$i++;
		}
		
		$final =  do_shortcode('['.$shortcode_name.' '.$shortcode.' '.$shortcode_atts.'="'.$shortcode2.'"]');
		
		return $final;
	}
	
} // End of Class

$dynamic_shortcode_plugin = new dynamic_shortcode_plugin(); 
?>