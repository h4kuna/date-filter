<?php

namespace h4kuna\DateFilter;

use Nette\Utils;

class DateTimeFormat
{

	/** @var array */
	private $formats;

	/** @var string */
	private $formatsActive;

	/** @var array */
	private $dayMonth = [];

	/** @var string */
	private $dayMonthActive;

	/** @var array */
	private $dayMonthHelper = [];

	public function __construct(array $formats)
	{
		if (!$formats) {
			throw new \InvalidArgumentException('$formats can not be empty.');
		}

		$this->formats = $formats;
		$this->setFormatsGroup(self::getFirstKey($formats));
	}

	public function setDayMonth(array $dayMonth, array $helper)
	{
		$this->dayMonth = $dayMonth;
		$this->dayMonthHelper = $helper;
		$this->setDayMonthGroup(self::getFirstKey($dayMonth));
	}

	/**
	 * @param string $group
	 * @return self
	 */
	public function setDayMonthGroup($group)
	{
		if (!isset($this->dayMonth[$group])) {
			$groups = implode(', ', array_keys($this->dayMonth));
			throw new \InvalidArgumentException('This group is not defined: "' . $group . '". Let\' choose one of ' . $groups . '.');
		}
		$this->dayMonthActive = $group;
		return $this;
	}

	/**
	 * Change group of filters
	 * @param string $group
	 * @return self
	 */
	public function setFormatsGroup($group)
	{
		if (!isset($this->formats[$group])) {
			$groups = implode(', ', array_keys($this->formats));
			throw new \InvalidArgumentException('This group is not defined: "' . $group . '". Let\' choose one of ' . $groups . '.');
		}

		$this->formatsActive = $group;
		return $this;
	}

	/**
	 * Get format by existing name
	 * @param string $name
	 * @return string
	 */
	public function getFormat($name)
	{
		if (!isset($this->formats[$this->formatsActive][$name])) {
			throw new \InvalidArgumentException('This format does not exists: "' . $name . '"');
		}
		return $this->formats[$this->formatsActive][$name];
	}

	/**
	 * @param string $name
	 * @param int|string|\DateTime $date
	 * @return string
	 */
	public function format($name, $date)
	{
		$dateObject = Utils\DateTime::from($date);
		$dateString = $dateObject->format($this->formats[$this->formatsActive][$name]);
		if (!isset($this->dayMonthHelper[$this->formatsActive][$name])) {
			return $dateString;
		}
		return preg_replace_callback('~(?<!\\\\)%([DlFM])%~', function($found) use ($dateObject) {
			$x = $dateObject->format($found[1]);
			if (isset($this->dayMonth[$this->dayMonthActive][$found[1]][$x])) {
				return $this->dayMonth[$this->dayMonthActive][$found[1]][$x];
			}
			return $x;
		}, $dateString);
	}

	private static function getFirstKey(array $array)
	{
		reset($array);
		return key($array);
	}

}
