<?php

namespace Moaction\Jsonrpc;

class Response {
	/**
	 * @var mixed
	 */
	private $result;

	/**
	 * @var Error
	 */
	private $error;

	/**
	 * @var string
	 */
	private $id;

	/**
	 * @param mixed $error
	 * @return $this
	 */
	public function setError(Error $error)
	{
		$this->error = $error;
		return $this;
	}

	/**
	 * @return Error
	 */
	public function getError()
	{
		return $this->error;
	}

	/**
	 * @param mixed $id
	 * @return $this
	 */
	public function setId($id)
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param mixed $result
	 * @return $this
	 */
	public function setResult($result)
	{
		$this->result = $result;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getResult()
	{
		return $this->result;
	}

	/**
	 * @param array $data
	 * @return self
	 */
	public static function fromArray($data)
	{

	}
}