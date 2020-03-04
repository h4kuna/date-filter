<?php declare(strict_types=1);

namespace h4kuna\DateFilter;

use h4kuna\DateFilter\Exceptions\InvalidArgumentException;
use Nette\Utils;

/**
 * $format is array like
 * [
 * 	'cs_CZ' => [
 * 	  'date' => 'j. n. Y.'
 * 	  'dayMonth' => 'j. n.'
 * 	  'day' => 'D'
 * 	]
 * ]
 */
final class DateTimeFormat
{
	/** @var array<string, array<string, string>> */
	private $formats;

	/** @var string */
	private $activeGroup;

	/** @var array */
	private $dayMonth = [];

	/** @var string */
	private $dayMonthActive;

	/** @var array */
	private $dayMonthHelper = [];


	public function __construct(array $formats)
	{
		if ($formats === []) {
			throw new InvalidArgumentException('$formats can not be empty.');
		}

		$this->formats = $formats;
		$this->setFormatsGroup((string) array_key_first($formats));
	}


	public function setDayMonth(array $dayMonth, array $helper): void
	{
		$this->dayMonth = $dayMonth;
		$this->dayMonthHelper = $helper;
		$this->setDayMonthGroup((string) array_key_first($dayMonth));
	}


	public function setDayMonthGroup(string $group): self
	{
		if (!isset($this->dayMonth[$group])) {
			$groups = implode(', ', array_keys($this->dayMonth));
			throw new \InvalidArgumentException('This group is not defined: "' . $group . '". Let\' choose one of ' . $groups . '.');
		}
		$this->dayMonthActive = $group;
		return $this;
	}


	public function setFormatsGroup(string $group): self
	{
		if (!isset($this->formats[$group])) {
			$groups = implode(', ', array_keys($this->formats));
			throw new \InvalidArgumentException('This group is not defined: "' . $group . '". Let\' choose one of ' . $groups . '.');
		}

		$this->activeGroup = $group;
		return $this;
	}


	/**
	 * Get format by existing name
	 */
	public function getFormat(string $name): string
	{
		if (!isset($this->formats[$this->activeGroup][$name])) {
			throw new \InvalidArgumentException('This format does not exists: "' . $name . '"');
		}
		return $this->formats[$this->activeGroup][$name];
	}


	/**
	 * @param int|string|\DateTimeInterface $date
	 */
	public function format(string $name, $date): string
	{
		$dateObject = Utils\DateTime::from($date);
		$dateString = $dateObject->format($this->formats[$this->activeGroup][$name]);
		if (!isset($this->dayMonthHelper[$this->activeGroup][$name])) {
			return $dateString;
		}
		return (string) preg_replace_callback('~(?<!\\\\)%([DlFM])%~', function ($found) use ($dateObject) {
			$x = $dateObject->format($found[1]);
			if (isset($this->dayMonth[$this->dayMonthActive][$found[1]][$x])) {
				return $this->dayMonth[$this->dayMonthActive][$found[1]][$x];
			}
			return $x;
		}, $dateString);
	}

}
