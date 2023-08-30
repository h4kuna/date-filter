<?php declare(strict_types=1);

namespace h4kuna\DateFilter;

use h4kuna\DateFilter\Exceptions\InvalidArgumentException;
use h4kuna\Memoize\MemoryStorage;
use IntlDateFormatter;

class DatetimeFormatterFactory
{
	use MemoryStorage;

	private $localeMap = [];


	public function __construct(
		private array $formats,
	)
	{
	}


	public function addLocale(string $locale, string $alias): void
	{
		$this->localeMap[$alias] = $locale;
	}


	public function get(
		string $name,
		string $locale = '',
	): IntlDateFormatter
	{
		$strLocale = $this->resolveLocale($locale);

		return $this->memoize([$name, $strLocale], fn () => $this->create($name, $strLocale));
	}


	public function create(
		string $name,
		string $locale = '',
	): IntlDateFormatter
	{
		$locale = $this->resolveLocale($locale);

		if (!isset($this->formats[$name])) {
			throw new InvalidArgumentException(sprintf('Format name alias does not exists. "%s", available are "%s.', $name, implode('", "', array_keys($this->formats))));
		} elseif (isset($this->formats[$name]['dateType'], $this->formats[$name]['timeType']) === false) {
			throw new InvalidArgumentException('The keys "date" and "time" must exists.');
		}
		$date = $this->formats[$name]['dateType'];
		$time = $this->formats[$name]['timeType'];
		$pattern = $this->formats[$name]['pattern'][$locale] ?? '';

		return new IntlDateFormatter($locale, $date, $time, null, null, $pattern);
	}


	private function resolveLocale(string $locale): ?string
	{
		if ($locale === '') {
			$locale = setlocale(LC_TIME, '0');

			return $locale === false ? null : $locale;
		}

		return $this->localeMap[$locale] ?? $locale;
	}

}
