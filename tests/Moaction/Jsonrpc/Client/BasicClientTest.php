<?php

use Moaction\Jsonrpc\Client\ClientBasic;
use Moaction\Jsonrpc\Common\Response;
use Moaction\Jsonrpc\Common\Request;

class BasicClientTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers       Moaction\Jsonrpc\Client\ClientBasic::call
	 * @dataProvider providerTestCall
	 */
	public function testCall($request, $expected)
	{
		/** @var PHPUnit_Framework_MockObject_MockObject|ClientBasic $client */
		$client = $this->getClientMock(array('send', 'prepareResponse'));

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

		/** @see Moaction\Jsonrpc\ClientBasic::call */
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

	/**
	 * @param array $methods
	 * @return PHPUnit_Framework_MockObject_MockObject|ClientBasic
	 */
	public function getClientMock($methods)
	{
		$client = $this->getMockBuilder('\Moaction\Jsonrpc\Client\ClientBasic')
			->setMethods($methods)
			->disableOriginalConstructor()
			->getMock();

		return $client;
	}

	/**
	 * @covers       Moaction\Jsonrpc\Client\ClientBasic::prepareResponse
	 * @dataProvider providerTestPrepareResponse
	 */
	public function testPrepareResponse($data, $expected)
	{
		$clientMock = $this->getClientMock(array('getResponse'));
		$clientMock->expects($this->any())
			->method('getResponse')
			->will(
				$this->returnCallback(
					function ($arg) {
						$response = new Response();
						$response->setId($arg->id);

						return $response;
					}
				)
			);

		$class = new \ReflectionClass($clientMock);
		$method = $class->getMethod('prepareResponse');
		$method->setAccessible(true);

		if (!$expected) {
			$this->setExpectedException('\Moaction\Jsonrpc\Common\Exception');
		}

		/** @see Moaction\Jsonrpc\Client\ClientBasic::prepareResponse */
		$result = $method->invoke($clientMock, $data);

		$this->assertEquals($expected, $result);
	}

	/**
	 * @return array
	 */
	public function providerTestPrepareResponse()
	{
		$response1 = new Response();
		$response1->setId(1);

		$response2 = new Response();
		$response2->setId(4);

		return array(
			'Invalid json'    => array(
				'invalidJson{}',
				false,
			),
			'Single response' => array(
				'{"id": 1}',
				$response1,
			),
			'Multi response'  => array(
				'[{"id": 1}, {"id": 4}]',
				array(1 => $response1, 4 => $response2),
			),
		);
	}
} 