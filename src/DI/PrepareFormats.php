<?php

namespace h4kuna\DateFilter\DI;

class PrepareFormats
{

	/** @var array */
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


	public function validDateFormats()
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


	public function getHelperFormat()
	{
		$this->validDateFormats();
		return $this->helperGroup;
	}


	public function validDayMonth()
	{
		return $this->dayMonth;
	}


	private function getRegexpHelper()
	{
		if (!$this->dayMonth) {
			return null;
		}
		$n = [];
		foreach ($this->dayMonth as $group => $data) {
			$n += $data;
		}

		$letters = implode('|', array_keys($n));
		return '/(?<!\\\\)(' . $letters . ')/';
	}


	private static function prepareFormat($regexp, $format, & $count)
	{
		if ($regexp === null) {
			$count = 0;
			return '';
		}
		return preg_replace_callback($regexp, function ($found) {
			return '%\\' . $found[1] . '%';
		}, $format, -1, $count);
	}

}
