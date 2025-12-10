<?php

namespace Sircon\Library\Exceptions;

use Exception;

class APIResponseCodeException extends Exception {

	protected $response_code = 0;

	protected $response_headers = [];

	protected $response_body = '';

	public function __construct(int $response_code, string $response_body, array $response_headers) {
		$this->response_code = $response_code;
		$this->response_body = $response_body;
		$this->response_headers = $response_headers;
		/* translators: %d will be replaced by the HTTP response code */
		parent::__construct(sprintf(__('API Error: Unexpected response code %d', 'sircon-library'), $response_code), $response_code);
	}

	public function getResponseCode(): int {
		return $this->response_code;
	}

	public function getResponseHeaders(): array {
		return $this->response_headers;
	}

	public function getResponseHeader(string $header): string {
		return $this->response_headers[strtolower(trim($header))] ?? '';
	}

	public function getResponseBody(): string {
		return $this->response_body;
	}

	public function getResponseJson(bool $associative = false) {
		return json_decode($this->response_body, $associative);
	}
}
