<?php

namespace View;

class ScriptView
{
	public function Output(string $input, string $format, array $conditions, array $data): void
	{
		echo 'File: ' . $input . PHP_EOL;
		$output = 'First ' . explode('-', $format)[0] . ' repeating ';
		foreach ($conditions as $condition) {
			echo $output . explode('-', $condition)[1] . ': ' . (isset($data[$condition]) ? $data[$condition]['char'] : 'None') . PHP_EOL;
		}
	}
}
