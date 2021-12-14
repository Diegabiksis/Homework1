<?php

namespace Utility;

class Batch
{
	const DEFAULT_BATCH_SIZE = 1000000;

	public static function AddCharCount(string $batch, array &$charCount): void
	{
		$length = strlen($batch);
		for ($i = 0; $i < $length; $i++) {
			$char = $batch[$i];
			$charCount[$char] = (isset($charCount[$char]) ? ++$charCount[$char] : 1);
		}
	}
}
