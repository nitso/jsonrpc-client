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

		$result = $client->call($request);
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

		$request3 = new Request();

		return array(
			'Empty request' => array(
				$request3,
				false
			),
			'Notification' => array(
				$request2,
				'{"jsonrpc":"2.0","method":"method2"}'
			),
			'Valid request' => array(
				$request1,
				'{"jsonrpc":"2.0","method":"method1","params":{"param1":"value1"},"id":2}'
			),
		);
	}

	/**
	 * @param array $methods
	 * @param array|bool $constructorArgs
	 * @return PHPUnit_Framework_MockObject_MockObject|ClientBasic
	 */
	public function getClientMock($methods, $constructorArgs = false)
	{
		$client = $this->getMockBuilder('\Moaction\Jsonrpc\Client\ClientBasic')
			->setMethods($methods);

		if ($constructorArgs === false) {
			$client->disableOriginalConstructor();
		}
		elseif ($constructorArgs) {
			$client->setConstructorArgs($constructorArgs);
		}

		return $client->getMock();
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
		);
	}

	/**
	 * @param bool $flag
	 * @param mixed $expected
	 * @dataProvider providerTestArrayResponse
	 * @covers \Moaction\Jsonrpc\Client\ClientBasic::__construct
	 * @covers \Moaction\Jsonrpc\Client\ClientBasic::prepareResponse
	 */
	public function testResponseType($flag, $expected)
	{
		$clientMock = $this->getClientMock(array('getResponse'), array('url', $flag));
		$clientMock->expects($this->any())
			->method('getResponse')
			->will($this->returnArgument(0));

		$class = new \ReflectionClass($clientMock);
		$method = $class->getMethod('prepareResponse');
		$method->setAccessible(true);

		/** @see Moaction\Jsonrpc\Client\ClientBasic::prepareResponse */
		$result = $method->invoke($clientMock, '{"id": 1, "data": {"field": "value"}}');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @return array
	 */
	public function providerTestArrayResponse()
	{
		$array = array(
			'id'   => 1,
			'data' =>
				array(
					'field' => 'value',
				),
		);

		$object = new stdClass();
		$object->id = 1;

		$data = new stdClass();
		$data->field = 'value';
		$object->data = $data;

		return array(
			'Array response' => array(true, $array),
			'Object response' => array(false, $object),
		);
	}

	/**
	 * @param array $requests
	 * @param bool $expected
	 * @dataProvider providerTestBatch
	 * @covers \Moaction\Jsonrpc\Client\ClientBasic::batch
	 */
	public function testBatch($requests, $expected)
	{
		$client = $this->getClientMock(array('prepareBatchResponse', 'send'));
		$client->expects($this->any())
			->method('send')
			->will($this->returnValue('Send result'));

		$client->expects($this->any())
			->method('prepareBatchResponse')
			->with('Send result')
			->will($this->returnValue('result'));

		if (!$expected) {
			$this->setExpectedException('\InvalidArgumentException');
		}

		$result = $client->batch($requests);
		$this->assertEquals('result', $result);
	}

	/**
	 * @return array
	 */
	public function providerTestBatch()
	{
		$request = new Request();
		$request->setMethod('testMethod');

		return array(
			'Empty request'   => array(array(), false),
			'Invalid request' => array(array($request, '123'), false),
			'Valid request'   => array(array($request), true),
		);
	}

	/**
	 * @param string $data
	 * @param mixed $expected
	 * @dataProvider providerTestPrepareBatchResponse
	 */
	public function testPrepareBatchResponse($data, $expected)
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
		$method = $class->getMethod('prepareBatchResponse');
		$method->setAccessible(true);

		if (!$expected) {
			$this->setExpectedException('\Moaction\Jsonrpc\Common\Exception');
		}

		/** @see Moaction\Jsonrpc\Client\ClientBasic::prepareBatchResponse */
		$result = $method->invoke($clientMock, $data);
		$this->assertEquals($expected, $result);
	}

	public function providerTestPrepareBatchResponse()
	{
		$response1 = new Response();
		$response1->setId(4);
		$response2 = new Response();
		$response2->setId(7);

		$expected = array(
			4 => $response1,
			7 => $response2,
		);

		return array(
			'Invalid json'        => array('{}invalid', false),
			'Unexpected response' => array('{"id":1}', false),
			'Multi response'      => array('[{"id": 4}, {"id": 7}]', $expected),
		);
	}
}