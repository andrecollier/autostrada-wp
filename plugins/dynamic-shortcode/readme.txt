=== Dynamic Shortcode ===
Contributors: web2webs
Tags: shortcodes, nested, dynamic shortcode, nested shortcode, shortcode inside shortcode
Requires at least: 4.5
Requires PHP: 5.3
Tested up to: 5.4
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A small plugin which allows you to use shortcodes as the attribute of other shortcode, shortcode inside shortcode, dynamic shortcode.


== Description ==

A small plugin which allows you to use shortcodes as the attribute of other shortcode, shortcode inside shortcode, dynamic shortcode.


= How to use =

* Use this shortcode to make dynamic shortcodes 

`[dy_shortcode sc="" at=""][/dy_shortcode]`

* For example making a gallery with dynamic id by another shortcode 

`[gallery id=""] [custom id="123"]`

* Final dynamic shortcode merged 

`[dy_shortcode sc="gallery" at="id"][custom id="123"][/dy_shortcode]`

* Only 1 attribute can be dynamic, use comma (,) for multiple shortcode entries 

`[dy_shortcode sc="gallery" at="id"][custom id="123"],[custom id="321"],[custom id="231"][/dy_shortcode]`

* All other static attributes can be placed on the final shortcode

`[dy_shortcode sc="gallery" at="id" size="medium" background="white"][custom id="123"][/dy_shortcode]`


== Installation ==

1. Install automatically through the 'Plugins', 'Add New' menu in WordPress, or upload the 'dynamic-shortcode' folder to the '/wp-content/plugins/' directory. 

2. Activate the plugin through the 'Plugins' menu in WordPress. 


