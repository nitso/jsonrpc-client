<?php
/**
 * User: nitso@yandex.ru
 * Date: 03.12.13
 * Time: 10:36
 */

use Moaction\Jsonrpc\Transport\Request;

class RequestTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers \Moaction\Jsonrpc\Transport\Request::toArray
	 * @dataProvider providerTestToArray
	 */
	public function testToArray(Request $request, $expected)
	{
		if (!$expected) {
			$this->setExpectedException('\InvalidArgumentException');
		}
		$this->assertEquals($expected, $request->toArray());
	}

	/**
	 * @return array
	 */
	public function providerTestToArray()
	{
		$emptyRequest = new Request();

		$someRequest = clone $emptyRequest;
		$someRequest->setMethod('testMethod');

		$fullRequest = clone $someRequest;
		$fullRequest->setId(10);
		$fullRequest->setParams(array('param' => 'value'));

		return array(
			'Missing method' => array($emptyRequest, false),
			'Some request' => array(
				$someRequest,
				array(
					'jsonrpc' => Request::VERSION,
					'method' => 'testMethod',
				),
			),
			'Full request' => array(
				$fullRequest,
				array(
					'jsonrpc' => Request::VERSION,
					'method' => 'testMethod',
					'id' => 10,
					'params' => array('param' => 'value'),
				),
			)
		);
	}

	/**
	 * Dummy version test
	 */
	public function testVersion()
	{
		$this->assertEquals('2.0', Request::VERSION);
	}

	/**
	 * @covers \Moaction\Jsonrpc\Transport\Request::setId
	 * @covers \Moaction\Jsonrpc\Transport\Request::setParams
	 * @covers \Moaction\Jsonrpc\Transport\Request::setMethod
	 * @covers \Moaction\Jsonrpc\Transport\Request::getId
	 * @covers \Moaction\Jsonrpc\Transport\Request::getParams
	 * @covers \Moaction\Jsonrpc\Transport\Request::getMethod
	 */
	public function testGettersSetters()
	{
		$request = new Request();
		$request->setId(10);
		$request->setParams(array('params'));
		$request->setMethod('method');

		$this->assertEquals(10, $request->getId());
		$this->assertEquals(array('params'), $request->getParams());
		$this->assertEquals('method', $request->getMethod());
	}
}