<?php

require_once __DIR__ . '/../Utility/Batch.php';

use PHPUnit\Framework\TestCase;
use Utility\Batch;

class BatchTest extends TestCase
{
	public function testAddCharCount(): void
	{
		$mockConfig = [[
			'expected' => ['j' => 3, 'h' => 3, 'e' => 1, 'y' => 3, 'f' => 1, 'n' => 1, '?' => 4, 'd' => 1, '*' => 2],
			'batches' => ['jhjheyfn', '?hyd', 'y?j**??']
		], [
			'expected' => ['?' => 4, 'h' => 1, 'y' => 2, '%' => 1, '.' => 2, 'd' => 4, 'j' => 1, '*' => 2],
			'batches' => ['?hy%..d', 'y?j*ddd*??']
		]];
		foreach ($mockConfig as $config) {
			$mockCharCount = [];
			foreach ($config['batches'] as $mockBatch) { Batch::AddCharCount($mockBatch, $mockCharCount); }
			$this->assertEquals($config['expected'], $mockCharCount);
		}
	}
}
