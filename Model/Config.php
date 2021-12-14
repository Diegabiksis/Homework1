<?php

namespace Model;

class Config
{
	private $format = '';
	private $conditions = [];
	private $charCount = [];

	public function __construct(string $format, array $conditions, array $charCount)
	{
		$this->format = $format;
		$this->conditions = $conditions;
		$this->charCount = $charCount;
	}

	public function GetResult(): array
	{
		$nonRepeat = ($this->format == 'non-repeating');
		$condCount = count($this->conditions);

		$result = [];
		$callback = $this->GetIterCallbackByFormat($result);
		foreach ($this->charCount as $char => $count) {
			$condition = $this->GetConditionByChar($char);
			if (!in_array($condition, $this->conditions)) { continue; }
			if ($callback($condition, $count)) {
				$result[$condition] = [
					'char' => $char,
					'count' => $count
				];
			}
			if ($nonRepeat && $condCount == count($result)) { break; }
		}
		return $result;
	}

	// Probably overkill.
	private function GetIterCallbackByFormat(array &$result): callable
	{
		switch ($this->format) {
			case 'non-repeating':
				return function(string $condition, int $count) use (&$result): bool {
					return (!isset($result[$condition]) && $count == 1);
				};
			case 'least-repeating':
				return function(string $condition, int $count) use (&$result): bool {
					return (!isset($result[$condition]) || $count < $result[$condition]['count']);
				};
			case 'most-repeating':
				return function(string $condition, int $count) use (&$result): bool {
					return (!isset($result[$condition]) || $count > $result[$condition]['count']);
				};
		}
	}

	private function GetConditionByChar(string $char): string
	{
		return (ctype_lower($char) ? 'include-letter' : (preg_match('/[.,:;?!()[\]\'"\/-]/', $char) ? 'include-punctuation' : 'include-symbol'));
	}

	public function GetConditions(): array
	{
		return $this->conditions;
	}

	public function GetFormat(): string
	{
		return $this->format;
	}
}
