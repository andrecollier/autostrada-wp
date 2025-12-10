<?php

namespace sircon\finncars;

use SimpleXMLElement;
use sircon\Options;

class Finn {

	private const FINN_API_URL = 'https://cache.api.finn.no/iad/';

	private const FILE_CACHE = ABSPATH . '/wp-content/cache/all/sircon-finncars/';

	private $cache_max_age = 0;

	private $orgid = '';

	private $apikey = '';

	public function __construct() {
		$this->cache_max_age = Options::get_option(FinnCars::OPTIONSPAGE_ID, 'cache_max_age');
		$this->orgid = Options::get_option(FinnCars::OPTIONSPAGE_ID, 'org_id');
		$this->apikey = Options::get_option(FinnCars::OPTIONSPAGE_ID, 'api_key');
	}

	
	/**
	* Get a list of all for sale cars
	*
	* @return array List of IDs
	*/
	public function getArchive(int $page = 1, array $filters = [], $rows = 21): string {
		$url = 'search/car-norway/?rows=' . $rows . '&page=' . $page;
		$has_orgid_filter = false;
		foreach ($filters as $filter) {
			$url .= '&' . $filter['name'] . '=' . $filter['value'];
			if ($filter['name'] === 'orgId') {
				$has_orgid_filter = true;
			}
		}

		if (!$has_orgid_filter) {
			$url .= '&orgId=' . $this->orgid;
		}

		$response = $this->getFinnResponse($url);
		$this->populateTypeCache($response);

		return $response;
	}


	/**
	 * Get data for a single property for sale
	 *
	 * @param  string $type [description]
	 * @param  int    $id   The finn-ID to get data from
	 *
	 * @return array        An array with the requested data
	 */
	public function getSingle(int $id): string {
		$type = $this->getType($id);
		$cached = $this->getCache($type, $id);
		if (!$cached) {
			$response = $this->getFinnResponse('ad/' . $type . '/' . $id);
			$this->setCache($type, $id, $response);
			return $response;
		}

		return $cached;
	}

	/**
	 * Get a response from Finn.no API
	 *
	 * @param  string $url The URL to query
	 *
	 * @return string      Response data
	 */
	private function getFinnResponse(string $url): string {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->orgid);
		curl_setopt($ch, CURLOPT_URL, self::FINN_API_URL . utf8_decode($url));
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-FINN-apikey: ' . urlencode($this->apikey)]);
		$data = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if ($httpcode !== 200) {
			return '';
		}

