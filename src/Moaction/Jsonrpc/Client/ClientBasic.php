<?php

namespace Moaction\Jsonrpc\Client;

use Moaction\Jsonrpc\Common\Exception;
use Moaction\Jsonrpc\Common\Request;
use Moaction\Jsonrpc\Common\Response;

class ClientBasic extends ClientAbstract
{
	/**
	 * @var bool
	 */
	private $returnArrayResponse;

	/**
	 * @inheritdoc
	 * @param bool $returnArrayResponse second flag while json_decoding response
	 */
	public function __construct($serverUrl, $returnArrayResponse = true)
	{
		$this->returnArrayResponse = $returnArrayResponse;
		parent::__construct($serverUrl);
	}

	/**
	 * @inheritdoc
	 */
	public function call(Request $request)
	{
		$data = json_encode($request->toArray());
		$result = $this->send($data);
		return $this->prepareResponse($result);
	}

	/**
	 * Send low level data by http
	 *
	 * @param string $content
	 * @return string
	 * @throws Exception
	 */
	protected function send($content)
	{
		$streamOptions = array(
			'http' => array(
				'method'  => 'POST',
				'header'  => 'Content-Type: application/json',
				'content' => $content,
			)
		);

		$context = stream_context_create($streamOptions);
		$result = @file_get_contents($this->getServerUrl(), false, $context);

		if ($result === false) {
			throw new Exception('Unable to connect to server');
		}

		return $result;
	}

	/**
	 * @param $data
	 * @throws Exception
	 * @return Response|Response[]
	 * @link http://ya.ru
	 */
	protected function prepareResponse($data)
	{
		if (!$data = json_decode($data, $this->returnArrayResponse)) {
			throw new Exception('Invalid server response. Json decoding error.');
		}
		return $this->getResponse($data);
	}

	/**
	 * @param $data
	 * @return Response
	 */
	protected function getResponse($data)
	{
		return Response::fromArray((array)$data);
	}

	/**
	 * @inheritdoc
	 */
	public function batch(array $requests)
	{
		if (!$requests) {
			throw new \InvalidArgumentException('Empty request');
		}

		$data = array();

		foreach ($requests as $request) {
			if (!$request instanceof Request) {
				throw new \InvalidArgumentException('Invalid request params. Array of `Request` objects expected');
			}

			$data[] = $request->toArray();
		}

		$result = $this->send(json_encode($data, $this->returnArrayResponse));
		return $this->prepareBatchResponse($result);
	}

	/**
	 * @param string $data
	 * @return Response[]
	 * @throws Exception
	 */
	protected function prepareBatchResponse($data)
	{
		if (!$results = json_decode($data, $this->returnArrayResponse)) {
			throw new Exception('Invalid server response. Json decoding error.');
		}

		if (!is_array($results)) {
			throw new Exception('Invalid server response. Array expected.');
		}

		$responses = array();
		foreach ($results as $result) {
			$response = $this->getResponse($result);
			$responses[$response->getId()] = $response;
		}

		return $responses;
	}
}