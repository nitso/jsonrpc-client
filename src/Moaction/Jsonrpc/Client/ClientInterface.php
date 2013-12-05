<?php

namespace Moaction\Jsonrpc\Client;

use Moaction\Jsonrpc\Common\Request;
use Moaction\Jsonrpc\Common\Response;

interface ClientInterface
{
	/**
	 * @param string $serverUrl JSONRPC server url
	 */
	public function __construct($serverUrl);

	/**
	 * @param \Moaction\Jsonrpc\Common\Request $request
	 * @return Response
	 */
	public function call(Request $request);

	/**
	 * @param Request[] $requests
	 * @return Response[]
	 */
	public function batch(array $requests);
} 