<?php
use Moaction\Jsonrpc\Client\ClientAbstract;

class ClientAbstractTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers \Moaction\Jsonrpc\Client\ClientAbstract::__construct
	 * @covers \Moaction\Jsonrpc\Client\ClientAbstract::getServerUrl
	 */
	public function testConstruct()
	{
		/** @var PHPUnit_Framework_MockObject_MockObject|ClientAbstract $client */
		$client = $this->getMockBuilder('\Moaction\Jsonrpc\Client\ClientAbstract')
			->setConstructorArgs(array('ServerUrl'))
			->getMockForAbstractClass();

		$this->assertEquals('ServerUrl', $client->getServerUrl());
	}
} 