<?php

require_once __DIR__ . '/../Controller/ScriptController.php';

use PHPUnit\Framework\TestCase;
use Controller\ScriptController;

class ScriptControllerTest extends TestCase
{
	public function testExecute(): void
	{
		$mockConfig = [[
			'expected' => 'File: ./Test/TestInput' . PHP_EOL . 'First most repeating punctuation: ?' . PHP_EOL . 'First most repeating symbol: *' . PHP_EOL,
			'data' => 'jhjheyfn?hydy?j**??',
			'flags' => ['input' => './Test/TestInput', 'format' => 'most-repeating', 'include-symbol' => false, 'include-punctuation' => false]
		], [
			'expected' => 'File: ./Test/TestInput' . PHP_EOL . 'First non repeating letter: None' . PHP_EOL,
			'data' => 'a??ff^$*agg&**??',
			'flags' => ['input' => './Test/TestInput', 'format' => 'non-repeating', 'include-letter' => false]
		]];
		$expected = '';
		$scriptController = new ScriptController();
		foreach ($mockConfig as $config) {
			$expected .= $config['expected'];
			file_put_contents($config['flags']['input'], $config['data']);
			$scriptController->Execute($config['flags']);
		}
		$this->expectOutputString($expected);
	}
}
