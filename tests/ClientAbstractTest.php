<?php
use Moaction\Jsonrpc\ClientAbstract;

/**
 * User: nitso@yandex.ru
 * Date: 03.12.13
 * Time: 11:43
 */

class ClientAbstractTest extends PHPUnit_Framework_TestCase {
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