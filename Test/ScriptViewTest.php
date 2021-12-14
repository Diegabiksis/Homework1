<?php

require_once __DIR__ . '/../View/ScriptView.php';

use PHPUnit\Framework\TestCase;
use View\ScriptView;

class ScriptViewTest extends TestCase
{
	public function testOutput(): void
	{
		$mockConfig = [[
			'expected' => 'File: ../test.txt' . PHP_EOL . 'First least repeating letter: d' . PHP_EOL . 'First least repeating symbol: None' . PHP_EOL,
			'input' => '../test.txt',
			'format' => 'least-repeating',
			'conditions' => ['include-letter', 'include-symbol'],
			'data' => ['include-letter' => ['char' => 'd', 'count' => rand(1, 99)]]
		], [
			'expected' => 'File: test2.txt' . PHP_EOL . 'First most repeating punctuation: !' . PHP_EOL,
			'input' => 'test2.txt',
			'format' => 'most-repeating',
			'conditions' => ['include-punctuation'],
			'data' => ['include-punctuation' => ['char' => '!', 'count' => rand(1, 99)]]
		]];
		$expected = '';
		$scriptView = new ScriptView;
		foreach ($mockConfig as $config) {
			$expected .= $config['expected'];
			$scriptView->Output($config['input'], $config['format'], $config['conditions'], $config['data']);
		}
		$this->expectOutputString($expected);
	}
}
