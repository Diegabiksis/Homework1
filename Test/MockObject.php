<?php

namespace Test;

// Result of fooling around.
class MockObject
{
	private $object = null;
	private $reflection = null;
	private $properties = [];
	private $methods = [];

	public function __construct(object $object)
	{
		$this->object = $object;
		$this->reflection = new \ReflectionClass($object);
	}

	/**
	 * @return might-be-anything
	 */
	public function ExecutePrivateMethod(string $methodName, array $methodArgs)
	{
		$method = null;
		if (isset($this->methods[$methodName])) {
			$method = $this->methods[$methodName];
		} else {
			$method = ($this->reflection)->getMethod($methodName);
			$method->setAccessible(true);
			$this->methods[$methodName] = $method;
		}
		return $method->invokeArgs($this->object, $methodArgs);
	}

	/**
	 * @param might-be-anything $propertyValue
	 */
	public function SetPrivateProperty(string $propertyName, $propertyValue): void
	{
		$property = null;
		if (isset($this->properties[$propertyName])) {
			$property = $this->properties[$propertyName];
		} else {
			$property = ($this->reflection)->getProperty($propertyName);
			$property->setAccessible(true);
			$this->properties[$propertyName] = $property;
		}
		$property->setValue($this->object, $propertyValue);
	}
}
