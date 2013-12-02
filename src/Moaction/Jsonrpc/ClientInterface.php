<?php

namespace Moaction\Jsonrpc;

use Moaction\Jsonrpc\Transport\Request;

interface ClientInterface
{
	/**
	 * @param string $serverUrl JSONRPC server url
	 */
	public function __construct($serverUrl);

	/**
	 * @param \Moaction\Jsonrpc\Transport\Request $request
	 * @return Response
	 */
	public function call(Request $request);
} 