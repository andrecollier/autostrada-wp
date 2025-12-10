<?php

namespace Sircon\Library;

use Sircon\Library\Exceptions\APIResponseCodeException;

class API {

	private $base_url;

	private $curl_headers;

	private $postdata_encoding_function;

	protected $endpoint = '';

	protected $response_code = 0;

	protected $response_headers = [];

	protected $response_body = '';

	protected $follow_location = true;

	public function __construct(string $base_url, array $headers = ['Content-Type: application/json'], $postdata_encoding_function = 'json_encode') {
		$this->base_url = $base_url;
		$this->curl_headers = $headers;
		$this->postdata_encoding_function = $postdata_encoding_function;
	}

	public function getCurlHeaders(): array {
		return $this->curl_headers;
	}

	public function setCurlHeaders(array $headers): self {
		$this->curl_headers = $headers;

		return $this;
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

	public function throwErrorIfNotResponseCode(int ...$codes): self {
		if (!in_array($this->response_code, $codes)) {
			throw new APIResponseCodeException($this->response_code, $this->response_body, $this->response_headers);
		}
		return $this;
	}

	public function reset(): self {
		$this->endpoint = '';
		$this->response_code = 0;
		$this->response_headers = [];
		$this->response_body = '';

		return $this;
	}

	public function sendRequest(string $method, string $endpoint, array $data = []): self {
		$ch = curl_init();

		$this->reset();

		// Support both absolute and relative endpoints
		$this->endpoint = $endpoint;
		if (strpos($endpoint, $this->base_url) === 0) {
			$this->endpoint = preg_replace('/^' . preg_quote($this->base_url, '/') . '/', '', $endpoint);
		}

		switch ($method) {
			case 'GET':
				$this->endpoint .= $data ? '?' . http_build_query($data) : '';
				break;

			case 'POST':
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, call_user_func($this->postdata_encoding_function, $data));
				break;

			default:
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
				curl_setopt($ch, CURLOPT_POSTFIELDS, call_user_func($this->postdata_encoding_function, $data));
		}

		curl_setopt($ch, CURLOPT_URL, $this->base_url . $this->endpoint);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $this->follow_location);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->curl_headers);

		// This function is called by curl for each header received :: https://stackoverflow.com/a/41135574
		$headers = [];
		curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($ch, $header) use (&$headers) {
			$len = strlen($header);
			$header = explode(':', $header, 2);
			if (count($header) < 2) { // ignore invalid headers
				return $len;
			}

			$headers[strtolower(trim($header[0]))] = trim($header[1]);

			return $len;
		});

		$body = curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
		curl_close($ch);

		$this->response_code = $code;
		$this->response_headers = $headers;
		$this->response_body = $body;

		return $this;
	}

	public function get(string $endpoint, array $data = []): self {
		return $this->sendRequest('GET', $endpoint, $data);
	}

	public function put(string $endpoint, array $data = []): self {
		return $this->sendRequest('PUT', $endpoint, $data);
	}

	public function post(string $endpoint, array $data = []): self {
		return $this->sendRequest('POST', $endpoint, $data);
	}

	public function patch(string $endpoint, array $data = []): self {
		return $this->sendRequest('PATCH', $endpoint, $data);
	}

	public function delete(string $endpoint, array $data = []): self {
		return $this->sendRequest('DELETE', $endpoint, $data);
	}
}
