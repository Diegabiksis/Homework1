<?php

namespace Model;

use Exception\{ErrorException, MissingFlagException, ValueConflictException};
use Utility\Batch;

class ConfigManager
{
	private $flags = null;
	private $config = null;
	private $input = '';

	public function __construct(?array $flags)
	{
		// Used in PHPUnit tests because there is no trivial way to override getopt() inputs.
		$this->flags = $flags;

		try {
			$format = $this->GetValue('f', 'format');
			if (!$this->IsValidFormat($format)) { throw new ErrorException('', 3); }
		} catch (MissingFlagException $e) {
			throw new ErrorException('', 3, $e);
		} catch (ValueConflictException $e) {
			throw new ErrorException('', 7, $e);
		}

		$conditions = [];
		foreach (['L' => 'include-letter', 'P' => 'include-punctuation', 'S' => 'include-symbol'] as $short => $long) {
			try {
				// Should rewrite this, bad smell.
				$this->GetValue($short, $long);
				$conditions[] = $long;
			} catch (MissingFlagException $e) {
				// It is fine. Flag is not mandatory.
			}
		}
		if (empty($conditions)) { throw new ErrorException('', 4); }

		try {
			$input = $this->GetValue('i', 'input');
			if (!is_file($input) || !is_readable($input)) { throw new ErrorException('', 1); }
			// Array will be ordered ascending by time char first encountered in file.
			$charCount = [];
			$offset = 0;
			while ($batch = file_get_contents($input, false, null, $offset, Batch::DEFAULT_BATCH_SIZE)) {
				if (!$this->IsValidInput($batch)) { throw new ErrorException('', 2); }
				Batch::AddCharCount($batch, $charCount);
				$offset += Batch::DEFAULT_BATCH_SIZE;
			}
		} catch (MissingFlagException $e) {
			throw new ErrorException('', 1, $e);
		} catch (ValueConflictException $e) {
			throw new ErrorException('', 7, $e);
		}

		$this->config = new Config($format, $conditions, $charCount);
		$this->input = $input;
	}

	private function GetValue(string $short, string $long): string
	{
		$opts = $this->flags ?? $this->GetOptions();
		if (!isset($opts[$short]) && !isset($opts[$long])) { throw new MissingFlagException(); }
		if (isset($opts[$short]) && isset($opts[$long]) && $opts[$short] != $opts[$long]) { throw new ValueConflictException(); }
		return $opts[$short] ?? $opts[$long];
	}

	private function GetOptions(): array
	{
		static $opts = null;
		return (!is_null($opts) ? $opts : getopt('i:f:LPS', ['input:', 'format:', 'include-letter', 'include-punctuation', 'include-symbol']));
	}

	private function IsValidFormat(string $format): bool
	{
		return in_array($format, ['non-repeating', 'least-repeating', 'most-repeating']);
	}

	// Hopefully covers everything. Can't spend more time on this homework.
	private function IsValidInput(string $input): bool
	{
		return !preg_match('/[^\x00-\x7F]|[A-Z\s\d]/', $input);
	}

	public function GetConfig(): Config
	{
		return $this->config;
	}

	public function GetInput(): string
	{
		return $this->input;
	}
}
