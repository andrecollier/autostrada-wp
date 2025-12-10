<?php

namespace Sircon\Library;

use DOMDocument;
use Exception;
use SimpleXMLElement;
use ScssPhp\ScssPhp\Compiler;
use Padaliyajay\PHPAutoprefixer\Autoprefixer;

final class Util {

	public static function pluginDataBySlug(string $slug, array $headers): array {
		$plugin_data = [];
		$headers = array_merge(['Name' => 'Plugin Name'], $headers);
		$plugindir = WP_PLUGIN_DIR . '/' . $slug;
		if (!file_exists($plugindir)) {
			return $plugin_data;
		}

		$handle = @opendir($plugindir);
		$plugin_data = [];
		while (($subfile = readdir($handle)) !== false) {
			if (substr($subfile, 0, 1) === '.') {
				continue;
			}

			if (substr($subfile, -4) === '.php') {
				$plugin_data = get_file_data("$plugindir/$subfile", $headers);
				if (!empty($plugin_data['Name'])) {
					break;
				}
			}
		}

		closedir($handle);

		return $plugin_data;
	}

	public static function resolveUrlRedirect(string $url) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_exec($ch);
		$target = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
		curl_close($ch);
		return $target ?? $url;
	}

	public static function prettyPrintXml(string $xml): void {
		$dom = new DOMDocument('1.0');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		libxml_use_internal_errors(true);
		$dom->loadXML($xml);
		if (empty(libxml_get_errors())) {
			$XMLElement = new SimpleXMLElement($dom->saveXML());
			echo htmlentities($XMLElement->saveXML());
		} else {
			echo htmlentities($xml);
		}

		libxml_clear_errors();
		libxml_use_internal_errors(false);
	}

	/**
	 * Compile SCSS
	 *
	 * Compile source SCSS file to target CSS file if the source file has been modified later than then target file.
	 * The compiled CSS will also be run through an Autoprefixer and minified. Minification can be disabled.
	 *
	 * @param string $scss_file Full path to the source file
	 * @param string $target Full path to the target file
	 * @param bool $prettyOutput Set to false to disable pretty print
	 * @return void
	 */
	public static function compileSCSS(string $scss_file, string $target, bool $prettyOutput = false): void {
		//Check for changes
		$do_compile = (!file_exists($target) || filemtime($target) < filemtime($scss_file));
		if (!$do_compile) {
			return;
		}

		// Compile
		$scss = new Compiler();
		$compiled = '';
		try {
			$compiled = $scss->compileString(file_get_contents($scss_file))->getCss();
			$compiled = (new Autoprefixer($compiled))->compile($prettyOutput);
		} catch (Exception $e) {
			$compiled = "/*\n * Compile error: {$e->getMessage()}\n */";
		}

		// Save
		file_put_contents($target, $compiled);
	}
}
