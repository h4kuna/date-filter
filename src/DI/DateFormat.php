<?php declare(strict_types=1);

namespace h4kuna\DateFilter\DI;

use IntlDateFormatter;

final class DateFormat
{
	/**
	 * @see https://unicode-org.github.io/icu/userguide/format_parse/datetime/
	 * locale => pattern
	 * cs_CZ => 'DD.MM.'
	 * @var array<string, string>
	 */
	public array $pattern = [];

	public int $date = IntlDateFormatter::FULL;

	public int $time = IntlDateFormatter::FULL;

}
