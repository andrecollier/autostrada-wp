<?php

namespace Sircon\Library\Updater;

use Sircon\Library\Library;

abstract class Updater {

	private const BASEURL = 'https://code.sircon.net/api';

	private static $licenses = null;

	protected function apiRequest(string $endpoint, string $slug, string $version = '') {
		global $wp_version;

		$request = [
			'body' => [
				'domain' => $_SERVER['SERVER_NAME'],
				'license' => $this->getLicense($slug),
				'version' => $version,
				'library' => Library::version(),
				'php' => PHP_VERSION,
				'wp' => $wp_version,
			],
		];

		return wp_remote_post(self::BASEURL . $endpoint, $request);
	}

	private function getLicense(string $slug): string {
		self::$licenses = self::$licenses ?? get_option('sircon-library-licenses');
		return self::$licenses[$slug] ?? '';
	}
}
