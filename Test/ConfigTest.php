<?php

require_once __DIR__ . '/MockObject.php';
require_once __DIR__ . '/../Model/Config.php';

use PHPUnit\Framework\TestCase;
use Test\MockObject;
use Model\Config;

class ConfigTest extends TestCase
{
	private $mockObject = null;

	private function Prepare(): void
	{
		if (is_null($this->mockObject)) { $this->mockObject = (new MockObject(new Config('', [], []))); }
	}

	public function testGetConditionByChar(): void
	{
		$this->Prepare();
		$mockConfig = [
			'l' => 'include-letter',
			'n' => 'include-letter',
			'.' => 'include-punctuation',
			'?' => 'include-punctuation',
			'%' => 'include-symbol',
			'*' => 'include-symbol'
		];
		foreach ($mockConfig as $mockChar => $mockCondidion) {
			$this->assertSame($mockCondidion, $this->mockObject->ExecutePrivateMethod('GetConditionByChar', [$mockChar]));
		}
	}

	public function testGetIterCallbackByFormat(): void
	{
		$this->Prepare();
		$mockResult = [
			'include-letter' => [
				'char' => 'z',
				'count' => rand(2, 99)
			]
		];
		foreach (['least-repeating', 'most-repeating', 'non-repeating'] as $format) {
			$this->mockObject->SetPrivateProperty('format', $format);
			$callback = $this->mockObject->ExecutePrivateMethod('GetIterCallbackByFormat', [&$mockResult]);
			$this->assertIsCallable($callback);
			if ($format != 'non-repeating') {
				$mostRepeat = ($format == 'most-repeating');
				$mockCount = $mockResult['include-letter']['count'];
				$this->assertSame(!$mostRepeat, $callback('include-letter', $mockCount - 1));
				$this->assertSame($mostRepeat, $callback('include-letter', $mockCount + 1));
				$this->assertSame(false, $callback('include-letter', $mockCount));
				$this->assertSame(true, $callback('include-punctuation', $mockCount + 1));
				$this->assertSame(true, $callback('include-symbol', $mockCount - 1));
			} else {
				$mockResult['include-letter']['count'] = 1;
				$this->assertSame(false, $callback('include-letter', 1));
				$this->assertSame(false, $callback('include-letter', 2));
				$this->assertSame(true, $callback('include-punctuation', 1));
				$this->assertSame(false, $callback('include-symbol', 2));
			}
		}
	}

	public function testGetResult(): void
	{
		$mockConfig = [[
			'format' => 'non-repeating',
			'conditions' => ['include-letter', 'include-punctuation'],
			'charCount' => ['j' => 3, 'h' => 3, 'e' => 1, 'y' => 3, 'f' => 1, 'n' => 1, '?' => 4, 'd' => 1, '*' => 2],
			'expected' => ['include-letter' => ['char' => 'e', 'count' => 1]]
		], [
			'format' => 'least-repeating',
			'conditions' => ['include-symbol'],
			'charCount' => ['%' => 9, '@' => 3, '#' => 5, '?' => 4, 'd' => 7, '*' => 11],
			'expected' => ['include-symbol' => ['char' => '@', 'count' => 3]]
		]];
		foreach ($mockConfig as $config) {
			$this->assertEquals($config['expected'], (new Config($config['format'], $config['conditions'], $config['charCount']))->GetResult());
		}
	}
}
