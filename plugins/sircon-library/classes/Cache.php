<?php

namespace Sircon\Library;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Cache {

	/**
	 * Write data to file cache
	 *
	 * @param string $path  Category of the item to be cached, also used as the subfolder for the actual file.
	 * @param string $name  Name of the item to be cached, also used as name for the actual file.
	 * @param string $value The value to cache
	 */
	public static function set(string $path, string $name, string $value): void {
		$file = self::file($path, $name);
		file_put_contents($file, $value);
	}

	/**
	 * Check if cache file exists
	 *
	 * @param string $path Category of the item to be cached, also used as the subfolder for the actual file.
	 * @param string $name Name of the item to be cached, also used as name for the actual file.
	 * @return bool
	 */
	public static function has(string $path, string $name): bool {
		$file = self::file($path, $name);
		return file_exists($file);
	}

	/**
	 * Get data from file cache
	 *
	 * @param  string $path Category of the item to be cached, also used as the subfolder for the actual file.
	 * @param  string $name Name of the item to be cached, also used as name for the actual file.
	 *
	 * @return string       The cached value
	 */
	public static function get(string $path, string $name): string {
		$file = self::file($path, $name);
		return file_exists($file) ? file_get_contents($file) : '';
	}

	/**
	 * Delete data from file cache
	 *
	 * @param  string $path Category of the item to be deleted, also used as the subfolder for the actual file.
	 * @param  string $name Name of the item to be deleted, also used as name for the actual file.
	 */
	public static function delete(string $path, string $name): void {
		$file = self::file($path, $name);
		unlink($file);
	}

	/**
	 * Get last modified time for cache
	 *
	 * @param  string $path Category of the item to be cached, also used as the subfolder for the actual file.
	 * @param  string $name Name of the item to be cached, also used as name for the actual file.
	 *
	 * @return int          Cache modified time
	 */
	public static function time(string $path, string $name): int {
		$file = self::file($path, $name);
		return file_exists($file) ? filemtime($file) : 0;
	}

	/**
	 * Get the cache filepath
	 *
	 * @param  string $path Category of the item to be cached, also used as the subfolder for the actual file.
	 * @param  string $name Name of the item to be cached, also used as name for the actual file.
	 *
	 * @return string       The cache file path
	 */
	public static function file(string $path, string $name): string {
		$path = str_replace('..', 'quack', $path);
		$name = str_replace(['..', '/'], ['quack', '-'], $name);
		$dir = ABSPATH . '/wp-content/cache/sircon/' . $path . '/';
		if (!file_exists($dir)) {
			mkdir($dir, 0755, true);
		}

		return $dir . $name;
	}

	/**
	 * Clear data from file cache path
	 *
	 * @param  string $path Category to be cleared.
	 */
	public static function clear(string $path): void {
		$abspath = self::file($path, '');
		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($abspath, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);

		foreach ($files as $fileinfo) {
			if ($fileinfo->isDir()) {
				rmdir($fileinfo->getRealPath());
			} else {
				unlink($fileinfo->getRealPath());
			}
		}

		rmdir($abspath);
	}
}
