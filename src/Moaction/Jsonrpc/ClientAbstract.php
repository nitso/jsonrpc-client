<?php

namespace Moaction\Jsonrpc;

abstract class ClientAbstract implements ClientInterface
{
	/**
	 * @var string
	 */
	private $serverUrl;

	/**
	 * @param string $serverUrl JSONRPC server url
	 */
	public function __construct($serverUrl)
	{
		$this->serverUrl = $serverUrl;
	}

	/**
	 * @return string
	 */
	public function getServerUrl()
	{
		return $this->serverUrl;
	}
}