		return $data;
	}

	public function updateTypeCache(): void {
		$url = 'search/car-norway/?orgId=' . $this->orgid . '&rows=100000';
		$response = $this->getFinnResponse($url);
		$this->populateTypeCache($response);
	}

	private function populateTypeCache(string $response): void {
		$result = simplexml_load_string($response);
		foreach ($result->entry as $ad) {
			$ns = $ad->getNamespaces(true);
			foreach ($ad->category as $category) {
				if ($category->attributes()->scheme->__toString() === 'urn:finn:ad:type') {
					$this->setCache('type', $ad->children($ns['dc'])->identifier->__toString(), $category->attributes()->term->__toString());
					continue 2;
				}
			}
		}
	}

	private function getType(int $id) {
		$type = $this->getCache('type', $id, false);
		return $type ? $type : 'car-used-sale';
	}

	public static function parseSingle(\SimpleXMLElement $ad): array {
		$item = [];
		$ns = $ad->getNamespaces(true);

		$item['id'] = $ad->children($ns['dc'])->identifier->__toString();
		$item['title'] = $ad->title->__toString();
		$item['summary'] = $ad->summary->__toString();
		$item['updated'] = $ad->updated->__toString();
		$item['published'] = $ad->published->__toString();

		$item['author'] = $ad->author->name->__toString();

		$contact = $ad->children($ns['finn'])->contact;
		if ($contact) {
			$item['contact'] = [
				'name' => (string) $contact->children()->name,
				'email' => (string) $contact->children()->email,
				'uri' => (string) $contact->children()->uri,
				'phone' => [],
			];

			foreach ($contact->children($ns['finn'])->{'phone-number'} as $phone) {
				$item['contact']['phone'][(string) $phone->attributes()->type] = (string) $phone;
			}
		}

		$item['location'] = $ad->children($ns['georss'])->point->__toString();

		foreach ($ad->category as $category) {
			switch ($category->attributes()->scheme->__toString()) {
				case 'urn:finn:ad:type':
					$item['type'] = $category->attributes()->term->__toString();
					break;

				case 'urn:finn:ad:disposed':
					$item['sold'] = $category->attributes()->term->__toString();
					break;

				case 'urn:finn:ad:private':
					$item['private'] = $category->attributes()->term->__toString();
					break;
			}
		}

		if ($ad->children($ns['media']) && $ad->children($ns['media'])->content->attributes()) {
			$item['image'] = $ad->children($ns['media'])->content->attributes()->url->__toString();
		}

		$item['city'] = $ad->children($ns['finn'])->location->children($ns['finn'])->city->__toString();

		$fields = $ad->children($ns['finn'])->adata->children($ns['finn'])->field;
		$item['weight'] = [];
		$item['engine'] = [];
		foreach ($fields as $field) {
			$fieldname = $field->attributes()->name->__toString();
			switch ($fieldname) {
				case 'model_spec':
				case 'description':
				case 'service_documents':
					$item[$fieldname] = $field->__toString();
					break;

				case 'equipment':
					$value = [];
					$equipments = $field->children($ns['finn']);
					foreach ($equipments as $equipment) {
						$value[] = $equipment->__toString();
					}

					$item[$fieldname] = $value;
					break;

				case 'weight':
				case 'engine':
					$value = [];
					$sub = $field->children($ns['finn']);
					foreach ($sub as $part) {
						if (!$part->attributes()->value) {
							continue;
						}

						$value[$part->attributes()->name->__toString()] = $part->attributes()->value->__toString();
					}

					$item[$fieldname] = $value;
					break;

				default:
					if (!$field->attributes()->value) {
						continue 2;
					}

					$item[$fieldname] = $field->attributes()->value->__toString();
					break;
			}
		}

		$prices = $ad->children($ns['finn'])->adata->children($ns['finn'])->price;
		$item['price'] = [];
		foreach ($prices as $price) {
			if (!$price->attributes()->value) {
				continue;
			}

			$item['price'][$price->attributes()->name->__toString()] = $price->attributes()->value->__toString();

			$sub = $price->children($ns['finn']);
			foreach ($sub as $part) {
				if (!$part->attributes()->value) {
					continue;
				}

				$item['price'][$price->attributes()->name->__toString() . '_' . $part->attributes()->name->__toString()] = $part->attributes()->value->__toString();
			}
		}

		$medias = $ad->children($ns['media']);
		foreach ($medias as $media) {
			assert($media instanceof SimpleXMLElement);
			$atts = $media->attributes();
			$desc = $media->description;
			$url = str_replace('default', '1600w', $atts->url);

			if ($atts->medium == 'image') {
				$item['images'][] = [
					'txt'   => (string) $desc,
					'url'   => (string) $url
				];
			}

			if ($atts->medium == 'video') {
				$item['videos'][] = (string) $media->children($ns['media'])->player->attributes()->url;
			}
		}

		return $item;
	}

	public static function parseSingleArchive(\SimpleXMLElement $ad): array {
		$item = [];
		$ns = $ad->getNamespaces(true);

		$item['id'] = $ad->children($ns['dc'])->identifier->__toString();
		$item['title'] = $ad->title->__toString();
		$item['updated'] = $ad->updated->__toString();
		$item['published'] = $ad->published->__toString();

		foreach ($ad->category as $category) {
			switch ($category->attributes()->scheme->__toString()) {
				case 'urn:finn:ad:type':
					$item['type'] = $category->attributes()->term->__toString();
					break;

				case 'urn:finn:ad:disposed':
					$item['sold'] = $category->attributes()->term->__toString();
					break;

				case 'urn:finn:ad:private':
					$item['private'] = $category->attributes()->term->__toString();
					break;
			}
		}

		$item['author'] = $ad->author->name->__toString();

		$item['location'] = $ad->children($ns['georss'])->point->__toString();

		if (isset($ns['media'])) {
			if ($ad->children($ns['media']) && $ad->children($ns['media'])->content->attributes()) {
				$item['image'] = $ad->children($ns['media'])->content->attributes()->url->__toString();
			}
		}

		$item['city'] = $ad->children($ns['finn'])->location->children($ns['finn'])->city->__toString();

		$fields = $ad->children($ns['finn'])->adata->children($ns['finn'])->field;
		foreach ($fields as $field) {
			if ($field->attributes()->value) {
				$item[$field->attributes()->name->__toString()] = $field->attributes()->value->__toString();
			}

			$sub = $field->children($ns['finn']);
			foreach ($sub as $part) {
				if ($part->attributes()->value) {
					$item[$field->attributes()->name->__toString() . '_' . $part->attributes()->name->__toString()] = $part->attributes()->value->__toString();
				}
			}
		}

		$prices = $ad->children($ns['finn'])->adata->children($ns['finn'])->price;
		foreach ($prices as $price) {
			if ($price->attributes()->value) {
				$item['price_' . $price->attributes()->name->__toString()] = $price->attributes()->value->__toString();
			}

			$sub = $price->children($ns['finn']);
			foreach ($sub as $part) {
				if ($part->attributes()->value) {
					$item['price_' . $price->attributes()->name->__toString() . '_' . $part->attributes()->name->__toString()] = $part->attributes()->value->__toString();
				}
			}
		}

		return $item;
	}

	/**
	 * Delete all finn.no cache files
	 */
	public static function clearCache() {
		$dir = self::FILE_CACHE . '/';
		if (file_exists($dir)) {
			$it = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
			$files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
			foreach ($files as $file) {
				if ($file->isDir()) {
					rmdir($file->getRealPath());
				} else {
					unlink($file->getRealPath());
				}
			}

			rmdir($dir);
		}

		(new Finn())->updateTypeCache();
	}


	/**
	* Write data to file cache.
	*
	* @param string $type  Category of the item to be cached, also used as the subfolder for the actual file.
	* @param string $name  Name of the item to be cached, also used as name for the actual file.
	* @param string $value The value to cache
	*/
	private function setCache(string $type, string $name, string $value) {
		$file = $this->getCacheFile($type, $name);
		file_put_contents($file, $value);
	}

	/**
	* Get data from file cache.
	*
	* @param  string $type Category of the item to be cached, also used as the subfolder for the actual file.
	* @param  string $name Name of the item to be cached, also used as name for the actual file.
	*
	* @return string       The cached value
	*/
	private function getCache(string $type, string $name, bool $validate_max_age = true): string {
		if ($validate_max_age && $this->getCacheTime($type, $name) <= strtotime('- ' . $this->cache_max_age . ' minutes')) {
			return '';
		}

		$file = $this->getCacheFile($type, $name);
		return file_exists($file) ? file_get_contents($file) : '';
	}

	/**
	* Get last modified time for cache
	*
	* @param  string $type Category of the item to be cached, also used as the subfolder for the actual file.
	* @param  string $name Name of the item to be cached, also used as name for the actual file.
	*
	* @return int          Cache modified time
	*/
	private function getCacheTime(string $type, string $name): int {
		$file = $this->getCacheFile($type, $name);
		return file_exists($file) ? filemtime($file) : 0;
	}

	/**
	* Get the cache filepath
	*
	* @param  string $type Category of the item to be cached, also used as the subfolder for the actual file.
	* @param  string $name Name of the item to be cached, also used as name for the actual file.
	*
	* @return string       The cache file path
	*/
	private function getCacheFile(string $type, string $name): string {
		$dir = self::FILE_CACHE . $type . '/';
		if (!file_exists($dir)) {
			mkdir($dir, 0755, true);
		}

		return $dir . $name . '.cache';
	}
}
