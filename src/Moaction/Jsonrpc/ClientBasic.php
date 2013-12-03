<?php

namespace Moaction\Jsonrpc;

use Moaction\Jsonrpc\Transport\Request;
use Moaction\Jsonrpc\Transport\Response;

class ClientBasic extends ClientAbstract
{
	protected $request;

	/**
	 * @inheritdoc
	 */
	public function call(Request $request)
	{
		$args = func_get_args();
		if (count($args) > 1) {
			$data = array();

			foreach ($args as $arg) {
				if (!$arg instanceof Request) {
					throw new \InvalidArgumentException('Request argument must be of type \Moaction\Jsonrpc\Request');
				}

				$data[] = $arg->toArray();
			}

			$data = json_encode($data);
		} else {
			$data = json_encode($request->toArray());
		}

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
		if (!$data = json_decode($data)) {
			throw new Exception('Invalid server response.');
		}

		// handle batch response array
		if (is_array($data)) {
			$responses = array();
			foreach ($data as $r) {
				if ($response = $this->getResponse($r)) {
					$responses[$response->getId()] = $response;
				}
			}

			if (count($responses)) {
				return $responses;
			}
		}
		else {
			if ($response = $this->getResponse($data)) {
				return $response;
			}
		}
	}

	/**
	 * @param $data
	 * @return Response
	 */
	protected function getResponse($data)
	{
		return Response::fromArray($data);
	}
}