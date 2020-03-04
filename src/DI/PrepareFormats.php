<?php declare(strict_types=1);

namespace h4kuna\DateFilter\DI;

class PrepareFormats
{

	/** @var array<string, string> */
	private $dateFormats;

	/** @var array */
	private $dayMonth;

	/** @var array */
	private $helperGroup;


	public function __construct(array $dateFormats, array $dayMonth)
	{
		$this->dateFormats = $dateFormats;
		$this->dayMonth = $dayMonth;
	}


	public function validDateFormats(): array
	{
		if ($this->helperGroup !== null) {
			return $this->dateFormats;
		}

		$this->helperGroup = [];
		$regexp = $this->getRegexpHelper();
		$formatsOrig = reset($this->dateFormats);
		foreach ($this->dateFormats as $group => $values) {
			if (!isset($this->helperGroup[$group])) {
				$this->helperGroup[$group] = [];
			}
			$formats = $formatsOrig;
			foreach ($values as $key => $format) {
				if (!isset($formats[$key])) {
					throw new \RuntimeException('This format "' . $format . '" in section "' . $group . '" is extra, let\'s delete it or must containt everywhere.');
				}
				unset($formats[$key]);
				$count = 0;
				$newFormat = self::prepareFormat($regexp, $format, $count);
				if ($count > 0) {
					$this->dateFormats[$group][$key] = $newFormat;
					$this->helperGroup[$group][$key] = true;
				}
			}
			if ($formats) {
				throw new \RuntimeException('These formats "' . implode(', ', $formats) . '" is extra, let\'s delete these or must containt everywhere.');
			}
		}

		return $this->dateFormats;
	}


	public function getHelperFormat(): array
	{
		$this->validDateFormats();
		return $this->helperGroup;
	}


	public function validDayMonth(): array
	{
		return $this->dayMonth;
	}


	private function getRegexpHelper(): string
	{
		if ($this->dayMonth === []) {
			return '';
		}
		$n = [];
		foreach ($this->dayMonth as $group => $data) {
			$n += $data;
		}

		$letters = implode('|', array_keys($n));
		return '/(?<!\\\\)(' . $letters . ')/';
	}


	private static function prepareFormat(string $regexp, string $format, int & $count): string
	{
		if ($regexp === '') {
			$count = 0;
			return '';
		}
		return preg_replace_callback($regexp, function ($found) {
			return '%\\' . $found[1] . '%';
		}, $format, -1, $count);
	}

}
