<?php

use Moaction\Jsonrpc\ClientBasic;
use Moaction\Jsonrpc\Transport\Request;

class BasicClientTest extends PHPUnit_Framework_TestCase {
	/**
	 * @covers Moaction\Jsonrpc\ClientBasic::call
	 * @dataProvider providerTestCall
	 */
	public function testCall($request, $expected) {
		/** @var PHPUnit_Framework_MockObject_MockObject|ClientBasic $client */
		$client = $this->getMockBuilder('\Moaction\Jsonrpc\ClientBasic')
			->setMethods(array('send', 'prepareResponse'))
			->disableOriginalConstructor()
			->getMock();

		$data = 'testResult';
		$client->expects($this->any())
			->method('send')
			->with($expected)
			->will($this->returnValue($data));

		$response = 'testResponse';
		$client->expects($this->any())
			->method('prepareResponse')
			->with($data)
			->will($this->returnValue($response));

		if (!$expected) {
			$this->setExpectedException('\InvalidArgumentException');
		}

		$result = call_user_func_array(array($client, 'call'), $request);
		$this->assertEquals($response, $result);
	}

	/**
	 * @return array
	 */
	public function providerTestCall()
	{
		$request1 = new Request();
		$request1->setMethod('method1');
		$request1->setParams(array('param1' => 'value1'));
		$request1->setId(2);

		$request2 = new Request();
		$request2->setMethod('method2');

		return array(
			array(
				array($request2, new stdClass()),
				false
			),
			array(
				array($request1),
				'{"jsonrpc":"2.0","method":"method1","params":{"param1":"value1"},"id":2}'
			),
			array(
				array($request1, $request2),
				'[{"jsonrpc":"2.0","method":"method1","params":{"param1":"value1"},"id":2},{"jsonrpc":"2.0","method":"method2"}]'
			),
		);
	}
} 