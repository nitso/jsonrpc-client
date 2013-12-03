<?php
/**
 * User: nitso@yandex.ru
 * Date: 03.12.13
 * Time: 10:30
 */

use Moaction\Jsonrpc\Transport\Error;

class ErrorTest extends PHPUnit_Framework_TestCase {
	/**
	 * @covers \Moaction\Jsonrpc\Transport\Error::__construct
	 * @covers \Moaction\Jsonrpc\Transport\Error::getCode
	 * @covers \Moaction\Jsonrpc\Transport\Error::getMessage
	 * @covers \Moaction\Jsonrpc\Transport\Error::getData
	 */
	public function testConstruct()
	{
		$error = new Error('10', 'Error message', array('data'));
		$this->assertEquals(10, $error->getCode());
		$this->assertEquals('Error message', $error->getMessage());
		$this->assertEquals(array('data'), $error->getData());
	}

	/**
	 * @covers \Moaction\Jsonrpc\Transport\Error::fromArray
	 */
	public function testFromArray()
	{
		$error = Error::fromArray(array(
			'code' => 33,
			'message' => 'Fake error',
			'data' => array('id' => 16),
		));

		$this->assertEquals(33, $error->getCode());
		$this->assertEquals('Fake error', $error->getMessage());
		$this->assertEquals(array('id' => 16), $error->getData());
	}

	/**
	 * @param $code
	 * @param $expected
	 * @dataProvider providerTestIntErrorCode
	 * @covers \Moaction\Jsonrpc\Transport\Error::fromArray
	 */
	public function testIntErrorCode($code, $expected)
	{
		$error = Error::fromArray(array(
			'code' => $code,
		));
		$this->assertEquals($expected, $error->getCode());
	}

	/**
	 * @return array
	 */
	public function providerTestIntErrorCode()
	{
		return array(
			array('a', '0'),
			array('1s', '1'),
			array('55', 55),
		);
	}
}