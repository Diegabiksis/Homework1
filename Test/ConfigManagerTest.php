<?php

spl_autoload_register(function($class) {
	require_once $class . '.php';
});

use PHPUnit\Framework\TestCase;
use Exception\{ErrorException, MissingFlagException, ValueConflictException};
use Model\ConfigManager;
use Test\MockObject;

class ConfigManagerTest extends TestCase
{
	private $mockObject = null;
	private $mockFlags = null;

	private function Prepare(array $mockFlags = ['i' => './Test/TestInput', 'f' => 'non-repeating', 'L' => false]): void
	{
		if (is_null($this->mockObject) || $this->mockFlags != $mockFlags) {
			file_put_contents($mockFlags['i'] ?? $mockFlags['input'], '');
			$this->mockObject = (new MockObject(new ConfigManager($mockFlags)));
			$this->mockFlags = $mockFlags;
		}
	}

	public function testIsValidInput(): void
	{
		$this->Prepare();
		$mockInputs = ['a?#' => true, 'a7f' => false, '.A?' => false, '' => true];
		foreach ($mockInputs as $input => $valid) {
			$this->assertSame($valid, $this->mockObject->ExecutePrivateMethod('IsValidInput', [$input]));
		}
	}

	public function testIsValidFormat(): void
	{
		$this->Prepare();
		$mockFormats = ['non-repeating' => true, 'this will fail' => false, 'most-repeating' => true, 'random' => false];
		foreach ($mockFormats as $format => $valid) {
			$this->assertSame($valid, $this->mockObject->ExecutePrivateMethod('IsValidFormat', [$format]));
		}
	}

	public function testGetValueNoFlag(): void
	{
		$this->Prepare();
		$this->expectException(MissingFlagException::class);
		$this->mockObject->ExecutePrivateMethod('GetValue', ['S', 'include-symbol']);
	}

	public function testGetValueConflict(): void
	{
		$this->Prepare();
		$this->mockObject->SetPrivateProperty('flags', ['f' => 'non-repeating', 'format' => 'least-repeating']);
		$this->expectException(ValueConflictException::class);
		$this->mockObject->ExecutePrivateMethod('GetValue', ['f', 'format']);
	}

	public function testGetValueSuccessful(): void
	{
		$this->Prepare();
		$this->assertSame('', $this->mockObject->ExecutePrivateMethod('GetValue', ['L', 'include-letter']));
		$this->mockObject->SetPrivateProperty('flags', ['i' => './Test/TestInput', 'input' => './Test/TestInput']);
		$this->assertSame('./Test/TestInput', $this->mockObject->ExecutePrivateMethod('GetValue', ['i', 'input']));
	}

	public function testConstruct(): void
	{
		try {
			new ConfigManager([]);
		} catch (ErrorException $e) {
			$this->assertSame(true, ($e->getCode() == 3) && ($e->getPrevious()) instanceof MissingFlagException);
		}

		try {
			new ConfigManager(['f' => 'non-repeating', 'format' => 'least-repeating']);
		} catch (ErrorException $e) {
			$this->assertSame(true, ($e->getCode() == 7) && ($e->getPrevious()) instanceof ValueConflictException);
		}

		try {
			new ConfigManager(['f' => 'non-existing']);
		} catch (ErrorException $e) {
			$this->assertSame(true, ($e->getCode() == 3) && is_null($e->getPrevious()));
		}

		try {
			new ConfigManager(['f' => 'non-repeating']);
		} catch (ErrorException $e) {
			$this->assertSame(true, ($e->getCode() == 4) && is_null($e->getPrevious()));
		}

		try {
			new ConfigManager(['f' => 'non-repeating', 'L' => 'include-letter']);
		} catch (ErrorException $e) {
			$this->assertSame(true, ($e->getCode() == 1) && ($e->getPrevious()) instanceof MissingFlagException);
		}

		try {
			new ConfigManager(['f' => 'non-repeating', 'L' => 'include-letter', 'i' => 'file1', 'input' => 'file2']);
		} catch (ErrorException $e) {
			$this->assertSame(true, ($e->getCode() == 7) && ($e->getPrevious()) instanceof ValueConflictException);
		}

		try {
			new ConfigManager(['f' => 'non-repeating', 'L' => 'include-letter', 'i' => 'file1']);
		} catch (ErrorException $e) {
			$this->assertSame(true, ($e->getCode() == 1) && is_null($e->getPrevious()));
		}

		try {
			$mockInput = './Test/TestInput';
			file_put_contents($mockInput, 'Invalid');
			new ConfigManager(['f' => 'non-repeating', 'L' => 'include-letter', 'i' => $mockInput]);
		} catch (ErrorException $e) {
			$this->assertSame(true, ($e->getCode() == 2) && is_null($e->getPrevious()));
		}

		file_put_contents($mockInput, 'very_valid??!');
		$config = (new ConfigManager(['f' => 'least-repeating', 'L' => 'include-letter', 'i' => $mockInput]))->GetConfig();
		$this->assertEquals(['include-letter' => ['char' => 'e', 'count' => 1]], $config->GetResult());
	}
}
