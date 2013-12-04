<?php
use Moaction\Jsonrpc\ClientAbstract;

class ClientAbstractTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers \Moaction\Jsonrpc\ClientAbstract::__construct
	 * @covers \Moaction\Jsonrpc\ClientAbstract::getServerUrl
	 */
	public function testConstruct()
	{
		/** @var PHPUnit_Framework_MockObject_MockObject|ClientAbstract $client */
		$client = $this->getMockBuilder('\Moaction\Jsonrpc\ClientAbstract')
			->setConstructorArgs(array('ServerUrl'))
			->getMockForAbstractClass();

		$this->assertEquals('ServerUrl', $client->getServerUrl());
	}
} 